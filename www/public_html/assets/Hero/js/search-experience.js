/**
 * Santis Vulnerability Search Experience Logic
 */

document.addEventListener('DOMContentLoaded', () => {
    const searchForm = document.querySelector('.precision-scanner-bar');
    const searchBtn = document.querySelector('.precision-scanner-btn');
    const searchInput = document.querySelector('.precision-scanner-input');

    // Create Modal HTML and inject it if not present
    if (!document.getElementById('searchExperienceModal')) {
        injectSearchModal();
    }

    const modal = document.getElementById('searchExperienceModal');
    const closeBtn = modal.querySelector('.search-modal-close');
    const scanArea = modal.querySelector('.scanning-phase');
    const resultsArea = modal.querySelector('.search-results-area');
    const checklistItems = modal.querySelectorAll('.checklist-item');

    searchBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const query = searchInput.value.trim();
        if (query) {
            startSearchExperience(query);
        }
    });

    // Handle URL-based auto-scan
    const urlParams = new URLSearchParams(window.location.search);
    const autoScanQuery = urlParams.get('scan');
    if (autoScanQuery) {
        // Small delay to ensure everything is ready
        setTimeout(() => {
            startSearchExperience(autoScanQuery);
            // Clean URL without refresh
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 500);
    }

    closeBtn.addEventListener('click', () => {
        modal.classList.remove('active');
        // Reset state for next time
        setTimeout(resetModal, 500);
    });

    // Close modal when contact CTA is clicked
    modal.addEventListener('click', (e) => {
        if (e.target.closest('.search-modal-close-trigger')) {
            modal.classList.remove('active');
            setTimeout(resetModal, 500);
        }
    });

    function startSearchExperience(query) {
        modal.classList.add('active');
        scanArea.style.display = 'block';
        resultsArea.classList.remove('active');

        simulateScanning(query);
    }

    async function simulateScanning(query) {
        // Sequentially activate checklist items
        for (let i = 0; i < checklistItems.length; i++) {
            const item = checklistItems[i];
            item.classList.add('active');

            // Random delay between 600ms and 1200ms
            await new Promise(r => setTimeout(r, 600 + Math.random() * 600));

            item.classList.add('completed');
        }

        // Delay before showing results
        await new Promise(r => setTimeout(r, 800));

        showResults(query);
    }

    function showResults(query) {
        scanArea.style.display = 'none';
        resultsArea.classList.add('active');

        // Logic to determine "Found" vs "Not Found"
        // For demo: if query contains "clean", show "Not Found"
        const isClean = query.toLowerCase().includes('clean');

        const successContent = resultsArea.querySelector('.results-success');
        const emptyContent = resultsArea.querySelector('.results-empty');

        if (isClean) {
            successContent.style.display = 'none';
            emptyContent.style.display = 'block';
        } else {
            successContent.style.display = 'block';
            emptyContent.style.display = 'none';
            // Trigger score animation
            const riskLevel = modal.querySelector('.risk-level');
            setTimeout(() => riskLevel.style.width = '75%', 100);
        }
    }

    function resetModal() {
        checklistItems.forEach(item => {
            item.classList.remove('active', 'completed');
        });
        const riskLevel = modal.querySelector('.risk-level');
        riskLevel.style.width = '0%';
    }

    function injectSearchModal() {
        const modalHTML = `
        <div id="searchExperienceModal" class="search-modal-backdrop">
            <div class="search-modal-content glass-card p-10 border-[#00F2FF]/20">
                <button class="search-modal-close">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <!-- Phase 1: Scanning -->
                <div class="scanning-phase text-center py-10">
                    <div class="radar-container mb-10">
                        <div class="radar-sweep"></div>
                        <div class="radar-ping" style="top: 30%; left: 40%"></div>
                        <div class="radar-ping" style="top: 60%; left: 70%"></div>
                        <img src="${window.URL_BASE || '/'}assets/img/logo-santis.svg" class="w-12 h-12 relative z-10 opacity-50" alt="Santis">
                    </div>
                    
                    <h3 class="text-3xl font-black Montserrat mb-4 uppercase tracking-tighter">
                        Executando Protocolo de <span class="text-[#00F2FF]">Varredura</span>
                    </h3>
                    <p class="text-gray-400 mb-12">Consultando bases globais em tempo real...</p>

                    <div class="max-w-md mx-auto text-left space-y-2">
                        <div class="checklist-item">
                            <div class="check-icon">✓</div>
                            <span class="text-sm font-bold Montserrat uppercase tracking-widest">Iniciando Injeção de Scan</span>
                        </div>
                        <div class="checklist-item">
                            <div class="check-icon">✓</div>
                            <span class="text-sm font-bold Montserrat uppercase tracking-widest">Acessando API HaveIBeenPwned</span>
                        </div>
                        <div class="checklist-item">
                            <div class="check-icon">✓</div>
                            <span class="text-sm font-bold Montserrat uppercase tracking-widest">Mapeando Timeline de Incidentes</span>
                        </div>
                        <div class="checklist-item">
                            <div class="check-icon">✓</div>
                            <span class="text-sm font-bold Montserrat uppercase tracking-widest">Calculando Vetor de Risco Geral</span>
                        </div>
                    </div>
                </div>

                <!-- Phase 2: Results -->
                <div class="search-results-area">
                    <!-- Case: Found -->
                    <div class="results-success">
                        <div class="flex flex-col md:flex-row gap-10 items-start">
                            <div class="w-full md:w-1/3 text-center">
                                <div class="mb-6 p-6 rounded-full border-4 border-red-500/20 inline-block">
                                    <span class="text-5xl font-black Montserrat text-red-500">75%</span>
                                </div>
                                <h4 class="font-black Montserrat uppercase tracking-widest text-sm mb-4">Vetor de Risco</h4>
                                <div class="risk-meter mb-2">
                                    <div class="risk-level state-critical"></div>
                                </div>
                                <p class="text-[10px] text-gray-500 uppercase tracking-widest">Nível de Ameaça: Crítico</p>
                            </div>

                            <div class="w-full md:w-2/3">
                                <h3 class="text-2xl font-black Montserrat mb-6 uppercase tracking-tight">Incidentes <span class="text-red-500">Localizados</span></h3>
                                
                                <div class="leak-timeline mb-10">
                                    <div class="leak-event">
                                        <div class="text-[10px] font-black text-[#00F2FF] uppercase tracking-widest mb-1">Janeiro 2024</div>
                                        <h5 class="font-bold text-white mb-1">Vazamento LinkedIn (Data Sync)</h5>
                                        <p class="text-sm text-gray-500">Dados expostos: E-mail, Senhas Hasheadas, Cargos.</p>
                                    </div>
                                    <div class="leak-event">
                                        <div class="text-[10px] font-black text-[#00F2FF] uppercase tracking-widest mb-1">Agosto 2023</div>
                                        <h5 class="font-bold text-white mb-1">E-commerce Global Breach</h5>
                                        <p class="text-sm text-gray-500">Dados expostos: E-mail, Endereço, Histórico de Compras.</p>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-4">
                                    <button class="bg-white text-black px-6 py-3 rounded-full font-black text-[10px] uppercase tracking-[2px] transition hover:scale-105">
                                        Baixar Relatório PDF
                                    </button>
                                    <a href="#contato" class="search-modal-close-trigger border border-[#00F2FF]/30 text-[#00F2FF] px-6 py-3 rounded-full font-black text-[10px] uppercase tracking-[2px] transition hover:bg-[#00F2FF]/10">
                                        Resolver com Especialista
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Case: Not Found -->
                    <div class="results-empty" style="display: none;">
                        <div class="text-center py-10">
                            <div class="w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-8 border border-green-500/30">
                                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <h3 class="text-4xl font-black Montserrat mb-4 uppercase tracking-tighter italic">
                                "Ainda Não" <span class="text-green-500">foram encontrados vazamentos</span>
                            </h3>
                            <p class="text-gray-400 mb-10 max-w-lg mx-auto">
                                Seu e-mail ou domínio não consta nas bases de dados de incidentes monitoradas. Isso é um bom sinal, mas a prevenção é contínua.
                            </p>
                            <a href="#services" class="text-[#00F2FF] font-black text-[10px] uppercase tracking-[3px] border-b border-[#00F2FF]/30 pb-2 hover:border-[#00F2FF] transition">
                                SAIBA COMO MANTER A BLINDAGEM
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }
});
