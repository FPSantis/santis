document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const postId = parseInt(urlParams.get('id'));
    const mainContent = document.getElementById('post-content');

    const post = blogPosts.find(p => p.id === postId);

    if (post) {
        const cleanTitle = post.title.replace(/<[^>]*>/g, '');
        document.title = `${cleanTitle} | Santis Radar`;

        mainContent.innerHTML = `
            <!-- Hero Banner -->
            <div class="relative min-h-[500px] md:h-[65vh] w-full overflow-hidden flex items-end pt-40 pb-20 md:pt-0 md:pb-24">
                <img src="${post.image || 'https://cdn.santis.ddev.site/radar/2026/02/hero-santis.png'}" class="absolute inset-0 w-full h-full object-cover opacity-30">
                <div class="absolute inset-0 post-banner-fade"></div>
                
                <div class="container mx-auto px-6 relative z-10 max-w-5xl">
                    <a href="/radar?filter=${post.category}" class="badge-highlight mb-8 no-print inline-block hover:scale-105 transition-transform hover:border-[#00F2FF] hover:text-[#00F2FF]">${post.category}</a>
                    <h1 class="text-5xl md:text-7xl font-black Montserrat uppercase tracking-tighter leading-[1] mb-6">
                        ${post.title}
                    </h1>
                    <div class="flex items-center gap-6 text-[11px] font-black uppercase tracking-[3px] text-gray-500 italic">
                        <span>${post.date} ${post.year}</span>
                        <span class="w-1.5 h-1.5 bg-[#8A2BE2] rounded-full"></span>
                        <span>${post.readTime} Leitura</span>
                    </div>
                </div>
            </div>

            <div class="container mx-auto px-6 max-w-4xl py-0">
                <div class="prose prose-invert max-w-none">
                    <p class="text-3xl text-gray-400 font-light leading-relaxed mb-16 italic border-l-8 border-[#8A2BE2] pl-10">
                        ${post.summary}
                    </p>

                    <div class="mb-16 relative rounded-3xl overflow-hidden border border-white/10 shadow-3xl aspect-[16/8]">
                        <img src="${post.image}" class="w-full h-full object-cover">
                    </div>

                    <div class="text-xl text-gray-300 font-light leading-relaxed space-y-10">
                        ${post.content}
                    </div>
                </div>

                <!-- Expanded Share Bar -->
                <div class="mt-20 pt-0 border-t border-white/10 no-print">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-12">
                        <div>
                            <h4 class="text-white font-black Montserrat uppercase tracking-[4px] text-sm mb-2 uppercase">Compartilhar Artigo</h4>
                            <p class="text-gray-600 text-[10px] uppercase tracking-widest font-black italic">Escrito com <span class="text-[#8A2BE2] animate-pulse italic"><i class="bi bi-heart-fill"></i></span> por Fernando Santis</p>
                        </div>
                        <div class="flex flex-wrap justify-center gap-4 max-w-[220px] md:max-w-none">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}" target="_blank" class="w-14 h-14 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-white hover:bg-[#1877F2] hover:text-white transition-all text-xl" title="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(window.location.href)}" target="_blank" class="w-14 h-14 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-white hover:bg-[#0077B5] hover:text-white transition-all text-xl" title="LinkedIn">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <a href="https://wa.me/?text=${encodeURIComponent(cleanTitle + ' ' + window.location.href)}" target="_blank" class="w-14 h-14 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-white hover:bg-[#25D366] hover:text-white transition-all text-xl" title="WhatsApp">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                            <a href="mailto:?subject=${cleanTitle}&body=Confira este artigo: ${window.location.href}" class="w-14 h-14 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-white hover:bg-[#EA4335] hover:text-white transition-all text-xl" title="E-mail">
                                <i class="bi bi-envelope-fill"></i>
                            </a>
                            <button onclick="copyToClipboard()" id="btn-copy" class="w-14 h-14 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-white hover:bg-[#00F2FF] hover:text-[#050A18] transition-all text-xl relative" title="Copiar Link">
                                <i class="bi bi-link-45deg"></i>
                                <span id="copy-tooltip" class="absolute -top-12 bg-[#00F2FF] text-[#050A18] text-[10px] font-black px-3 py-1 rounded-lg opacity-0 pointer-events-none transition-all uppercase tracking-widest">Copiado!</span>
                            </button>
                            <button onclick="window.print()" class="w-14 h-14 flex items-center justify-center rounded-2xl bg-white/5 border border-white/10 text-white hover:bg-white hover:text-[#050A18] transition-all text-xl" title="Imprimir Artigo">
                                <i class="bi bi-printer-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else {
        mainContent.innerHTML = `
            <div class="h-screen flex items-center justify-center text-center px-6">
                <div>
                    <div class="text-red-500 font-black Montserrat text-6xl mb-8 uppercase tracking-tighter">404</div>
                    <h2 class="text-2xl font-black Montserrat uppercase tracking-widest mb-12">Intelecto não encontrado.</h2>
                    <a href="/#radar" class="px-8 py-4 border border-[#00F2FF] text-[#00F2FF] text-[10px] font-black uppercase tracking-[3px] rounded-full hover:bg-[#00F2FF] hover:text-black transition-all">Voltar ao Radar</a>
                </div>
            </div>
        `;
    }
});

function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(() => {
        const tooltip = document.getElementById('copy-tooltip');
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
