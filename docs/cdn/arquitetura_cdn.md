# Rede de Entrega de Conteúdo (CDN)

Este documento dita a dinâmica da hospedagem apartada da Santis para arquivos estáticos e de upload dinâmico.
(Servido sob o host `cdn.santis.net.br` / `cdn.santis.ddev.site`).

## Visão Geral
A pasta `cdn` guarda imagens, PDFs e grandes binários. Ela garante que requisições estáticas não tomem thread worker do interpretador PHP nos containers `www` ou `painel`.

## Uso Pela Aplicação
As imagens ou mídias invocadas nas estruturas MVC do Projeto `www` sempre precederão do helper configurado nas Views, resolvendo a Rota para o caminho real da sua CDN.

*Mais detalhes sobre políticas de cache e controle de banda serão estabelecidos no momento da integração entre painel (upload) e cdn.*
