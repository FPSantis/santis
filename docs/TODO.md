
# ROADMAP E TO-DO LIST DO PAINEL SANTIS

A estrutura do desenvolvimento e todas as pequenas tarefas (épicos) estão divididas em sub-tarefas para garantirmos entregas contínuas, seguindo a regra dos branches granulares descritas em `docs/workflow/rules.md`.

## FASE 1: Planejamento & Arquitetura Core (Atual)
- [x] Ajustar pastas dos domínios (adotar padrão `/public_html` da Hostinger).
- [x] Brainstorming das entidades Iniciais (RBAC, Site Content, CDN, Settings).
- [x] Documentar o Conceito do CMS Headless (Arquitetura Multi-Tenant).
- [x] Documentar Padrões de Git e Fluxo de Dev das IAs.
- [x] Implementar e Revisar a Arquitetura Final do Banco de Dados Dinâmico (`schema.sql`).
  *(Precisamos refazer o BD para acomodar Tipos dinâmicos e Tenants antes de iniciar a Fase 2)*

## FASE 2: Backend Core (API e Banco de Dados)
- [x] Estruturar Router base em PHP para API REST (Responder JSON/CORS).
- [x] Refatorar separação estrita de Assets Locais vs Mídia na CDN (Front e Docs atualizados).
- [x] Implementar sistema de Autenticação (JWT ou Sessão Base) no Backend.
- [x] Construir os CRUDs básicos (Tipos, Conteúdo, Configurações).
- [x] Integração Central com a Tabela/Pasta de CDN (Controlador e File Manager de Uploads).
- [x] Auditing Base (Fase P6) - Logar cada ação dos usuários no Backend em uma Tabela `audit_logs`.
- [x] Implementar Helper de Criptografia At-Rest LGPD (Fase P7) para ofuscação bidirecional em tabelas de PII (Leads/Usuários externos).

## FASE 3: Painel Webmaster e Admin (Sneat Pro)
- [x] Instalar os assets limpos do Sneat Pro na pasta `public_html` (apenas o necessário).
- [x] Criar Dashboard de Entrada (Visão Admin e Visão Geral).
- [x] Criar listagens de Arquivos do Módulo CDN.
- [x] Interface de controle Global Settings.
- [x] Interface do Webmaster para criação visual de "Tipos" de Conteúdo e "Drivers" de Mensageiros.
- [x] Ferramenta de Importação/Exportação (JSON) para Tipos de Conteúdo e Configurações (Reuso de templates entre clientes).

## FASE 4: Back-end API - Entidades, Mídia e Drivers
- [x] Construir Rota Rest `GET /api/v1/settings` (Disponibilizar Configs Globais como Slogan, Contatos, Cópias, SVG Logos).
- [x] Provisionar fisicamente (MariaDB/Painel) os 5 Novos Custom Types EAV (`services`, `partners`, `portfolio`, `social_networks`, `blog`).
- [x] Criar "Media Manager" e "Tipos de Upload" (Validação MIME/Resize) exportando estritos para `/cdn/public_html/portfolio/`, `/blog/` e `/config/`.
- [x] Implementar Serviços REST exclusivos (`GET /api/v1/services/share-options`, `POST /api/v1/messenger/whatsapp`, `POST /api/v1/scanner/pwned`).

## FASE 5: Front-end WWW (Vanilla JS & Fallback)
- [x] Renomear o `/www` antigo (Experimentos MVC) para `/ref/www_migrated` para preservação térmica de códigos CSS e classes uteis.
- [x] Mover todo pacote bruto HTML aprovado de `/ref/www` renascendo a real `/www/public_html/`.
- [x] Codificar a Classe Core de JS Fetch (`api.js`) mapeada para buscar os endpoints listados na Fase 4.
- [x] Criar lógica de "Fallback Autônomo" (Renderizar base fake/cacheada offline caso a API do Painel expire / falhe em responder).
- [ ] Conectar os retornos em JSON diretamente nas Áreas Alvo do HTML estático limpo (Destruir Skeleton Loader e inserir os Cards EAV).

## FASE 6: Escalabilidade SaaS (Blueprints)
- [ ] Construir o motor de "Blueprint Export/Import" (Exporta Tipos EAV + Settings Padrões + Scaffold CDN + Base WWW HTML).
- [ ] Modularizar injeções de código para garantir que novas Módulos Driver possam ser fundidos na `main` inativos.

## Fixo: Rotina Contínua
- [ ] Testes Nativos a cada Merge.
- [ ] Respeito absoluto à integridade isolada do `www`, `cdn` e `painel`.
