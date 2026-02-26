/**
 * Santis Core Hydration System
 * Responsável por injetar de forma transparente os dados do Backend (Headless) no DOM Estático.
 * Utiliza o SantisAPI (Fallback Resiliente) desenhado na base core.
 */

document.addEventListener('DOMContentLoaded', async () => {

    // 1. Instância Global do Client
    const api = window.santisApi;
    if (!api) {
        console.error("[Santis] Classe SantisAPI não carregada no escopo Window.");
        return;
    }

    try {
        // --- A. HIDRATAÇÃO DE CONFIGURAÇÕES GLOBAIS (Settings) ---
        const settings = await api.getSettings();

        // Slogan Principal (Respeitando tags strong do Painel)
        const elSlogan = document.getElementById('hero-slogan');
        if (elSlogan && settings.slogan) {
            elSlogan.innerHTML = settings.slogan;
        }

        // Email do Rodapé
        const elFooterEmail = document.getElementById('footer-email');
        if (elFooterEmail && settings.contact_email) {
            elFooterEmail.href = `mailto:${settings.contact_email}`;
            elFooterEmail.textContent = settings.contact_email;
        }

        // --- B. HIDRATAÇÃO DE PORTFÓLIO (EAV Dynamics) ---
        const portfolioData = await api.getPortfolio();
        const portfolioContainer = document.querySelector('.portfolio-grid');

        if (portfolioContainer && portfolioData && portfolioData.length > 0) {
            // Limpa o grid estrutural estático (Opcional, ou manter misturado com o JS de load more original)
            // Aqui, a prioridade é a hidratação tática dos dados aprovados no Brainstorm
            // Em fases futuras, o componente portfolio.js assume a paginação

            // Ajustar o contador
            const totalCases = document.getElementById('portfolio-total-cases');
            if (totalCases) {
                totalCases.textContent = String(portfolioData.length).padStart(2, '0');
            }
        }

        // --- C. HIDRATAÇÃO DE BLOG / RADAR (EAV Dynamics) ---
        const blogData = await api.getBlog();
        const radarContainer = document.getElementById('radar-grid');

        if (radarContainer && blogData && blogData.length > 0) {
            // A renderização completa dos cards do blog é feita no motor específico radar.js
            // Aqui apenas garantimos que os dados chegaram resilientes no window.
            window.SantisPosts = blogData;
        }

        console.log("[Santis] Hydration System ativado e finalizado com sucesso.");

    } catch (error) {
        console.error("[Santis] Erro Crítico de Hydration:", error);
    }
});
