<?php

namespace Painel\Core;

use Exception;

class Crypto
{
    private const METHOD = 'aes-256-cbc';

    /**
     * Encripta um texto (Ex: E-mail, Telefone, CPF) para gravar ofuscado no Banco do BD.
     * Retorna a string em Base64 contendo o IV acoplado para não perdermos a chave vetor.
     */
    public static function encrypt(string $plainText): string
    {
        $key = self::getKey();
        
        // Gera um Initialization Vector (IV) aleatório e matematicamente forte para o tamanho do cipher
        $ivLength = openssl_cipher_iv_length(self::METHOD);
        $iv = openssl_random_pseudo_bytes($ivLength);

        // Encripta
        $encrypted = openssl_encrypt($plainText, self::METHOD, $key, OPENSSL_RAW_DATA, $iv);

        if ($encrypted === false) {
            throw new Exception("Falha Crítica ao tentar cifrar o dado PII.");
        }

        // Acopla o IV junto da Hash Encriptada para conseguirmos decifrar depois
        $payload = $iv . $encrypted;

        // Base64 para ficar bonitinho/varchar-friendly no MariaDB
        return base64_encode($payload);
    }

    /**
     * Desencripta um dado ofuscado oriundo do Banco de Dados para tela limpa do Webmaster
     */
    public static function decrypt(string $base64Payload): ?string
    {
        $key = self::getKey();
        $payload = base64_decode($base64Payload);
        
        if ($payload === false) {
            return null;
        }

        $ivLength = openssl_cipher_iv_length(self::METHOD);
        
        // Separa o IV das engrenagens da String (Os primeiros X bytes) e a string cifrada
        $iv = substr($payload, 0, $ivLength);
        $encrypted = substr($payload, $ivLength);

        if (strlen($iv) !== $ivLength || empty($encrypted)) {
            return null; // O formato não confere com nossa criptografia
        }

        $plainText = openssl_decrypt($encrypted, self::METHOD, $key, OPENSSL_RAW_DATA, $iv);

        return $plainText !== false ? $plainText : null;
    }

    /**
     * Puxa a Chave de Segurança Global do .env (A Chave ÚNICA que desbloqueia tudo)
     */
    private static function getKey(): string
    {
        $appKey = getenv('APP_KEY');
        
        if (empty($appKey)) {
            throw new Exception("O sistema encontra-se bloqueado: APP_KEY não configurada no .env.");
        }
        
        // Se a APP_KEY do Laravel for "base64:...", removemos a tag e lemos cru
        if (str_starts_with($appKey, 'base64:')) {
            $appKey = base64_decode(substr($appKey, 7));
        }

        // Garante que o salt tenha no mínimo 32 len para o AES-256
        return hash('sha256', $appKey, true);
    }
}
