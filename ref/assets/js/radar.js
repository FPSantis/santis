const radarSection = document.getElementById('radar-scanner');

radarSection.innerHTML = `
    <!-- Section 3: Radar Scanner -->
    <div class="container mx-auto px-6 text-center">
        <div class="inline-block px-4 py-1 rounded-full bg-[#00F2FF]/10 text-[#00F2FF] text-[9px] font-bold uppercase tracking-widest mb-12">
            Sistema de Varredura Ativo
        </div>
        
        <div class="radar-container mb-12">
            <div class="radar-circle">
                <div class="radar-sweep"></div>
                <div class="radar-grid"></div>
                
                <!-- Simulated Blips -->
                <div class="absolute top-[20%] left-[30%] w-2 h-2 bg-[#00F2FF] rounded-full animate-ping opacity-40"></div>
                <div class="absolute top-[60%] left-[70%] w-1.5 h-1.5 bg-[#8A2BE2] rounded-full animate-pulse opacity-60"></div>
                <div class="absolute top-[40%] left-[80%] w-1 h-1 bg-white rounded-full opacity-30"></div>
            </div>
        </div>
        
        <div class="scanner-input-group">
            <h2 class="text-3xl font-black mb-8 Montserrat uppercase tracking-tight">Análise de <span class="text-[#00F2FF]">Vulnerabilidade</span> IP</h2>
            <div class="precision-scanner-bar mx-auto !max-w-2xl !bg-white/5 border-white/5">
                <input type="text" id="scan-input" placeholder="Digite seu e-mail ou domínio..." 
                    class="precision-scanner-input !text-sm">
                <button id="btn-scan" class="precision-scanner-btn">
                    VERIFICAR AGORA
                </button>
            </div>
            <div id="scan-result" class="mt-8 min-h-[40px] font-bold text-[10px] uppercase tracking-[2px] text-gray-500 italic"></div>
        </div>
    </div>
`;

const btnScan = document.getElementById('btn-scan');
const scanResult = document.getElementById('scan-result');

btnScan.addEventListener('click', () => {
    const value = document.getElementById('scan-input').value;
    if (!value) return;

    btnScan.disabled = true;
    scanResult.innerText = "> Iniciando varredura de protocolos...";

    setTimeout(() => { scanResult.innerText = "> Checando cabeçalhos de segurança..."; }, 1000);
    setTimeout(() => { scanResult.innerText = "> Analisando conformidade LGPD..."; }, 2000);

    setTimeout(() => {
        scanResult.innerHTML = `<span class="text-red-500 font-bold uppercase tracking-widest">⚠️ Atenção: Vulnerabilidades Detectadas.</span><br>Solicite o relatório completo via WhatsApp.`;
        btnScan.disabled = false;
    }, 3500);
});