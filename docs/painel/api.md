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
```
