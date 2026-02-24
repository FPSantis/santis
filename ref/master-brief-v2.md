# Santis Engenharia Digital: Master Brief V2
**Inteligência, Tecnologia e Performance**

Este documento detalha o ecossistema digital **Santis**, descrevendo cada seção, funcionalidade e a lógica técnica por trás da experiência do usuário. 

---

## 🏗️ 1. Arquitetura Técnica & Design
O site utiliza uma abordagem **Vanilla Modern**: alta performance sem o overhead de frameworks pesados.
-   **Cores Primárias**: `#050A18` (Navy), `#00F2FF` (Cyan), `#8A2BE2` (Purple).
-   **Tipografia**: Montserrat (Impacto) e Inter (Leitura).
-   **Efeitos**: `backdrop-blur` para glassmorphism, animações de gradiente via CSS (`animate-gradient`), e glows interativos.

---

## 🏠 2. Estrutura Detalhada da Home

### A. Seção Hero & Scanner Inicial
-   **Headline**: Foco em "Performance e Tecnologia" com gradientes animados.
-   **Input de Varredura**: Campo de busca inicial conectado ao `search-experience.js`.
-   **Cards de Acesso Rápido**: Links diretos para "Site Seguro" e "Otimização".

### B. Seção Performance (Cuidado Técnico Profissional)
Focada em otimização de sistemas, dividida em um fluxo de 4 passos:
1.  **Anamnese & Entrevista**: Identificação de gargalos.
2.  **Diagnóstico & Proposta**: Relatório detalhado.
3.  **Execução & Blindagem**: Drivers, Windows Lite e limpeza.
4.  **Entrega & Backup**: Suporte pós-entrega.

### C. Seção Web Presence (Sites de Credibilidade)
Focada no desenvolvimento de autoridade digital:
-   **Tecnologia sob Medida**: Infraestrutura construída do zero.
-   **Visão do Futuro**: Experiência mobile fluida.
-   **Gestão Ágil**: Painéis administrativos modernos.

### D. Santis Control (Autoridade Técnica)
Apresentação do ecossistema proprietário v2.4, baseado em 4 pilares:
1.  **Gerenciamento Centralizado**: Controle total de dados.
2.  **Automação & Sincronia**: Publicação multiplataforma.
3.  **Segurança & LGPD**: Blindagem jurídica e técnica.
4.  **Interface Ágil**: Responsividade extrema.

---

## 🔍 3. Modal de Verificação (Search Experience)
Ativado via botão "Verificar Agora", o modal simula um protocolo de varredura real:
-   **Fases de Scan**: 
    1. Iniciação de Injeção.
    2. Acesso à API HaveIBeenPwned.
    3. Mapeamento de Incidentes.
    4. Cálculo de Vetor de Risco.
-   **Resultados Dinâmicos**:
    -   **Crítico (Found)**: Mostra nível de risco (ex: 75%) e timeline de vazamentos reais.
    -   **Protegido (Not Found)**: Feedback positivo de segurança.
-   **CTA de Fechamento**: Direciona o usuário para "Resolver com Especialista" (âncora `#contato`).

---

## 🎨 4. Portfólio de Projetos Dinâmicos
Grid interativo que mostra a versatilidade da engenharia:
-   **Status dos Projetos**:
    -   `ONLINE`: Projetos ativos e em produção.
    -   `LEGACY`: Projetos históricos que servem como referência técnica.
-   **Interação de Card**: Efeito de `glow` constante, com `blur` e texto "Acessar Portfólio" no hover.
-   **Expansão (Overlay)**: Ao clicar, abre um painel detalhado com descrição completa e **Navegação em Carrossel** (Seta pros lados para ver outros projetos sem fechar o modal).

---

## 📡 5. Radar Santis (Blog)
-   **Categorias**: Segurança e Otimização.
-   **Cards Inteligentes**: O grid é intercalado com anúncios de serviço (LGPD/Scanner).
-   **Compartilhamento**: Barra social fixa com Facebook, LinkedIn, WhatsApp e ferramentas (Imprimir/Copiar).

---

## 📩 6. Rodapé & Conversão
-   **QR Code**: Acesso direto ao WhatsApp oficial.
-   **Formulário Inteligente**: Capta Nome e Mensagem, enviando direto para o especialista via API do WhatsApp.
-   **Copyright Dinâmico**: Script que atualiza o ano automaticamente.

---
*Este documento é a referência oficial da Arquitetura Santis v2.*