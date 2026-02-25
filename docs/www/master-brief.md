# Site Institucional (WWW): Master Brief V2
**Inteligência, Tecnologia e Performance**

Este documento detalha o site institucional principal da Santis (`www.santis.net.br` / `www.santis.ddev.site`), descrevendo a lógica técnica, os módulos estruturais e o motor de renderização.

---

## 🏗️ 1. Arquitetura Técnica & Design
O frontend utiliza uma abordagem **Vanilla Modern MVC**, rodando nativamente sobre PHP com o template engine **Twig**.
O objetivo é máxima performance e SEO, entregando HTML estático processado no backend, sem o overhead de frameworks SPA pesados no navegador.

-   **Cores Primárias**: `#050A18` (Navy), `#00F2FF` (Cyan), `#8A2BE2` (Purple).
-   **Tipografia**: Montserrat (Impacto) e Inter (Leitura).
-   **Efeitos**: `backdrop-blur` para glassmorphism, animações de gradiente via CSS (`animate-gradient`), e glows interativos via Vanilla JS.

---

## 🧩 2. Estrutura Modular (Arquitetura Twig)
O site **não é monolítico**. A página principal (`Home`) atua apenas como um agregador que invoca módulos independentes localizados em `src/Modules/`:

1.  **Hero** (`src/Modules/Hero/`): Foco em "Performance e Tecnologia" com gradientes animados e campo de busca (Scan).
2.  **Performance** (`src/Modules/Performance/`): Timeline de otimização de sistemas em 4 passos (Anamnese, Diagnóstico, Execução, Entrega).
3.  **Partners** (`src/Modules/Partners/`): Carrossel infinito monocromático com logotipos e stack de tecnologia.
4.  **Services** (`src/Modules/Services/`): Foco no desenvolvimento de Autoridade Digital (Tecnologia sob medida, Visão do Futuro, Gestão Ágil).
5.  **Social** (`src/Modules/SocialLink/`): Links rápidos e botões de conversão social.
6.  **Portfolio** (`src/Modules/Portfolio/`): Grid interativo "Projetos Dinâmicos" (Online/Legacy) com efeito blur e carrossel interno modal.
7.  **Radar** (`src/Modules/Radar/`): Listagem de artigos do Blog intercalada com pílulas informativas.
8.  **Contact** (`src/Modules/Contact/`): Integração direta com o especialista via WhatsApp e rodapé fluido.

*Nota:* Headers e Footers são globais e residem em `src/Views/_partials/`.

---

## 📂 3. Separação Estrita de Assets e Código
Em fevereiro de 2026, a arquitetura foi aprimorada para o modelo profissional de isolamento total de responsabilidades, dividindo o ecossistema em três camadas físicas irrevogáveis:

### 3.1. Camada de Lógica e Renderização (`www/src/*`)
Contém **exclusivamente** regras de negócio (Controllers PHP) e os templates visuais dinâmicos (Views Twig). *Nenhum* asset ou imagem que o navegador precise processar vive aqui. Esta pasta é inacessível da internet pelo Nginx para reforço sumário de segurança.

### 3.2. Camada de Assets Estruturais da "Casca" (`www/public_html/assets/*`)
O subdomínio da Landing Page entrega diretamente pelo Nginx **apenas o que forma a interface**:
- Folhas de estilo (CSS) compiladas por Módulo.
- Scripts Vanilla (JS) de comportamento e animação de interface.
- Imagens Vetoriais/Logos estruturais do próprio site (ex: `logo-santis.svg`).

### 3.3. Camada de Conteúdo Dinâmico (A CDN) (`cdn/public_html/*`)
Isolamento em subdomínio nativo `cdn.santis...`. Aqui reside tudo que não é o código-fonte da aplicação front-end, mas sim aquilo que é conteúdo inserido ou alterado com o tempo.
- **Estrutura**: Pastas segmentadas por módulo e temporalidade (`cdn/public_html/<modulo>/YYYY/MM/`).
- Abriga mockups dinâmicos do Portfólio, miniaturas de posts do Radar (Blog), logotipos de parceiros em nuvem e arquivos de serviços. Todo link de mídia em JSON aponta obrigatoriamente para a URL base da CDN.

---

## 🔍 4. Lógicas Interativas Principais

### Modal de Verificação (Santis Scan)
Ativado no módulo Hero, simula uma varredura real:
- Consulta à API **HaveIBeenPwned**.
- Mapeamento dinâmico de risco (Crítico x Seguro).
- Animações CSS em cascata imitando um terminal.

### Portfólio Expansível
- Cards que ao receberem `hover` acendem um border glow e revelam um botão de acesso.
- Ao clicar, um overlay injeta um carrossel navegável sem a necessidade de recarregar a página (completamente desenvolvido em Vanilla JS).

---

## 📡 5. Roteamento (Controllers)
As requisições públicas (que passam pelo `public_html/index.php`) são interpretadas pelo `Router.php` que invoca os controllers específicos (`src/Controllers/`):

- **SiteController**: Processa a Home page e renderiza a composição Módulo a Módulo.
- **BlogController**: Processa a exibição das publicações do "Radar Santis" (Listagem e Single Post).

---
*Este documento reflete a versão final do Frontend reescrito na arquitetura modular v2.*