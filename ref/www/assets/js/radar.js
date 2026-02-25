const radarGrid = document.getElementById('radar-grid');
const btnLoadMore = document.getElementById('load-more-blog');
const blogTitle = document.getElementById('blog-title');
const blogDescription = document.getElementById('blog-description');
const blogPretitle = document.getElementById('blog-pretitle');

let currentDisplayedCount = 0;
const IS_BLOG_PAGE = window.location.pathname.includes('blog.html');
const POSTS_PER_PAGE = IS_BLOG_PAGE ? 9 : 3;

// Read URL Filter
const urlParams = new URLSearchParams(window.location.search);
const activeFilter = urlParams.get('filter');

// Filter source data if needed
let filteredPosts = activeFilter
    ? blogPosts.filter(p => p.category.toLowerCase() === activeFilter.toLowerCase())
    : blogPosts;

const CTA_HIBP = `
    <article class="filler-card group border-none bg-gradient-to-br from-[#050A18] via-[#050A18] to-[#8A2BE2] !p-12 overflow-hidden relative flex flex-col justify-center h-full shadow-[0_30px_100px_-20px_rgba(5,10,24,0.8)]">
        <!-- Airport Radar Animation Background -->
        <div class="absolute inset-0 z-0 opacity-20 pointer-events-none">
            <div class="radar-sweep-line"></div>
            <div class="radar-circle radar-circle-1"></div>
            <div class="radar-circle radar-circle-2"></div>
            <div class="radar-circle radar-circle-3"></div>
            <!-- Animated Pings (Data Leaks) -->
            <div class="radar-ping" style="top: 30%; left: 40%; animation-delay: 0.5s"></div>
            <div class="radar-ping" style="top: 15%; left: 70%; animation-delay: 1.2s"></div>
            <div class="radar-ping" style="top: 60%; left: 30%; animation-delay: 2.8s"></div>
            <div class="radar-ping" style="top: 80%; left: 60%; animation-delay: 4.1s"></div>
        </div>
        
        <div class="relative z-10 flex flex-col h-full">
            <div class="inline-block px-4 py-1 rounded-full bg-[#00F2FF]/10 text-[#00F2FF] text-[9px] font-black uppercase tracking-widest mb-8 border border-[#00F2FF]/20 w-fit">
                Scanner Global Live
            </div>
            <h3 class="text-3xl md:text-4xl font-black Montserrat uppercase tracking-tighter mb-6 text-white leading-[1.1] italic pr-10">Seus dados estão <br><span class="text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] to-[#8A2BE2]">Seguros?</span></h3>
            <p class="text-gray-300 text-sm font-light mb-10 leading-relaxed max-w-[300px]">Varredura técnica profunda em bases de vazamentos globais. O protocolo Santis detecta falhas em milissegundos.</p>
            
            <div class="mt-auto">
                <form action="index.html" method="GET" class="relative group/input">
                    <div class="relative mb-6">
                        <input type="email" name="scan" placeholder="Digite seu e-mail ou domínio..." required 
                            class="w-full bg-white/10 border border-white/20 rounded-xl px-6 py-5 text-sm font-bold text-white focus:outline-none focus:border-[#00F2FF] transition-all placeholder:text-white/30">
                        <i class="bi bi-broadcast absolute right-6 top-1/2 -translate-y-1/2 text-[#00F2FF]/40"></i>
                    </div>
                    <button type="submit" class="w-full py-5 bg-gradient-to-r from-[#00F2FF] to-[#8A2BE2] text-[#050A18] rounded-full text-[10px] font-black uppercase tracking-[4px] hover:scale-[1.05] transition-all active:scale-95 shadow-[0_0_40px_rgba(0,242,255,0.3)]">
                        Verificar
                    </button>
                </form>
            </div>
            <p class="mt-8 text-[9px] text-white/40 font-black uppercase tracking-widest italic flex items-center gap-2">
                <i class="bi bi-shield-fill-check"></i>
                Powered by Have I Been Pwned
            </p>
        </div>
    </article>
`;

const CTA_LGPD = `
    <article class="filler-card glass-card group border-[#8A2BE2]/50 !p-0 overflow-hidden relative flex flex-col justify-end h-full shadow-[0_30px_80px_-20px_rgba(138,43,226,0.4)]">
        <img src="assets/img/santis_control.png" class="absolute inset-0 w-full h-full object-cover opacity-30 grayscale group-hover:grayscale-0 group-hover:opacity-50 transition-all duration-1000">
        <div class="absolute inset-0 bg-gradient-to-t from-[#8A2BE2]/90 via-[#050A18]/90 to-transparent"></div>
        
        <div class="relative z-10 p-12 flex flex-col h-full justify-end">
            <div class="inline-block px-4 py-1 rounded-full bg-red-500/10 text-red-500 text-[9px] font-black uppercase tracking-widest mb-8 border border-red-500/20 w-fit">
                Alerta de Risco LGPD
            </div>
            <h3 class="text-3xl md:text-4xl font-black Montserrat mb-8 leading-[1.1] uppercase tracking-tighter italic text-white pr-10">
                "O seu site protege os dados ou é uma <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-[#8A2BE2]">porta aberta para multas?</span>"
            </h3>
            <p class="text-gray-200 text-sm font-light mb-10 leading-relaxed">
                A segurança da informação não é um custo, é a blindagem do seu patrimônio digital. No cenário atual, a <span class="text-white font-medium">invisibilidade técnica</span> é sua maior vulnerabilidade.
            </p>
            
            <a href="#contato" class="bg-gradient-to-r from-[#00F2FF] to-[#8A2BE2] text-[#050A18] px-10 py-5 rounded-full font-black text-[10px] uppercase text-center tracking-[3px] hover:scale-105 transition-all duration-500 shadow-[0_0_40px_rgba(0,242,255,0.3)]">
                Falar com um Especialista
            </a>
        </div>
    </article>
`;

let ctaToggle = false;

function renderBlogGrid() {
    if (!radarGrid) return;

    // Logic for Home Page (limit 3) vs Blog Page (pagination)
    const limit = IS_BLOG_PAGE ? (currentDisplayedCount + POSTS_PER_PAGE) : 3;
    const batch = filteredPosts.slice(currentDisplayedCount, limit);

    batch.forEach((post, index) => {
        // Inject CTA every 3 items in Blog Page
        if (IS_BLOG_PAGE && index > 0 && index % 3 === 0) {
            radarGrid.insertAdjacentHTML('beforeend', ctaToggle ? CTA_HIBP : CTA_LGPD);
            ctaToggle = !ctaToggle;
        }

        const cleanTitle = post.title.replace(/<[^>]*>/g, '');
        const displayTitle = IS_BLOG_PAGE ? post.title : cleanTitle;

        const imageHtml = post.image ? `
            <div class="relative aspect-[16/10] overflow-hidden border-b border-white/5">
                <img src="${post.image}" alt="${cleanTitle}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 opacity-70 group-hover:opacity-100">
                <div class="absolute inset-0 bg-gradient-to-t from-[#050A18] to-transparent opacity-60"></div>
                <div class="absolute top-4 right-4 z-10">
                    <div class="bg-[#050A18]/80 backdrop-blur-md border border-white/10 rounded-xl p-2 text-center min-w-[50px] bg-badge-date transition-all">
                        <span class="block text-[10px] font-black leading-none text-[#00F2FF]">${post.date.split(' ')[0]}</span>
                        <span class="block text-[8px] font-bold uppercase text-gray-500 mt-1">${post.date.split(' ')[1]}</span>
                    </div>
                </div>
            </div>
        ` : '';

        const postCard = `
            <article class="news-card glass-card glass-card-purple overflow-hidden group !border-white/10 cursor-pointer relative" onclick="openPost(${post.id})">
                ${imageHtml}
                <div class="p-8">
                    <div class="flex justify-between items-center mb-6">
                        <a href="blog.html?filter=${post.category}" onclick="event.stopPropagation()" class="px-3 py-1 rounded bg-[#8A2BE2]/10 text-[#8A2BE2] text-[9px] font-black uppercase tracking-widest border border-[#8A2BE2]/20 hover:bg-[#8A2BE2] hover:text-white transition-all">${post.category}</a>
                        <span class="text-[9px] text-gray-600 font-bold uppercase tracking-widest italic">${post.readTime} Leitura</span>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black Montserrat mb-6 group-hover:text-[#00F2FF] transition leading-tight uppercase tracking-tighter">
                        ${displayTitle}
                    </h3>
                    <p class="text-gray-400 text-sm font-light leading-relaxed mb-8 line-clamp-4">
                        ${post.summary}
                    </p>
                    <div class="flex items-center gap-2 text-[#8A2BE2] text-[10px] font-black uppercase tracking-[3px] group/link mt-auto">
                        Ler Artigo
                        <i class="bi bi-arrow-right transition-transform group-hover/link:translate-x-2"></i>
                    </div>
                </div>
            </article>
        `;
        radarGrid.insertAdjacentHTML('beforeend', postCard);
    });

    currentDisplayedCount += batch.length;

    // Handle Home Page Fillers (only on home)
    if (!IS_BLOG_PAGE) {
        updateFillers();
    }

    // Pagination Visibility
    if (btnLoadMore) {
        if (currentDisplayedCount >= filteredPosts.length) {
            btnLoadMore.style.display = 'none';
        } else {
            btnLoadMore.style.display = 'inline-block';
        }
    }
}

function updateFillers() {
    if (IS_BLOG_PAGE) return;

    document.querySelectorAll('.filler-card').forEach(f => f.remove());

    // Strict Home check: if we rendered less than 3, add fillers to complete the single row
    const remainder = currentDisplayedCount % 3;

    if (remainder !== 0) {
        const slotsNeeded = 3 - remainder;

        if (slotsNeeded === 2) {
            const filler = `
                <div class="filler-card filler-2-cols glass-card group cursor-pointer" onclick="document.getElementById('radar').scrollIntoView({behavior:'smooth'})">
                    <img src="assets/img/hero-santis.png" alt="Scanner">
                    <div class="relative z-10">
                        <div class="badge-highlight mb-4">Ferramenta Santis</div>
                        <h3 class="text-4xl font-black Montserrat uppercase tracking-tighter mb-4">Realize uma <span class="text-[#00F2FF]">Varredura Profissional</span> em seu site agora.</h3>
                        <p class="text-gray-400 font-light mb-8 max-w-xl">Identifique falhas críticas e vazamentos de dados com nosso protocolo de inteligência.</p>
                        <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-[3px] text-[#00F2FF]">
                            Acessar Scanner de Elite
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                    </div>
                </div>
            `;
            radarGrid.insertAdjacentHTML('beforeend', filler);
        } else if (slotsNeeded === 1) {
            const filler = `
                <div class="filler-card glass-card group cursor-pointer border-[#8A2BE2]/30 bg-[#8A2BE2]/5" onclick="window.location.href='post.html?id=1'">
                    <div class="relative z-10 h-full flex flex-col justify-center">
                        <i class="bi bi-exclamation-triangle-fill text-4xl text-[#8A2BE2] mb-6"></i>
                        <h3 class="text-2xl font-black Montserrat uppercase tracking-tighter mb-4 text-[#8A2BE2]">Alerta de Risco <br> LGPD 2026</h3>
                        <p class="text-gray-400 text-xs font-light mb-8 italic">"Sua empresa está em conformidade com os novos protocolos de 2026?"</p>
                        <div class="text-[9px] font-black uppercase tracking-[3px] text-white">Consulte Especialista</div>
                    </div>
                </div>
            `;
            radarGrid.insertAdjacentHTML('beforeend', filler);
        }
    }
}

function openPost(id) {
    window.location.href = `post.html?id=${id}`;
}

// Events
if (!IS_BLOG_PAGE && btnLoadMore) {
    btnLoadMore.addEventListener('click', () => {
        window.location.href = 'blog.html';
    });
} else if (IS_BLOG_PAGE && btnLoadMore) {
    btnLoadMore.addEventListener('click', () => {
        renderBlogGrid();
    });
}

// Blog Specific UI
if (IS_BLOG_PAGE) {
    if (activeFilter) {
        if (blogPretitle) blogPretitle.textContent = "Radar Santis";
        blogTitle.innerHTML = `<span class="text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient">${activeFilter}</span>`;
        if (blogPretitle) {
            blogPretitle.textContent = "Radar Santis";
            blogPretitle.classList.add('pr-6');
        }

        // Dynamic title based on category
        let gradientClass = "from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF]";
        if (activeFilter.toLowerCase() === 'segurança') {
            gradientClass = "from-red-500 to-[#8A2BE2]";
        } else if (activeFilter.toLowerCase() === 'otimização') {
            gradientClass = "from-[#00F2FF] to-white";
        }

        blogTitle.innerHTML = `<span class="inline-block text-transparent bg-clip-text bg-gradient-to-r ${gradientClass} animate-gradient px-4 pb-4">${activeFilter}</span>`;

        if (blogDescription) {
            blogDescription.textContent = activeFilter.toLowerCase() === 'segurança'
                ? 'Monitoremos e blindamos sua infraestrutura contra ameaças globais em tempo real.'
                : 'Foco total em performance. Ajustes profundos para garantir a fluidez máxima do seu sistema.';
        }

        const filterMap = { 'segurança': 'filter-seguranca', 'otimização': 'filter-otimizacao' };
        const activeBtnId = filterMap[activeFilter.toLowerCase()];
        if (activeBtnId) {
            document.getElementById(activeBtnId).classList.add('active');
        }
    } else {
        if (blogPretitle) {
            blogPretitle.textContent = "Radar Online";
            blogPretitle.classList.add('pr-6');
        }
        blogTitle.innerHTML = `<span class="inline-block text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient px-4 pb-4">Radar Santis</span>`;
        if (blogDescription) {
            blogDescription.textContent = 'Inteligência, Segurança e Performance para o seu negócio. Mantenha-se atualizado com as últimas tendências e análises do mercado.';
        }
        document.getElementById('filter-all').classList.add('active');
    }
}

// Initial Run
if (radarGrid) {
    renderBlogGrid();
}

// Global Sharing Logic
function shareWhatsApp() {
    const text = encodeURIComponent('Confira o Radar Santis - Inteligência, Segurança e Performance: ' + window.location.href);
    window.open(`https://wa.me/?text=${text}`, '_blank');
}

function shareFacebook() {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`, '_blank');
}

function shareEmail() {
    const subject = encodeURIComponent('Radar Santis | Inteligência Digital');
    const body = encodeURIComponent('Confira este conteúdo do Radar Santis: ' + window.location.href);
    window.location.href = `mailto:?subject=${subject}&body=${body}`;
}

function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        const tooltip = document.getElementById('copy-tooltip-global');
        if (tooltip) {
            tooltip.classList.remove('opacity-0');
            tooltip.classList.add('opacity-100');
            setTimeout(() => {
                tooltip.classList.remove('opacity-100');
                tooltip.classList.add('opacity-0');
            }, 2000);
        }
    });
}