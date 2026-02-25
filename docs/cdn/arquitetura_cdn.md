# Rede de Entrega de Conteúdo (CDN & Media Manager)

Este documento dita a dinâmica do subdomínio apartado para arquivos estáticos (`cdn.santis.net.br`), integrado sob o ecossistema do Painel Santis.

## 1. Visão Geral
A `cdn` atua fisicamente isolada de processamento pesado (PHP). Seu propósito é exclusivamente entregar estáticos (Imagens, PDFs, Vídeos, Fontes) e desonerar os containers principais (`www` e `painel`) de requisições de I/O em binários.

A raiz de arquivos públicos segue o padrão da Hostinger: `cdn.santis.net.br/public_html/`.

## 2. A Camada "Media Manager" do Painel
A submissão de um arquivo nunca ocorre de forma "cega". Todo upload passa através do **Media Manager** do painel antes de pousar fisicamente na CDN. 

A arquitetura do Media Manager é baseada em **Regras (Rules)** criadas pelo Webmaster para cada "Tipo" de Post ou Configuração:
1. **Validação Estrita:** Se o Webmaster define que o campo "Logo" usa a *Regra Logo*, o Media Manager vai rejeitar (HTTP 415/422) qualquer arquivo que não seja `.svg` ou que passe de 2MB.
2. **Transformação Automática:** Se o upload for para um Post de Blog, a regra embutida no Media Manager fará a conversão automática do arquivo para `.webp`, reduzindo dimensões e sanitizando o nome do arquivo com timestamps.
3. **Endereçamento Lógico:** Após o sucesso, o Manager decide a pasta lógica na CDN (`/blog/YYYY/`) e insere o registro no Banco de Dados (`cdn_files`), tornando o arquivo não apenas um link, mas uma "Entidade Mídia" reutilizável e rastreável pelo tenant.

## 3. Entrega ao Público (Consumo Frontend)
No consumo pelo `www` (Frontend), as URLs da CDN formadas nos Endpoints do painel já acompanham o caminho absoluto ou o hostname parametrizável para maximizar o cache hit-rate nos nós de proxy reversos globais (Cloudflare / Edge).

---
*Para consultar o fluxo de permissões de quem pode criar Regras vs quem faz Upload, veja a arquitetura central em `docs/architecture/cms_core_concept.md`.*
