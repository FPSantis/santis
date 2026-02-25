document.getElementById('contact-form').addEventListener('submit', function (e) {
    e.preventDefault();
    const btn = this.querySelector('button');
    const originalContent = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = `
        <span class="font-[Montserrat] uppercase tracking-widest text-sm">Enviando Solicitação...</span>
        <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    `;

    // Simulação de protocolo de envio corporativo
    setTimeout(() => {
        alert("Sua solicitação foi processada com sucesso. Um especialista Santis entrará em contato em breve.");
        btn.disabled = false;
        btn.innerHTML = originalContent;
        this.reset();
    }, 2500);
});