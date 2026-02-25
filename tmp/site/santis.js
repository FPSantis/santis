// santis.js - High-Tech Interactions

document.addEventListener('DOMContentLoaded', () => {
    console.log("[Santis OS] Protocolo de Interface Ativado.");

    // --- SMOOTH SCROLL PARA ÂNCORAS ---
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                window.scrollTo({
                    top: target.offsetTop - 80, // Offset para o header fixo
                    behavior: 'smooth'
                });
            }
        });
    });

    // --- WHATSAPP REDIRECT LOGIC ---
    const whatsappButtons = document.querySelectorAll('.btn-whatsapp, .whatsapp-float');
    whatsappButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const phone = "5511999999999"; // Substituir pelo real
            const message = "Olá Santis! Gostaria de iniciar um protocolo de defesa digital.";
            const url = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
            window.open(url, '_blank');
        });
    });

    // --- REVEAL ON SCROLL (FADE-IN) ---
    const revealElements = document.querySelectorAll('.reveal');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, {
        threshold: 0.1
    });

    revealElements.forEach(el => revealObserver.observe(el));

    // --- SCANNER SIMULATION ---
    const scannerBtn = document.getElementById('scanner-btn');
    if (scannerBtn) {
        scannerBtn.addEventListener('click', () => {
            const input = document.getElementById('scanner-input');
            const result = document.getElementById('scanner-result');
            const btnText = document.getElementById('btn-text');

            if (!input.value) return;

            btnText.innerText = "ANALISANDO...";
            scannerBtn.classList.add('opacity-50', 'pointer-events-none');

            setTimeout(() => {
                result.classList.remove('hidden');
                setTimeout(() => result.classList.add('opacity-100', 'scale-100'), 50);
                btnText.innerText = "CONCLUÍDO";
            }, 2000);
        });
    }

    // --- HEADER SCROLL EFFECT ---
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            header.classList.add('bg-[#050A18]/90', 'border-white/10');
            header.classList.remove('bg-transparent', 'border-transparent');
        } else {
            header.classList.remove('bg-[#050A18]/90', 'border-white/10');
            header.classList.add('bg-transparent', 'border-transparent');
        }
    });
});