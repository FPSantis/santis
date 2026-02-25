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
- [ ] Auditing Base (Fase P6) - Logar cada ação dos usuários no Backend em uma Tabela `audit_logs`.
- [ ] Implementar Helper de Criptografia At-Rest LGPD (Fase P7) para ofuscação bidirecional em tabelas de PII (Leads/Usuários externos).

## FASE 3: Painel Webmaster e Admin (Sneat Pro)
- [ ] Instalar os assets limpos do Sneat Pro na pasta `public_html` (apenas o necessário).
- [ ] Criar Dashboard de Entrada (Visão Admin e Visão Geral).
- [ ] Criar listagens de Arquivos do Módulo CDN.
- [ ] Interface de controle Global Settings.
- [ ] Interface do Webmaster para criação visual de "Tipos" de Conteúdo e "Drivers" de Mensageiros.
- [ ] Ferramenta de Importação/Exportação (JSON) para Tipos de Conteúdo e Configurações (Reuso de templates entre clientes).

## FASE 4: Integração com Frontend (Santis / Apps)
- [ ] Configurar Landing Page para fazer GETs de conteúdo dos Tipos Dinâmicos.
- [ ] Formatar o Asset Manager do CMS para só injetar JS/CSS que a Landing precisar.
- [ ] Implementar forms de Contato (POST pra API de Mensageiro).

## FASE 5: Escalabilidade SaaS (Blueprints e Updates Base)
- [ ] Construir o motor de "Blueprint Export/Import" (Exporta Tipos + Scaffold de CDN + Scaffold WWW base).
- [ ] Modularizar injeções de código para garantir que novas `features/` possam ser mergiadas na `main` no estado "Desativado por Padrão" sem quebrar tenants existentes.

## Fixo: Rotina Contínua
- [ ] Atualização constante dos Docs.
- [ ] Testes Nativos a cada Merge.
