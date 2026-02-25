// Menu Mobile Toggle
const openMenu = document.getElementById('open-menu');
const closeMenu = document.getElementById('close-menu');
const overlay = document.getElementById('menu-overlay');

openMenu.addEventListener('click', () => {
    overlay.classList.remove('translate-x-full');
});

closeMenu.addEventListener('click', () => {
    overlay.classList.add('translate-x-full');
});

// Fechar menu ao clicar em link
document.querySelectorAll('#menu-overlay a').forEach(link => {
    link.addEventListener('click', () => {
        overlay.classList.add('translate-x-full');
    });
});

// Simulação de Radar
const scanBtn = document.querySelector('button.bg-white');
if (scanBtn) {
    scanBtn.addEventListener('click', function () {
        this.innerText = "Localizando Vulnerabilidades...";
        this.classList.add('animate-pulse');
        setTimeout(() => {
            alert('Análise concluída: Seu site precisa de blindagem premium Santis.');
            this.innerText = "Escanear novamente";
            this.classList.remove('animate-pulse');
        }, 3000);
    });
}