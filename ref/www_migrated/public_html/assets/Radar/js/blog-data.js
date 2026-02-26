const blogPosts = [
    {
        id: 1,
        title: "Alerta de Segurança: Mega Vazamento expõe <span class='text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient'>200M de credenciais</span>",
        category: "Segurança",
        image: "https://cdn.santis.ddev.site/radar/2026/02/system_optimization.png",
        date: "24 Fev",
        year: "2026",
        readTime: "4 min",
        summary: "Um novo banco de dados massivo foi encontrado em fóruns de cibercrime, contendo e-mails e senhas de serviços populares. Saiba como se proteger.",
        content: "O cenário de ameaças digitais acaba de sofrer um novo abalo. Pesquisadores de segurança identificaram um repositório com mais de 200 milhões de registros expostos. Este vazamento, apelidado de 'Santis Alert #1', destaca a importância de senhas únicas e autenticação em dois fatores."
    },
    {
        id: 2,
        title: "Dica de Otimização: Como reduzir a latência no <span class='text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient'>Windows 11</span>",
        category: "Otimização",
        image: "https://cdn.santis.ddev.site/services/2026/02/web_presence.png",
        date: "22 Fev",
        year: "2026",
        readTime: "6 min",
        summary: "Pequenos ajustes nas configurações de energia e serviços de segundo plano podem transformar a fluidez do seu sistema.",
        content: "Muitos usuários sofrem com micro-travamentos no Windows 11 devido a telemetria excessiva e serviços de indexação pesados. Neste guia, mostramos como desabilitar processos desnecessários com segurança para ganhar performance real em jogos e trabalho."
    },
    {
        id: 3,
        title: "Vulnerabilidade <span class='text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient'>Crítica</span> em Roteadores Domésticos",
        category: "Segurança",
        image: "https://cdn.santis.ddev.site/services/2026/02/santis_control.png",
        date: "20 Fev",
        year: "2026",
        readTime: "5 min",
        summary: "Uma falha de dia zero permite execução remota de código em marcas populares. Verifique se o seu firmware está atualizado.",
        content: "A segurança da sua rede começa no roteador. Uma nova falha descoberta permite que atacantes assumam o controle total do tráfego de rede sem interação do usuário. A Santis recomenda a atualização imediata dos patches de segurança fornecidos pelos fabricantes."
    },
    {
        id: 4,
        title: "Nota Rápida: Atualização do Windows 11 causa bugs em <span class='text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient'>drives SSD</span>",
        category: "Otimização",
        image: "https://cdn.santis.ddev.site/radar/2026/02/hero-santis.png",
        date: "18 Fev",
        year: "2026",
        readTime: "2 min",
        summary: "Usuários relatam quedas de performance após o último patch KB503... Saiba como reverter ou mitigar o problema.",
        content: "A Microsoft confirmou que a última atualização cumulativa pode afetar a velocidade de escrita em determinados modelos de SSD. Se você notou lentidão ao abrir programas, recomendamos pausar as atualizações automáticas até que o patch de correção oficial seja lançado."
    },
    {
        id: 5,
        title: "Santis Intelligence: O Futuro da <span class='text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient'>Segurança Preditiva</span>",
        category: "Segurança",
        image: "https://cdn.santis.ddev.site/radar/2026/02/system_optimization.png",
        date: "16 Fev",
        year: "2026",
        readTime: "7 min",
        summary: "Como algoritmos de IA estão antecipando ataques antes mesmo deles acontecerem. A nova era da defesa digital.",
        content: "A proatividade é a chave para a segurança moderna. Exploramos como as novas ferramentas da Santis utilizam análise comportamental para bloquear ameaças em milissegundos."
    },
    {
        id: 6,
        title: "Otimização Extrema: Script para <span class='text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient'>Debloat do Windows</span> 2026",
        category: "Otimização",
        image: "https://cdn.santis.ddev.site/services/2026/02/web_presence.png",
        date: "14 Fev",
        year: "2026",
        readTime: "3 min",
        summary: "Remova telemetria, apps inúteis e libere recursos ocultos do seu hardware com um único comando.",
        content: "Menos é mais quando se trata de performance. Disponibilizamos um script seguro para limpar sua instalação do Windows e focar apenas no que importa: velocidade total."
    },
    {
        id: 7,
        id_original: 1,
        title: "Arquivo: Mega Vazamento expõe <span class='text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient'>200M de credenciais</span>",
        category: "Segurança",
        image: "https://cdn.santis.ddev.site/services/2026/02/santis_control.png",
        date: "12 Fev",
        year: "2026",
        readTime: "4 min",
        summary: "Relembre os detalhes do Santis Alert #1 e as medidas que ainda são válidas para proteger seus dados hoje.",
        content: "Revisitamos o vazamento histórico para analisar os padrões de ataque e como a segurança evoluiu desde então."
    },
    {
        id: 8,
        id_original: 2,
        title: "Performance: Guia Definitivo de <span class='text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient'>Latência Zero</span>",
        category: "Otimização",
        image: "https://cdn.santis.ddev.site/radar/2026/02/system_optimization.png",
        date: "10 Fev",
        year: "2026",
        readTime: "8 min",
        summary: "Do hardware ao software: tudo o que você precisa saber para atingir a performance máxima.",
        content: "Um mergulho profundo nos protocolos de rede e otimização de kernel para entusiastas e profissionais."
    },
    {
        id: 9,
        title: "Dark Web: Como monitorar seu <span class='text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient'>Domínio</span>",
        category: "Segurança",
        image: "https://cdn.santis.ddev.site/services/2026/02/web_presence.png",
        date: "08 Fev",
        year: "2026",
        readTime: "5 min",
        summary: "Descubra se sua empresa está sendo citada em fóruns restritos e evite invasões antes que aconteçam.",
        content: "O monitoramento contínuo da Dark Web é essencial para a resiliência empresarial. Veja como automatizar esse processo."
    },
    {
        id: 10,
        title: "Bugs: A Verdade sobre as <span class='text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient'>Últimas Atualizações</span>",
        category: "Otimização",
        image: "https://cdn.santis.ddev.site/radar/2026/02/hero-santis.png",
        date: "06 Fev",
        year: "2026",
        readTime: "4 min",
        summary: "Análise técnica dos patches mensais e seus impactos reais na estabilidade do sistema.",
        content: "Nem toda atualização é positiva. Analisamos os regressos de performance introduzidos nos kernels mais recentes."
    },
    {
        id: 11,
        title: "Biometria: O Fim das <span class='text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient'>Senhas Comuns</span>?",
        category: "Segurança",
        image: "https://cdn.santis.ddev.site/services/2026/02/santis_control.png",
        date: "04 Fev",
        year: "2026",
        readTime: "6 min",
        summary: "Passkeys e autenticação biométrica estão dominando o mercado. Vale a pena migrar agora?",
        content: "A praticidade versus a segurança. Discutimos o estado atual das Passkeys e por que elas são o futuro."
    },
    {
        id: 12,
        title: "Hardware: A Escolha certa para <span class='text-transparent bg-clip-text bg-gradient-to-r from-[#00F2FF] via-[#8A2BE2] to-[#00F2FF] animate-gradient'>Performance Máxima</span>",
        category: "Otimização",
        image: "https://cdn.santis.ddev.site/radar/2026/02/system_optimization.png",
        date: "02 Fev",
        year: "2026",
        readTime: "10 min",
        summary: "SSD NVMe vs SATA em 2026: A diferença real que você sente no dia a dia de trabalho.",
        content: "Testes laboratoriais Santis mostram que a latência de acesso é mais importante que a velocidade sequencial pura."
    }
];
