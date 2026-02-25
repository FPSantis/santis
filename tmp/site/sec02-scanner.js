const btn = document.getElementById('scanner-btn');
const input = document.getElementById('scanner-input');
const resultBox = document.getElementById('scanner-result');

btn.addEventListener('click', () => {
    if (!input.value.includes('@')) return alert('Por favor, insira um e-mail profissional válido para o diagnóstico.');

    btn.disabled = true;
    const btnText = document.getElementById('btn-text');
    const originalText = btnText.innerText;
    btnText.innerText = "ANALISANDO DADOS...";

    // Simulação de check-up profissional com delay elegante
    setTimeout(() => {
        resultBox.classList.remove('hidden');
        // Delay para trigger de transição CSS
        setTimeout(() => {
            resultBox.classList.remove('opacity-0', 'scale-95');
            resultBox.classList.add('opacity-100', 'scale-100');
        }, 50);

        btn.disabled = false;
        btnText.innerText = "NOVO DIAGNÓSTICO";
    }, 2500);
});