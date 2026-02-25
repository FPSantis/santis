// Scripts Gerais de UI
document.addEventListener('DOMContentLoaded', () => {
    console.log('Santis Engenharia Digital V2 - Sistema Iniciado');

    // Mobile Menu Toggle
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuSpans = menuToggle.querySelectorAll('span');
    const body = document.body;

    if (menuToggle && mobileMenu) {
        menuToggle.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.contains('active');

            if (isOpen) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        // Close on link click
        document.querySelectorAll('.mobile-nav-link').forEach(link => {
            link.addEventListener('click', () => {
                closeMenu();
            });
        });
    }

    function openMenu() {
        mobileMenu.classList.add('active', 'opacity-100');
        mobileMenu.classList.remove('pointer-events-none');
        menuSpans[0].style.transform = 'translateY(4px) rotate(45deg)';
        menuSpans[1].style.transform = 'translateY(-4px) rotate(-45deg)';
        menuSpans[1].style.width = '24px';
        body.style.overflow = 'hidden';
    }

    function closeMenu() {
        mobileMenu.classList.remove('active', 'opacity-100');
        mobileMenu.classList.add('pointer-events-none');
        menuSpans[0].style.transform = 'none';
        menuSpans[1].style.transform = 'none';
        menuSpans[1].style.width = '16px';
        body.style.overflow = '';
    }

    // Smooth Scroll para links do menu
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Animação de Revelação ao Rolar (Reveal on Scroll)
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
});