# Arquitetura do Painel Administrativo (Santis CMS V2)

Este documento dita a arquitetura do Painel Administrativo da Santis (CMS Headless Multi-Tenant). Servido sob o host `painel.santis.net.br`.

## 1. Visão Geral (O Coração do CMS)
O Painel deixou de ser um administrador estático de apenas um site. Ele agora é o motor de dados (Headless) projetado para gerenciar múltiplos "Tenants" (Clientes ou Sites). 

Sua responsabilidade é fornecer uma interface de usuário (UI) limpa nas visões Admin/Editor e expor APIs (Endpoints REST/GraphQL) de altíssima performance para consumo do Frontend (www).

## 2. Padrão Estrutural (Tipos e Módulos Dinâmicos)
A arquitetura de dados não é "Hardcoded" para páginas fixas. Ela atua baseada na construção de "Tipos" (Entity-Attribute-Value).

1. **Webmaster Panel:** Área restrita a desenvolvedores da Santis. Aqui, o Webmaster constrói a estrutura de um cliente, declarando quais "Módulos" (ex: Mensageiros de Form, Analytics) e quais "Tipos de Post" (ex: Blog, Cardápio, Vagas, Portfólio) aquele cliente vai usar. 
2. **Admin/Editor Panel:** O cliente entra no painel e vê apenas a UI gerada dinamicamente para ele alimentar o conteúdo dos `Tipos` habilitados, sem acesso à estrutura de banco.

## 3. Segurança e Auditoria (Audit Trail)
O painel atua como o **único intermediário autorizado a escrever e deletar no banco de dados e na CDN**. 
Toda transação que altera estado (POST, PUT, DELETE) passa obrigatoriamente por middleware de Autenticação (JWT ou Sessões) e é gravada no sistema de `Audit Trail`, registrando *quem fez a alteração, quando fez, e de qual IP/dispositivo*.

## 4. Gerenciamento de Assets (Asset Manager Base)
Para servir a Landing Page com notas 100 de performance, o Painel orquestra quais CSS e JS são despachados via API. O painel entrega em seu payload os dados `json` atrelados às tags estruturais (ex: `Requires: [share.css, gallery.js]`) garantindo Injeção Sob Demanda (Lazy Loading de Assets) no Frontend.

---
*Para o workflow de desenvolvimento e Git deste projeto, consulte obrigatoriamente `docs/workflow/rules.md`.*
*Para a visão arquitetural completa com a analogia de permissões (Webmaster, Admin, Editor), acesse `docs/architecture/cms_core_concept.md`.*
