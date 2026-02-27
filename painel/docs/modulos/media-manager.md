# Gerenciador de Mídia (Media Manager)

> Módulo de gerenciamento de ativos estáticos do CDN do Santis CMS.

---

## Visão Geral

O Gerenciador de Mídia é um módulo do painel administrativo que permite navegar, visualizar, fazer upload e editar os metadados dos arquivos hospedados no CDN (`cdn/public_html/`).

A interface é inspirada na biblioteca de mídia do WordPress, com suporte a:
- Navegação hierárquica de pastas (breadcrumb completo)
- Upload via drag-and-drop (Dropzone.js)
- Grade e lista de arquivos
- Edição de metadados (nome, alt text, URL pública)
- Exclusão individual ou em massa
- Visualização especial para SVGs (fundo xadrez)

---

## Arquitetura

### Rota de Acesso

| Tipo   | Rota           | Controller              | Método    |
|--------|----------------|-------------------------|-----------|
| Web    | `GET /media`   | `WebController::media()` | Renderiza `media.twig` |
| API    | `GET /api/secure/media` | `MediaController::index()` | Lista pastas e arquivos |
| API    | `POST /api/secure/media/upload` | `MediaController::upload()` | Upload de arquivo |
| API    | `GET /api/secure/media/{id}` | `MediaController::show()` | Detalhe de um arquivo |
| API    | `PUT /api/secure/media/{id}` | `MediaController::update()` | Atualiza metadados |
| API    | `DELETE /api/secure/media/{id}` | `MediaController::delete()` | Remove um arquivo |
| API    | `POST /api/secure/media/delete-bulk` | `MediaController::deleteBulk()` | Remove múltiplos |

---

## Estrutura de Pastas no CDN

As pastas raiz mapeiam diretamente para os **módulos de conteúdo (ContentTypes)**:

```
cdn/public_html/
├── partners/       → Módulo: Parceiros       (slug: partners)
├── portfolio/      → Módulo: Portfólio       (slug: portfolio)
├── radar/          → Módulo: Radar/Blog      (slug: blog)
└── services/       → Módulo: Serviços        (slug: services)
```

O match entre pasta e módulo é feito via o campo `path` da tabela `media_folders`:
- `/partners` → busca `ContentType` com `slug = 'partners'`
- Fallback: se nenhum módulo encontrado, usa ícone `bx-folder` e o nome da pasta

---

## Banco de Dados

### Tabela: `media_files`

Armazena os metadados dos arquivos enviados.

| Coluna        | Tipo         | Descrição                         |
|---------------|--------------|-----------------------------------|
| `id`          | INT PK       | ID interno                        |
| `tenant_id`   | INT          | Tenant do arquivo                 |
| `folder_id`   | INT FK       | Pasta pai (`media_folders`)       |
| `file_name`   | VARCHAR      | Nome amigável                     |
| `file_path`   | VARCHAR      | Caminho relativo no CDN           |
| `mime_type`   | VARCHAR      | MIME type do arquivo              |
| `size`        | INT          | Tamanho em bytes                  |
| `alt_text`    | TEXT         | Texto alternativo (SEO)           |
| `caption`     | TEXT         | Legenda opcional                  |
| `created_at`  | DATETIME     | Data de criação                   |

### Tabela: `media_folders`

Representa os diretórios no CDN.

| Coluna       | Tipo     | Descrição                        |
|--------------|----------|----------------------------------|
| `id`         | INT PK   | ID interno                       |
| `tenant_id`  | INT      | Tenant da pasta                  |
| `name`       | VARCHAR  | Nome de exibição da pasta        |
| `parent_id`  | INT FK   | Pasta pai (NULL = raiz)          |
| `path`       | VARCHAR  | Caminho relativo no CDN          |

---

## Componentes do Frontend

### `src/Views/media.twig`

Template principal. Contém:
- Header com breadcrumb navegável (`#mediaBreadcrumb`)
- Barra de filtros (tipo, busca, grid/lista)
- Grade de cards (pastas + arquivos)
- Offcanvas lateral de detalhe/edição (`#offcanvasDetails`)
- Upload por Dropzone.js (`#dz-upload`)

#### Estado JS

```js
let currentFolderId = null; // null = raiz
let breadcrumbStack = [];   // histórico de navegação [{id, name}, ...]
let currentView = 'grid';   // 'grid' | 'list'
let initialized = false;    // flag para o IntersectionObserver
```

#### Funções principais

| Função               | Descrição                                              |
|----------------------|--------------------------------------------------------|
| `navigateFolder(id, name, stackIndex)` | Navega para uma pasta e atualiza breadcrumb |
| `loadMedia(refresh)` | Chama a API e renderiza pastas/arquivos               |
| `renderFolders(folders)` | Renderiza cards de categoria (com ícone do módulo) |
| `renderFiles(files)` | Renderiza cards de arquivo (imagens, SVGs, PDFs)      |
| `openDetails(id)`    | Abre o offcanvas lateral com dados do arquivo         |
| `renderBreadcrumbs()`| Atualiza o trail de navegação no `#mediaBreadcrumb`   |

---

## CDN URL Dinâmica

O CDN base URL é determinado dinamicamente pelo backend conforme o ambiente:

| Ambiente    | CDN URL                   |
|-------------|---------------------------|
| DDEV (local)| `https://cdn.santis.ddev.site` |
| Produção    | `https://cdn.santis.net.br`   |

A lógica está em `MediaController::getCdnBaseUrl()` e é passada ao Twig via `WebController::getSiteVariables()` como `cdn_url`.

---

## Regras de Negócio

1. **Upload desabilitado na raiz**: O botão "Adicionar novos ativos" é desabilitado quando `currentFolderId === null`. O upload só é permitido dentro de uma categoria específica.
2. **Pastas internas ocultadas**: As pastas `config`, `gerenciador`, `inicio` são filtradas da listagem raiz.
3. **Infinite scroll**: Paginado com 40 itens por página via `IntersectionObserver`, ativado somente após a carga inicial (`initialized = true`).
4. **Fundo xadrez para SVG/PNG**: Cards de imagens SVG e PNG usam fundo xadrez (checkerboard) para tornar conteúdo transparente visível.

---

## Dependências

- [Dropzone.js](https://www.dropzone.dev/) — Upload drag-and-drop
- Bootstrap 5 (Sneat theme) — UI components (Offcanvas, Collapse)
- Boxicons (`bx-*`) — Ícones

---

## Melhorias Futuras

- [ ] Renomear pasta CDN `radar` para `blog` (ou atualizar slug do módulo Blog para `radar`)
- [ ] Adicionar suporte a busca por tags/categorias
- [ ] Filtro por intervalo de datas
- [ ] Preview de vídeo no offcanvas
- [ ] Drag-and-drop para reorganizar arquivos entre pastas
