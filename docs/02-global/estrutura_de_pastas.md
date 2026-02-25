# Arquitetura e Estrutura de Diretórios (Santis)

Este documento define a nova arquitetura oficial **Multi-Site** do projeto Santis. A arquitetura foi desenhada com foco absoluto em **Segurança (Defesa em Profundidade)** e **Escalabilidade**, dividindo responsabilidades Frontend, Backend e Storage em aplicações isoladas.

## 1. Raiz do Projeto (Ambiente Dev Multi-Site)
A raiz (`/mnt/d/_WEB/santis/`) atua como a infraestrutura global. Ela dita como o servidor NGINX interno do DDEV vai orquestrar as três aplicações separadas.

* `.ddev/`: Orquestração global do ambiente local. Contém o NGINX com mapeamento de domínios.
* `.vscode/`: Configurações do editor e do assistente IA (Antigravity).
* `docs/`: Base de conhecimento global e das sub-aplicações.
* `scripts/`: Scripts `.sh` de automação (StartWeb, Deploy).
* `ref/`: Arquivos de referência, imagens brutas e mockups originais.

---

## 2. As Sub-Aplicações (Os Projetos Reais)
Dentro da raiz, vivem agora as três sub-aplicações independentes que respondem pelos diferentes domínios.

### A. `www/` (Frontend - Santis Site Principal / O "Public_html")
Responsável pelo site institucional acessível ao público geral (Domínio: `www.santis.net.br` e `www.santis.ddev.site`).
Trabalha com PHP e MVC, mas foca puramente em consumir/apresentar dados, utilizando o motor de templates **Twig**.

* `public_html/`: Única pasta pública. Dentro dela reside o `index.php` roteador (Front Controller) e os assets específicos do frontend.
* `src/`: Lógica do MVC (Controllers, Views baseadas em Módulos, Core helpers).

### B. `painel/` (Backend Oculto - Painel Administrativo / "Admin")
Interface administrativa segura, inacessível ao usuário final (Domínio: `painel.santis.net.br` e `painel.santis.ddev.site`).
Será a única aplicação com permissão real para manipular profundamente o banco de dados via APIs seguras ou controllers administrativos.

### C. `cdn/` (Storage Dinâmico e Estático - Delivery Network)
Hospedagem exclusiva de arquivos para evitar que uploads quebrem na recriação de containers ou sobrecarreguem as requisições principais do site (Domínio: `cdn.santis.net.br` e `cdn.santis.ddev.site`).

---

## 3. Automação e DevOps Global (`scripts/`)
Scripts shell padronizados para manutenção de todo o ambiente de uma vez:

* `startweb.sh`: Inicializa o `.ddev` da raiz e automatiza os compilações necessárias (como o `composer install` no `www`).
* `create_test_db_dump.sh` / `reset_test_db.sh`: Operações utilitárias para o banco de dados nativo do DDEV que alimenta as aplicações.
* `sync_to_dev.sh` / `sync_to_prod.sh`: Sincronização avançada entre o ambiente local Multi-Site e o Hostinger.