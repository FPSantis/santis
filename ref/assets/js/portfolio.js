const portfolioData = [
    {
        id: 1,
        title: "PortoRun",
        url: "portorun.com.br",
        category: "Eventos & Esporte",
        fullDesc: "Sistema completo de inscrições e gestão de cronometragem. Desenvolvido em PHP 8.4 com foco em alta carga de acessos e segurança MySQL."
    },
    {
        id: 2,
        title: "Advocacia Santis",
        url: "advocaciasantis.com.br",
        category: "Landing Page Pro",
        fullDesc: "Página focada em conversão jurídica com blindagem de dados sensíveis e total conformidade com as normas da LGPD."
    },
    {
        id: 3,
        title: "Santis Control",
        url: "demo.santis.eng.br",
        category: "SaaS / Admin",
        fullDesc: "Nosso dashboard proprietário. Uma interface limpa e segura para que o cliente gerencie leads e veja a saúde do seu site."
    }
];

const gridContainer = document.querySelector('.portfolio-grid');
const detailsArea = document.getElementById('portfolio-details');

function initPortfolio() {
    portfolioData.forEach(item => {
        const card = `
            <div class="portfolio-item glass-card group overflow-hidden border-white/5 hover:border-[#00F2FF]/30" onclick="expandProject(${item.id})">
                <div class="relative aspect-video bg-slate-950 flex items-center justify-center text-xs text-gray-700 italic border-b border-white/5 overflow-hidden">
                    <img src="https://via.placeholder.com/600x400/050A18/00F2FF?text=${item.title}" alt="${item.title}" class="w-full h-full object-cover opacity-50 group-hover:scale-110 group-hover:opacity-80 transition duration-1000">
                    <div class="absolute top-4 left-4">
                        <span class="bg-black/80 backdrop-blur-md px-3 py-1 rounded text-[8px] font-black uppercase tracking-[3px] text-[#00F2FF] border border-[#00F2FF]/20">STABLE</span>
                    </div>
                </div>
                <div class="p-8">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-[#00F2FF] text-[9px] uppercase tracking-[0.2em] font-black">${item.category}</span>
                        <span class="text-gray-600 font-bold text-[9px] uppercase tracking-widest">ID: 00${item.id}</span>
                    </div>
                    <h4 class="text-2xl font-black Montserrat text-white leading-tight uppercase tracking-tighter">${item.title}</h4>
                    <p class="text-gray-500 text-[10px] mt-4 font-bold tracking-widest uppercase italic">${item.url}</p>
                    <div class="mt-8 flex justify-between items-center opacity-0 group-hover:opacity-100 transition duration-500">
                        <span class="text-[9px] font-black uppercase tracking-[2px]">Acessar Protocolo</span>
                        <span class="text-[#00F2FF]">→</span>
                    </div>
                </div>
            </div>
        `;
        gridContainer.insertAdjacentHTML('beforeend', card);
    });
}

function expandProject(id) {
    const project = portfolioData.find(p => p.id === id);

    detailsArea.innerHTML = `
        <div class="glass-card p-10 md:p-20 border-[#00F2FF]/20 relative overflow-hidden bg-gradient-to-br from-[#00F2FF]/5 to-transparent">
            <div class="absolute top-0 right-0 w-64 h-64 bg-[#00F2FF]/5 blur-[100px] -mr-32 -mt-32"></div>
            <button onclick="closeDetails()" class="absolute top-8 right-10 text-gray-500 hover:text-white uppercase text-[10px] font-black tracking-widest flex items-center gap-2 group">
                <span class="w-8 h-px bg-gray-800 group-hover:bg-white transition-all"></span> 
                Fechar Protocolo
            </button>
            <div class="grid lg:grid-cols-12 gap-20 items-center">
                <div class="lg:col-span-7">
                    <div class="relative group">
                        <div class="absolute -inset-2 bg-white/5 blur-xl opacity-0 group-hover:opacity-100 transition"></div>
                        <div class="relative detail-media aspect-video flex items-center justify-center italic text-gray-600 bg-slate-950 rounded-2xl border border-white/5 shadow-2xl overflow-hidden">
                            <img src="https://via.placeholder.com/1280x720/050A18/00F2FF?text=${project.title}+Case+Study" alt="${project.title}" class="w-full h-full object-cover opacity-60">
                            <div class="absolute inset-0 flex items-center justify-center font-black uppercase tracking-[5px] text-[#00F2FF]/20">Active Domain</div>
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
                            <span class="text-gray-600 text-[9px] uppercase tracking-widest font-bold">Protocolo de Comunicação</span>
                            <span class="text-white text-xs font-medium tracking-wider font-mono">https://${project.url}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    detailsArea.style.display = 'block';
    detailsArea.scrollIntoView({ behavior: 'smooth' });
}

function closeDetails() {
    detailsArea.style.display = 'none';
}

initPortfolio();