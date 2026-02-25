# Santis CMS - API Reference

Esta é a documentação viva da API REST (Headless CMS) provida pelo diretório `painel/`.

## Endpoints Públicos

### POST `/api/login`
Autentica um usuário e retorna um JSON Web Token (JWT).

**Request Body (JSON):**
```json
{
  "email": "admin@santis.net.br",
  "password": "sua-senha-segura"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "token": "eyJ0eXAi... (Seu JWT longo)",
    "expires_in": 7200,
    "user": {
      "id": 1,
      "name": "Webmaster Santis",
      "email": "admin@santis.net.br",
      "tenant_id": 1
    }
  },
  "message": "Login autorizado e token emitido."
}
```

---

## Endpoints Protegidos (Módulo Seguro)
> **Todos** os endpoints abaixo necessitam da header `Authorization: Bearer <TOKEN>` oriunda do endpoint de login.

### GET `/api/secure/me`
Retorna as credenciais decodificadas da sua Sessão JWT atual. 

---

### GET `/api/secure/types`
Lista todos os `Content Types` (Tipos de Conteúdo ou Módulos) ativos do sistema pertencentes àquele Cliente/Tenant.

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "tenant_id": 1,
      "name": "Radar",
      "slug": "radar",
      "description": "Feed de Notícias e Novidades",
      "schema": "{\"fields\": [\"title\", \"content\", \"author\"]}",
      "is_active": 1,
      "...": "..."
    }
  ],
  "message": "1 tipos de conteúdo encontrados."
}
```

---

### POST `/api/secure/types`
Registra via Active Record um NOVO `Content Type` estrutural.

**Request Body (JSON):**
```json
{
  "name": "Portfólio",
  "slug": "portfolio",
  "description": "Lista de projetos realizados",
  "schema": "{\"fields\": [\"title\", \"client\", \"year\", \"gallery\"]}",
  "is_active": 1
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "data": {
    "id": 2,
    "name": "Portfólio",
    "slug": "portfolio",
    "...": "..."
  },
  "message": "Tipo de conteúdo criado com sucesso."
}

---

### GET `/api/secure/entries/{type_slug}`
Exibe todas as "Entradas" ou "Posts" associadas a um Módulo específico (`type_slug`). Por exemplo, `/api/secure/entries/radar` vai carregar o blog, enquanto `/api/secure/entries/portfolio` vai carregar os cases.

**Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 15,
      "tenant_id": 1,
      "content_type_id": 1,
      "category_id": 3,
      "title": "Novo Evento Online",
      "slug": "novo-evento-online",
      "status": "published",
      "content_data": {
        "author": "Equipe Santis",
        "featured_image": "/cdn/public_html/radar/2026/02/banner.jpg"
      },
      "category_name": "Tecnologia",
      "category_slug": "tecnologia",
      "...": "..."
    }
  ],
  "message": "1 entradas do tipo 'radar' localizadas."
}
```

---

### POST `/api/secure/entries/{type_slug}`
Cria uma nova Entrada/Registro para um módulo. O campo milagroso aqui é o `content_data`, que aceita um objeto JSON livre e o injeta diretamente no Banco de Dados MariaDB como Dado Estruturado EAV Híbrido.

**Request Body (JSON):**
```json
{
  "title": "Novo Evento Online",
  "slug": "novo-evento-online",
  "status": "published",
  "category_id": 3,
  "content_data": {
    "author": "Equipe Santis",
    "featured_image": "/cdn/public_html/radar/2026/02/banner.jpg"
  }
}
```

**Response (201 Created):**
```json
{
  "success": true,
  "data": {
    "id": 16,
    "slug": "novo-evento-online"
  },
  "message": "Entrada documentada e arquivada via EAV."
}
```

---

### POST `/api/secure/upload`
Endpoint validador de arquivo binário (`multipart/form-data`) que envia imagens ou PDFs fisicamente para as pastas da nossa CDN Frontend e gera uma URL relativa pronta para ser lincada em qualquer entrada do banco.

**Request Form-Data:**
- `file` (Arquivo Binário de até 5MB)
- Extensões Permitidas: `jpg, png, webp, gif, pdf`.

**Response (201 Created):**
```json
{
  "success": true,
  "data": {
    "id": 8,
    "tenant_id": 1,
    "filename": "fundo-hero-1abc99.webp",
    "original_name": "fundo-hero.webp",
    "mime_type": "image/webp",
    "size_bytes": 104500,
    "path": "/uploads/2026/02/fundo-hero-1abc99.webp",
    "uploaded_by": 1,
    "full_url": "https://cdn.santis.ddev.site/uploads/2026/02/fundo-hero-1abc99.webp"
  },
  "message": "Upload realizado com sucesso transferido para CDN."
}
```
```
