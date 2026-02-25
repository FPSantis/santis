# Arquitetura do Painel Administrativo

Este documento servirá de base para a futura documentação do backend administrativo do ecossistema Santis.
(Servido sob o host `painel.santis.net.br` / `painel.santis.ddev.site`).

## Visão Geral
O painel administrará a centralização de conteúdo, usuários e portfólio.
Será a única interface do projeto autorizada a disparar escritas (DML) de forma pesada ao banco de dados e manipular grandes deleções através de lógicas protegidas por autenticação.

## Integração com o WWW
Toda mudança feira aqui poderá ser refletida no frontend (WWW) via consumo de banco de dados compartilhado.

*Mais detalhes serão preenchidos conforme os módulos administrativos forem esculpidos.*
