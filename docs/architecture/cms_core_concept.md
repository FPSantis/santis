# Documento Arquitetural do Painel Santis (CMS Headless Multi-Tenant)

Este documento registra o conceito, as regras de negócios e as responsabilidades de atores dentro do sistema CMS Multi-Tenant da Santis, projetado para atender múltiplos clientes e aplicativos de forma dinâmica.

## 1. Níveis de Acesso e Responsabilidades (Analogy: A Cozinha)

O sistema deve operar em um formato hierárquico, onde diferentes perfis têm permissões para criar estrutura (Cozinha), cozinhar (Ingredientes) ou apenas servir pratos (Frontend).

| Perfil | Descrição Técnica | Analogia da Cozinha |
| :--- | :--- | :--- |
| **Webmaster** | Usuário Mestre do Sistema (Super Admin Santis). Responsável por configurar os "Tipos" (Services, Posts, ACF), habilitar Módulos (Mensageiros, Compartilhamento) para um determinado site/cliente. | **Arquiteto da Cozinha:** Define e compra a geladeira, fogão, e os utensílios disponíveis. |
| **Admin (Cliente/Owner)** | Dono do Site no painel. Responsável pelas configurações do seu próprio site (Ativar/Desativar seções, inserir tokens). Ele administra as opções disponibilizadas pelo Webmaster. | **Chef de Cozinha:** Usa o fogão, a panela, dita a receita e compra os ingredientes. |
| **Editor / Colaborador** | Membro da equipe do Cliente. Só tem acesso à criação e edição do conteúdo em si (Textos, Imagens) nos módulos já configurados. Não vê painéis técnicos ou configurações estruturais do site. | **Cozinheiro:** Abre a receita, mistura os ingredientes e prepara o prato. |
| **Público/Frontend** | Landing Page, Aplicativo ou Serviço consumindo as APIs REST / GraphQL entregues pelo Painel. | **O Restaurante:** Onde o cliente (visitante) vê apenas o prato finalizado consumindo a API. |

---

## 2. Fundamentos Básicos (Módulos Obrigatórios)

Para qualquer cliente cadastrado no painel, teremos módulos centrais:

1. **Autenticação & Controle de Acesso:** Login, Logout e gestão de sessões.
2. **Usuários e Permissões:** Cadastro de usuários e associação a Perfis (Roles) atrelados a um Cliente específico.
3. **Auditoria (Audit Trail):** Registro de quem fez o quê, central para a governaça dos Administradores/Webmasters.

---

## 3. Módulos Dinâmicos e Configuráveis

A criação de um módulo no painel deixará de ser fixa (Hardcoded) para o cliente. O Webmaster os "instancia" dinamicamente conforme necessidade.

### 3.1. Configurações Gerais (Global Settings)
Sistema Chave/Valor (Key-Value).
- Mapeia elementos essenciais como `title`, `description`, `logo`, `favicon`.
- *Dinamicidade:* Se um cliente precisa de uma `logo_alternativa` (versão dark/monocromática), o Webmaster apenas cria um novo campo chave/valor para o tipo "Logo", e o Admin do Cliente o preenche.

### 3.2. Módulo de Conteúdo Dinâmico (Entidades Genéricas "Tipos")
Base de "Custom Post Types" e ACF (Advanced Custom Fields), mas pensada para Headless.
- **Tipos de Postagens/Coleções:** Serviços, Blog, Portfólio, Menu/Cardápio.
- Cada *Tipo* possui uma estrutura definida pelo Webmaster (Ex: `Serviço` exige `id, slug, pre_titulo, titulo, categoria, image, body, action_button`).
- *Sub-Tabelas Sugeridas:*
  - `modules`: Definição de quais campos comporão o tipo.
  - `categories`: Taxonomia atrelada a cada Módulo/Tipo.
  - `posts / entries`: Os registros criados pelos Editores.

### 3.3. Serviços de Mensageria (Messenger Service & Drivers)
Um gerenciador central para saídas de comunicação (Outbound).
- O Frontend coleta dados (ex: formulário de lead) e envia ao Painel.
- O Painel delega o formato de envio para os **Drivers** configurados.
- **Drivers:** 
  - *Driver E-Mail (SMTP, PHPMailer)*
  - *Driver WhatsApp (URL Schema `wa.me/` ou API Oficial Cloud)*
  - *Driver SMS*
- O Admin cadastra seus detalhes (o e-mail destino ou número receptor), mas o Webmaster quem designa se aquele driver existe pro cliente.

### 3.4. Módulo de Redes Sociais e Compartilhamento (Social / Share)
Agrega dados para OpenGraph / SEO Automático baseados no conteúdo.
- Configura as tags fixas e define regras de compartilhamento (ex: link automático gerando as rotas).
- Similar ao Mensageiro: possuirá *Drivers* formatadores para o Twitter Card, Facebook OG, WhatsApp Card.

### 3.5. Controle Avançado de Mídia (Media Manager & Rules)
Diferente de um simples upload, funciona por *Tipos de Mídia* e *Restrições (Rules)* estipuladas pelo Webmaster.
- **Exemplo *Regra Logo*:** Aceita apenas formato `SVG`, tamanho máximo `2MB`, arquivados na pasta `logos`.
- **Exemplo *Regra Blog*:** Conversão automática para `.webp`, sufixo baseado em tempo numérico UNIX, salvo em `blog/YYYY/MM/`, limitados a resolução fixa.
- No cadastro do Tipo "Postagem de Blog", o Webmaster indica que o campo de imagem obedecerá a "Regra Blog". O Editor fará upload de uma foto do celular, e o *Media Manager* já trata o arquivo antes de enviá-lo para a CDN. 

---

## 4. Arquitetura Core e Funcionalidades Avançadas

### 4.1. Core: Performance e Asset Manager Dinâmico
Foco absoluto em performance e "Green Web". Tanto no painel quanto nas Landings, os arquivos estáticos (CSS/JS) não serão inseridos por padrão num `app.css` ou `app.js` gigante.
- **Lazy Loading de Assets:** O sistema base verifica quais *Módulos* (Ex: Compartilhamento, Galeria, Formulário) estão sendo impressos na rota solicitada.
- **Injeção Sob Demanda:** Carrega apenas o `.css` e `.js` daquele componente em tempo de execução. Se não tem Compartilhamento na Home, `share.js` e `share.css` não são embutidos. 
- *Isso garante uma nota 100 no Lighthouse e reduz a pegada digital.*

### 4.2. Integrações / APIs de Terceiros e Funções Específicas (Edge Cases)
Como lidar com uma funcionalidade super exclusiva, como a API do `haveibeenpwned.com` feita especificamente para a Santis?
- **O Dilema:** Colocar regras de negócio de um site específico no core do CMS mancha a arquitetura *Multi-Tenant* agnóstica.
- **A Solução Ideal (Proposta):** 
  - O painel deve ter um Módulo de "Integrações" ou "Webhooks".
  - Nele, o Webmaster cadastra que a integração "Scanner Have I Been Pwned" existe para este cliente, salvando nela a API Key.
  - O **código (A Regra de Negócio)** que faz a consulta à API do *haveibeen* e formata o PDF deve viver primariamente no Backend da própria Landing Page (ou num Microserviço Serverless na Vercel/Cloudflare atrelado a essa L.P), consumindo a API Key guardada no Painel.
  - Alternativamente, se quisermos manter a regra no backend do Painel: Criamos uma arquitetura de `Plugins / Extensões` exclusivas por Tenant. O código vive no painel, mas numa sandbox (ex: `app/Plugins/SantisScanner/`). O frontend faz o POST para a API do painel `api/v1/plugins/santis-scanner`, e o painel devolve o conteúdo ou o PDF gerado.
  - **Sobre o PDF:** O ideal focado em performance é "On-Demand View". Exibe-se o relatório na tela do Frontend e ali colocamos um botão que injeta uma biblioteca puramente no browser (`window.print()` estilizado ou jspdf) para que o cliente "baixe o PDF". Assim poupamos servidor de tráfego, armazenamento (salvar no CDN) de algo temporário, e computação backend.

---

## 5. Escalabilidade SaaS (Blueprints e Encapsulamento)

Pensando em uma adoção "SaaS" onde o sistema roda em múltiplos servidores/clientes, a arquitetura deve prever dois comportamentos vitais a longo prazo.

### 5.1 Importação/Exportação Total (Site Blueprints)
Além de exportar apenas um "Custom Type", o Webmaster poderá exportar um "Blueprint" completo de um cliente (Ex: "Template Imobiliária").
- **O que compõe um Blueprint:** A definição de Tipos (Imóveis, Corretores, Contato), Configurações Padrão (Cores genéricas), Arquitetura da CDN vazia (pastas pré-criadas) e a base estática do Frontend (www).
- Quando um novo cliente entrar, o Webmaster clica em "Gerar do Blueprint". O sistema instantaneamente clona o esquema de pastas na CDN, cria as tabelas do Tenant baseadas no JSON, e acopla a Landing Page padrão. O administrador só precisará trocar cores, logotipos e textos.

### 5.2 Arquitetura de Módulos Independentes (Safe Updates)
Para poder atualizar todos os clientes simultaneamente (ex: Repositório Base V1.0 para V1.1) através de um comando ou `git pull`, os Módulos (Mensageiros, Tipos de Mídia, Social) devem ser 100% **encapsulados e independentes**.
- Adicionar um novo Driver de Mensageiro (ex: Slack) jamais deve tocar no script de envio via E-Mail.
- O novo código do "Driver Slack" é injetado na base de código de todos os clientes em uma atualização.
- **Segurança por Omissão:** Qualquer funcionalidade nova atrelada a uma atualização de versão deve subir no servidor **Desativada por Padrão**. O cliente não leva sustos. Cabe ao Webmaster entrar no painel daquele cliente específico e habilitar o novo módulo.

---

## 6. Próxima Etapa: O Banco de Dados Dinâmico

Para suportar essa arquitetura *EAV (Entity-Attribute-Value)* ou *Relacional Dinâmica*, as tabelas precisarão prever a separação por `tenant_id` (Cliente) e os mapeamentos dos "Campos Personalizados".

Toda a codificação SQL só deverá ser iniciada após a validação unânime destes conceitos pelos idealizadores.
