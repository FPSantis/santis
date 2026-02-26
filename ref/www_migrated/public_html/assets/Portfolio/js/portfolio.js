const portfolioData = [
    {
        id: 1,
        title: "PortoRun",
        url: "portorun.com.br",
        category: "Eventos & Esporte",
        status: "ONLINE",
        image: "https://cdn.santis.ddev.site/portfolio/2026/02/portorun_portfolio_v2.png",
        fullDesc: "Sistema completo de inscrições e gestão de cronometragem. Desenvolvido em PHP 8.4 com foco em alta carga de acessos e segurança MySQL."
    },
    {
        id: 2,
        title: "Advocacia Santis",
        url: "advocaciasantis.com.br",
        category: "Landing Page Pro",
        status: "LEGACY",
        image: "https://cdn.santis.ddev.site/portfolio/2026/02/advocacia_portfolio_v2.png",
        fullDesc: "Página focada em conversão jurídica com blindagem de dados sensíveis e total conformidade com as normas da LGPD."
    },
    {
        id: 3,
        title: "Santis Control",
        url: "demo.santis.eng.br",
        category: "SaaS / Admin",
        status: "ONLINE",
        image: "https://cdn.santis.ddev.site/portfolio/2026/02/santis_control_portfolio_v2.png",
        fullDesc: "Nosso dashboard proprietário. Uma interface limpa e segura para que o cliente gerencie leads e veja a saúde do seu site."
    },
    {
        id: 4,
        title: "TechFlow",
        url: "techflow.io",
        category: "Produtividade",
        status: "ONLINE",
        image: "https://cdn.santis.ddev.site/portfolio/2026/02/portorun_portfolio_v2.png",
        fullDesc: "Ferramenta de fluxo de trabalho para equipes de engenharia, integrando APIs de terceiros para automação de tarefas."
    },
    {
        id: 5,
        title: "GreenEnergy",
        url: "greenenergy.eco",
        category: "Sustentabilidade",
        status: "ONLINE",
        image: "https://cdn.santis.ddev.site/portfolio/2026/02/advocacia_portfolio_v2.png",
        fullDesc: "Portal de monitoramento de energia renovável com gráficos em tempo real e relatórios de eficiência energética."
    },
    {
        id: 6,
        title: "SmartCity App",
        url: "smartcity.gov",
        category: "Setor Público",
        status: "ONLINE",
        image: "https://cdn.santis.ddev.site/portfolio/2026/02/santis_control_portfolio_v2.png",
        fullDesc: "Aplicativo para gestão de serviços urbanos, permitindo que cidadãos reportem problemas e acompanhem resoluções."
    }
];

const gridContainer = document.querySelector('.portfolio-grid');
const detailsArea = document.getElementById('portfolio-details');
const loadMoreBtn = document.getElementById('load-more-btn');
const totalCasesCounter = document.getElementById('portfolio-total-cases');

let itemsVisible = 0;
const itemsPerLoad = 3;

function renderPortfolio() {
    const nextItems = portfolioData.slice(itemsVisible, itemsVisible + itemsPerLoad);

    nextItems.forEach(item => {
        const card = `
            <div class="portfolio-item glass-card group overflow-hidden border-[#00F2FF]/30 portfolio-glow-active" onclick="expandProject(${item.id})">
                <div class="relative aspect-video bg-slate-950 border-b border-white/5 overflow-hidden">
                    <img src="${item.image}" alt="${item.title}" class="w-full h-full object-cover opacity-60 group-hover:scale-105 group-hover:opacity-40 transition-all duration-700">
                    
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 backdrop-blur-sm bg-black/40 transition-all duration-500">
                        <div class="text-center">
                            <span class="text-white text-[10px] font-black uppercase tracking-[0.4em] mb-2 block">Acessar Portfólio</span>
                            <div class="h-px w-12 bg-[#00F2FF] mx-auto"></div>
                        </div>
                    </div>

                    <div class="absolute top-4 left-4 z-10">
                        <span class="bg-black/80 backdrop-blur-md px-3 py-1 rounded text-[8px] font-black uppercase tracking-[3px] ${item.status === 'ONLINE' ? 'text-[#00F2FF] border-[#00F2FF]/20' : 'text-gray-500 border-white/10'} border">${item.status}</span>
                    </div>
                </div>
                <div class="p-8">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[#00F2FF] text-[9px] uppercase tracking-[0.2em] font-black">${item.category}</span>
                        <span class="text-gray-600 font-bold text-[9px] uppercase tracking-widest">ID: 00${item.id}</span>
                    </div>
                    <h4 class="text-2xl font-black Montserrat text-white leading-tight uppercase tracking-tighter">${item.title}</h4>
                    <p class="text-gray-500 text-[10px] mt-4 font-bold tracking-widest uppercase italic">${item.url}</p>
                </div>
            </div>
        `;
        gridContainer.insertAdjacentHTML('beforeend', card);
    });

    itemsVisible += nextItems.length;

    // Update counter
    if (totalCasesCounter) {
        totalCasesCounter.textContent = itemsVisible.toString().padStart(2, '0');
    }

    if (itemsVisible >= portfolioData.length) {
        loadMoreBtn.style.display = 'none';
    }
}

function expandProject(id) {
    const projectIndex = portfolioData.findIndex(p => p.id === id);
    const project = portfolioData[projectIndex];

    // Circular navigation
    const prevId = portfolioData[(projectIndex - 1 + portfolioData.length) % portfolioData.length].id;
    const nextId = portfolioData[(projectIndex + 1) % portfolioData.length].id;

    detailsArea.innerHTML = `
        <div class="expanded-project-container relative">
            <!-- Navigation Arrows (Slider Style) -->
            <button onclick="expandProject(${prevId})" class="nav-arrow-side nav-arrow-left" title="Anterior">
                <i class="bi bi-chevron-left"></i>
            </button>
            <button onclick="expandProject(${nextId})" class="nav-arrow-side nav-arrow-right" title="Próximo">
                <i class="bi bi-chevron-right"></i>
            </button>

            <div class="glass-card p-10 md:p-20 border-[#00F2FF]/20 relative overflow-hidden bg-gradient-to-br from-[#00F2FF]/5 to-transparent">
                <div class="absolute top-0 right-0 w-64 h-64 bg-[#00F2FF]/5 blur-[100px] -mr-32 -mt-32"></div>
                
                <div class="flex justify-between items-center mb-12">
                     <span class="text-[10px] font-black text-[#00F2FF]/40 tracking-[0.5em] uppercase">Projeto ${projectIndex + 1} / ${portfolioData.length}</span>
                     <button onclick="closeDetails()" class="text-gray-500 hover:text-white uppercase text-[10px] font-black tracking-widest flex items-center gap-2 group">
                        <span class="w-8 h-px bg-gray-800 group-hover:bg-white transition-all"></span> 
                        Fechar
                    </button>
                </div>

                <div class="grid lg:grid-cols-12 gap-20 items-center">
                    <div class="lg:col-span-7">
                        <div class="relative group">
                            <div class="absolute -inset-2 bg-white/5 blur-xl opacity-0 group-hover:opacity-100 transition"></div>
                            <div class="relative detail-media aspect-video flex items-center justify-center italic text-gray-600 bg-slate-950 rounded-2xl border border-white/5 shadow-2xl overflow-hidden">
                                <img src="${project.image}" alt="${project.title}" class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-5">
                        <div class="text-[#00F2FF] text-[10px] font-black uppercase tracking-[0.3em] mb-4">${project.category}</div>
                        <h3 class="text-5xl font-black Montserrat text-white mb-8 uppercase tracking-tighter">${project.title}</h3>
                        <p class="text-gray-400 font-light leading-relaxed mb-12 text-xl italic border-l-2 border-[#00F2FF]/20 pl-8">${project.fullDesc}</p>
                        <div class="flex flex-col sm:flex-row gap-6">
                            <a href="https://${project.url}" target="_blank" class="bg-white text-black px-12 py-4 rounded-full font-black text-xs uppercase tracking-widest text-center hover:bg-[#00F2FF] transition scale-100 hover:scale-105">Deploy Ativo</a>
                            <div class="flex flex-col justify-center">
                                <span class="text-gray-600 text-[9px] uppercase tracking-widest font-bold">Acessar Projeto</span>
                                <span class="text-white text-xs font-medium tracking-wider font-mono">https://${project.url}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    detailsArea.style.display = 'block';

    // Scroll directly to the details area top
    detailsArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function closeDetails() {
    detailsArea.style.display = 'none';
    document.getElementById('portfolio').scrollIntoView({ behavior: 'smooth' });
}

loadMoreBtn.addEventListener('click', renderPortfolio);

// Initialize
renderPortfolio();