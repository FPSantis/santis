# Arquitetura de Serviços e Pastas (O Ecossistema Santis)

Para permitir escalabilidade até o cenário de múltiplos servidores na Hostinger ou Nuvem, nosso Monorepo local (`/mnt/d/_WEB/santis`) atua apenas como um ambiente de conveniência ("Brainstorm/Dev Local").

**Na vida real (Produção), os 3 serviços principais são fisicamente separados, hospedados em espaços ou instâncias diferentes, não compartilham código-fonte e conversam estritamente via protocolo HTTP (API/Assets).**

Abaixo a regra **inegociável** do que cada pasta raiz representa:

---

## 1. `cdn/` -> O Servidor de Arquivos (cdn.dominio.com)
* **Status em Prod:** Hospedagem/Storage Isolado.
* **Responsabilidade:** Receber uploads e servir arquivos binários para o mundo (`.css`, `.js`, imagens, vídeos, PDFs).
* **Restrições:** **NÃO TEM PHP** processando regras de negócio aqui. Não tem PDO. É apenas um balde público (S3, Cloudflare R2 ou Public HTML básico).
* **Quem o alimenta:** O `painel/` envia as imagens pra lá após aprovação.

## 2. `painel/` -> O Backend Master (painel.dominio.com)
* **Status em Prod:** Servidor PHP com MariaDB/MySQL. Fortemente Protegido.
* **Responsabilidade:** É o CMS Headless. Aqui reside o Banco de Dados. Ele valida logins (JWT), administra Tipos de Conteúdo e cospe dados dinâmicos através de uma API RESTful (`/api/v1/...`). O Webmaster loga aqui e constrói a base.
* **Restrições:** Não renderiza a cara do site do cliente. Renderiza **apenas** a casca administrativa (Sneat Pro). Ele não sabe e não se importa quem está puxando seus dados.

## 3. `www/` -> O Consumidor / Landing Page (www.dominio.com)
* **Status em Prod:** Pode ser uma hospedagem simples, um Vercel (Next.js), ou um App Mobile.
* **Responsabilidade:** Apresentar a Face Pública da Empresa para o visitante. Pegar o HTML/CSS incrível que foi aprovado pelo cliente, engatilhar funções **Javascript Fetch** (AJAX) para bater nos Endpoints do `painel/` e imprimir os Posts de Blog, Projetos de Portfólio ou Enviar Leads de Contato.
* **Restrições:** **NUNCA DEVE POSSUIR SENHAS DE BANCO DE DADOS (PDO) OU ROTAS PHP COMPLEXAS NO MESMO REPOSITÓRIO.** O site é "cego" e "burro". Toda sua inteligência vem de perguntar à API do Painel Administrativo.

---

## Pastas de Apoio do Monorepo Local (NÃO VÃO PARA PRODUÇÃO COMO ESTÃO)
- `.ddev/`: Os containers Docker que fingem ser a Hostinger.
- `docs/`: A Bíblia do projeto. Todo planejamento arquitetural reside aqui.
- `ref/`: Referências "Brutas" baixadas da internet (ex: o zip baixado do Sneat Pro HTML).
- `scripts/`: Shell Scripts de automação local do ambiente do desenvolvedor.
- `tmp/`: Lixo temporário e caches locais.

## Regra de Ouro do Dev (IAs inclusas)
Ao atuar em uma tarefa para o Site Público (`www`), **É TERMINANTEMENTE PROIBIDO** tentar importar arquivos usando manipulação de pastas globais que subam de nível (ex: `require '../../painel/src/Models/User.php'`). Em produção, a pasta `painel/` **não existirá** no mesmo disco rígido que o `www/`. Use Ferramentas Agnosticistas (CURL / Fetch API JS).
