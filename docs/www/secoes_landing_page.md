# Planeamento do Frontend - Landing Page Santis

Este documento detalha a estrutura visual e os componentes da página principal (OnePage) do site santis.net.br.

## 1. Identidade Visual (Design System)
* **Fundo Principal:** Azul Marinho Abissal (`#050A18`).
* **Cores de Destaque:** Ciano Neon (`#00F2FF`) e Púrpura Vibrante (`#8A2BE2`).
* **Tipografia:** * Títulos: Montserrat (Bold).
    * Texto/Código: JetBrains Mono.
* **Estilo UI:** Glassmorphism (efeito de vidro jateado) nos cards e Dark Mode Premium.

---

## 2. Seções da Página (Ordem de Leitura)

### 2.1 Hero Section (Abertura)
* **Visual:** Foto de Fernando Santis (Pose Peter Norton) à direita, com terno azul marinho e gravata púrpura.
* **Conteúdo:** * Headline: "O seu site é seguro? O seu Windows está otimizado?"
    * Subheadline: "Santis Engenharia Digital: Performance máxima e blindagem de dados para empresas e pessoas."
    * CTA Principal: Botão [INICIAR PROTOCOLO DE DEFESA] (Link para WhatsApp).

### 2.2 Scanner de Vulnerabilidades (A Isca)
* **Funcionalidade:** Campo interativo estilo terminal de comandos.
* **Texto:** "> Verifique vazamentos de dados: [ digite seu e-mail... ] [ SCAN ]"
* **Backend:** Integração futura com API HaveIBeenPwned.

### 2.3 Pilares de Serviço (Cards)
Três blocos com ícones estilizados e efeito hover neon:
1.  **Sites Seguros:** Landing Pages Mobile-First, conformidade LGPD e PHP otimizado.
2.  **Otimização Windows:** Limpeza técnica, remoção de bloatware e hardening de segurança (Win 10/11).
3.  **Media Tech & IA:** Edição de Reels/Shorts e criação de conteúdo com inteligência artificial.

### 2.4 Prova Técnica (Santis Control)
* **Visual:** Mockup do painel administrativo próprio.
* **Destaque:** "Não entregamos apenas um site, entregamos uma plataforma de gestão blindada."

---

## 3. Assets de Referência
As imagens brutas e mockups estão localizados na pasta raiz `/ref/`. 
A imagem oficial do Hero (Fernando Santis) deve ser processada para remover o fundo e adicionada em `public_html/assets/img/hero-santis.webp`.