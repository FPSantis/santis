<?php

namespace Painel\Core;

use Exception;

class ImageHelper
{
    /**
     * Processa a imagem enviada, converte para WEBP e salva redimensionada (se necessário)
     * Mantém a proporção (Aspect Ratio) correta.
     * 
     * @param string $sourcePath Caminho absoluto do arquivo temporário a ser lido
     * @param string $destinationPath Caminho absoluto onde o arquivo .webp será salvo
     * @param int $maxWidth Largura máxima em pixels permitida (0 para não limitar)
     * @param int $maxHeight Altura máxima em pixels permitida (0 para não limitar)
     * @param int $quality Qualidade WEBP (0 a 100)
     * 
     * @return bool True se processado com sucesso, false caso contrário
     * @throws Exception Em caso de formato não suportado ou erro na biblioteca de imagem
     */
    public static function processToWebp(string $sourcePath, string $destinationPath, int $maxWidth = 1200, int $maxHeight = 1200, int $quality = 85): bool
    {
        if (!extension_loaded('gd')) {
            throw new Exception("Extensão GD do PHP não está habilitada. Não é possível processar imagens.");
        }

        if (!file_exists($sourcePath)) {
            throw new Exception("Arquivo de origem não encontrado.");
        }

        // 1. Descobrir Informações Originais da Imagem
        $info = getimagesize($sourcePath);
        if ($info === false) {
            throw new Exception("O arquivo não é uma imagem válida suportada.");
        }

        $width = $info[0];
        $height = $info[1];
        $mime = $info['mime'];

        // 2. Criar um recurso de imagem nativo do PHP baseado no tipo original
        $image = null;
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = @imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = @imagecreatefrompng($sourcePath);
                break;
            case 'image/webp':
                $image = @imagecreatefromwebp($sourcePath);
                break;
            case 'image/gif':
                $image = @imagecreatefromgif($sourcePath);
                break;
            default:
                throw new Exception("Tipo MIME não suportado para conversão: {$mime}");
        }

        if (!$image) {
            throw new Exception("Falha ao abrir o recurso de imagem do arquivo (memória ou corrupção).");
        }

        // Lidar com transparência original se for PNG/WEBP
        if ($mime === 'image/png' || $mime === 'image/webp') {
            imagepalettetotruecolor($image);
            imagealphablending($image, true);
            imagesavealpha($image, true);
        }

        // 3. Lógica de Redimensionamento Mantendo a Proporção
        $newWidth = $width;
        $newHeight = $height;

        if ($maxWidth > 0 && $maxHeight > 0) {
            if ($width > $maxWidth || $height > $maxHeight) {
                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = (int) round($width * $ratio);
                $newHeight = (int) round($height * $ratio);
            }
        }

        // Criar uma nova tela (canvas) redimensionada
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Manter o fundo transparente para a nova tela
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefill($newImage, 0, 0, $transparent);

        // Copiar e redimensionar a imagem original sobre a nova tela
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Liberar a memória da imagem monstruosa original
        imagedestroy($image);

        // 4. Salvar Fisicamente como WEBP
        $result = imagewebp($newImage, $destinationPath, $quality);

        // Liberar a nova tela redimensionada
        imagedestroy($newImage);

        if (!$result) {
            throw new Exception("Falha interna ao tentar compilar e escrever o arquivo WEBP na CDN.");
        }

        return true;
    }
}
