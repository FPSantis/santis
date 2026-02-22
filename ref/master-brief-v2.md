# 🏛️ Santis Engenharia Digital - Master Brief V2

## 🎯 Posicionamento de Marca
- **Pilar Central:** Transição do estigma "Hacker/Cyberpunk" para "Engenharia Digital Premium".
- **Foco:** Luxo técnico, Performance extrema e Blindagem Corporativa (LGPD).
- **Slogan Sugerido:** "Transformando tecnologia em segurança e resultados."

## 🎨 Identidade Visual (UI/UX)
- **Cores:** - Fundo: Azul Marinho Abissal (#050A18).
  - Acentos: Ciano Neon (#00F2FF) e Púrpura Vibrante (#8A2BE2) para glows e bordas finas.
- **Tipografia:** - Títulos e Menus: Montserrat (Clean e imponente).
  - Corpo: Sans-serif nativa moderna (evitar JetBrains Mono para fugir do visual de terminal).
- **Estilo:** Glassmorphism suave (efeito jateado), bordas de 1px e iluminação de fundo para profundidade.

## 🧱 Arquitetura da OnePage (Desktop & Mobile First)

1. **Header:** Menu minimalista com transparência (Glassmorphism). No mobile, ícone hambúrguer com overlay de tela cheia.
2. **Hero:** Foto executiva (Pose Peter Norton) à direita. Headline editorial à esquerda.
3. **Radar Scanner:** Ferramenta interativa com animação de radar aeronáutico. Simula diagnóstico de vulnerabilidades e performance.
4. **Bento Grid (Serviços):** - **Card A (Sites):** Foco em Landing Pages Mobile-First, conversão e conformidade implícita com LGPD.
   - **Card B (Windows):** Conceito "PC Doctor". Otimização, limpeza e performance sem focar inicialmente em formatação.
5. **Santis Control:** Prova de autoridade exibindo o mockup do painel administrativo proprietário (Sneat Pro adaptado).
6. **Portfólio de Destaque:** Grid de 2 colunas (Desktop). Itens expansíveis que revelam detalhes técnicos e status do domínio.
7. **Radar Santis (Blog):** Layout Masonry com notícias reais de segurança e performance para gerar autoridade de "vigília".
8. **O Xeque-Mate:** Seção de urgência focada em segurança de dados e riscos de multas da LGPD.
9. **Parcerias:** Barra discreta com logos monocromáticos (AWS, Cloudflare, Norton, Hostinger).
10. **Rodapé:** Assinatura "Feito com ♥ por Fernando Santis" em design extremamente clean.

## 🛠️ Especificações Técnicas
- **Ambiente:** WSL2 + DDEV (PHP 8.4 + MariaDB).
- **Frontend:** Tailwind CSS (via CDN para preview, compilado para produção).
- **Backend Final:** PHP 8.4 nativo, Bramus Router, Twig Templates e Dotenv.
- **Assets:** Imagens padronizadas como `logo-santis.svg` e `hero-santis.png` na pasta `assets/img/`.

---
*Documento atualizado em: 21 de Fevereiro de 2026.*