/**
 * Santis Core API Client
 * Responsável por conectar o Front-End Estático (Vanilla JS) ao Backend Headless (API Painel).
 * Implementa padrão de Resiliência (Fallback Offline / Timeout).
 */

class SantisAPI {
    constructor() {
        // Auto-Discovery: Se estiver rodando offline/local, tenta apontar pro DDEV
        // Caso esteja na web real (Hostinger), usa caminhos absolutos ou relativos v1/
        this.apiBase = window.location.hostname.includes('santis.net.br')
            ? 'https://painel.santis.net.br/api/v1'
            : 'https://painel.santis.ddev.site/api/v1';

        this.timeoutLimit = 5000; // 5 segundos max antes do Fallback entrar em ação

        // Memória Cache (SessionStorage) para acelerar múltiplas visitas na mesma navegação
        this.useCache = true;
    }

    /**
     * Mestre Central Engine do Fetch Resiliente
     */
    async fetchWithFallback(endpoint, cacheKey, fallbackData) {

        // 1. First Layer: Check Cache
        if (this.useCache) {
            const cached = sessionStorage.getItem(`santis_${cacheKey}`);
            if (cached) {
                console.log(`[Santis] Cache Hit: ${cacheKey}`);
                return JSON.parse(cached);
            }
        }

        // 2. Second Layer: Network Attempt (With Abort Controller Timeout)
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.timeoutLimit);

        try {
            const response = await fetch(`${this.apiBase}/${endpoint}`, {
                method: 'GET',
                signal: controller.signal,
                headers: {
                    'Accept': 'application/json'
                }
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`HTTP Error: ${response.status}`);
            }

            const jsonResponse = await response.json();

            if (jsonResponse.success && jsonResponse.data) {
                // Traz resposta limpa e Guarda no Cache
                if (this.useCache) {
                    sessionStorage.setItem(`santis_${cacheKey}`, JSON.stringify(jsonResponse.data));
                }
                return jsonResponse.data;
            } else {
                throw new Error('Formato da API Headless Santis inesperado.');
            }

        } catch (error) {
            clearTimeout(timeoutId);
            console.warn(`[Santis] Falha Network em '${endpoint}': ${error.message}. Injetando Fallback Resiliente Offline.`);

            // 3. Third Layer: Offline Fallback Ignition
            return fallbackData;
        }
    }

    // --- Repositórios Específicos de Consumo --- //

    async getSettings() {
        const fallbacks = {
            slogan: "Soluções em <strong>Performance</strong> e <strong>Tecnologia.</strong>",
            contact_email: "contato@santis.eng.br"
        };
        return await this.fetchWithFallback('settings', 'settings', fallbacks);
    }

    async getPortfolio() {
        const fallbackCards = [
            { titulo: 'Advocacia V2', categoria: 'Online', capa: 'assets/img/advocacia_portfolio_v2.png', url_site: '#' },
            { titulo: 'Sistema Interno', categoria: 'Offline', capa: 'assets/img/santis_control_portfolio_v2.png', url_site: '#' },
            { titulo: 'Porto Run App', categoria: 'Online', capa: 'assets/img/portorun_portfolio_v2.png', url_site: '#' }
        ];
        return await this.fetchWithFallback('entries/portfolio', 'portfolio', fallbackCards);
    }

    async getBlog() {
        const fallbackNews = [
            {
                titulo: 'Nova API WebP em Ação',
                resumo: 'Velocidade recorde utilizando media manager independente.',
                slug: 'nova-api-webp',
                imagem: 'assets/img/system_optimization.png',
                target_url: '#'
            },
            {
                titulo: 'Proteção de Endpoints',
                resumo: 'Como blindamos requisições públicas de ataques.',
                slug: 'protecao-endpoints',
                imagem: 'assets/img/web_presence.png',
                target_url: '#'
            }
        ];
        return await this.fetchWithFallback('entries/blog', 'blog', fallbackNews);
    }
}

// Inicializa globalmente para as funções Vanilla de UI utilizarem
window.santisApi = new SantisAPI();
