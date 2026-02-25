# Padrões e Fluxo de Trabalho (Workflow & Git)

Este documento dita as regras inegociáveis para qualquer desenvolvedor, IA, ou colaborador que atuar no repositório da Santis.

## 1. Fluxo de Git (Git Flow Adaptado)

Para manter o repositório principal (`main`) sempre estável e espelhando a produção, adotamos um fluxo baseado em **Branches Baseadas em Tarefas e Commits Atômicos**.

### 1.1 Nomenclatura de Branches
Nunca comite diretamente na `main`. Todo desenvolvimento deve acontecer em uma branch dedicada e temporária.
- `feat/nome-da-tarefa` (Para novas funcionalidades, ex: `feat/modulo-mensageiro`)
- `fix/nome-do-bug` (Para correções de bugs, ex: `fix/erro-upload-cdn`)
- `docs/nome-do-documento` (Para atualização exclusiva de documentação)
- `refactor/nome-do-refatoramento` (Para melhorias de código sem mudar comportamentos)

### 1.2 Regras de Commits
Os commits **DEVEM** ser granulares e atômicos (um commit = uma ação logicamente completa, que não quebra a build).
- O idioma obrigatório para os commits é **Português do Brasil (PT-BR)**.
- Use verbos no infinitivo ou imperativo na primeira letra maiúscula.
- *Exemplos Corretos:*
  - `Adiciona tabela de leads no esquema do banco`
  - `Corrige margem do botão de contato no mobile`
  - `Refatora o carregamento de CSS do componente portfolio`
- *Exemplos Incorretos:*
  - `fix na home`
  - `arrumando coisas`
  - `Implement module and fix other stuff` (Não é atômico e não foca em um problema só).

### 1.3 Fluxo de Merge
1. Finalizou o desenvolvimento da `feat/`? Garanta que todos os testes/builds passam localmente.
2. Faça o merge para a `main` (via Merge Request/Pull Request, ou localmente mantendo o fluxo limpo).
3. Ao finalizar o merge, **delete** a branch de recurso (`feat/`) para não poluir o repositório.
4. **Tags:** Releases importantes (ex: *Lançamento CMS V1*) devem ser marcadas com Tags no Git (ex: `v1.0.0`).

---

## 2. A Bíblia do AI Dev

IAs que atuarem neste repositório devem cumprir rigorosamente o alinhamento arquitetural do `/docs/architecture`.

1. **Aja de forma Atômica:** Não saia alterando 10 arquivos e reescrevendo funções que não foram pedidas em nome de "refatorações heroicas".
2. **Brainstorming Primeiro:** Em casos de arquiteturas centrais, proponha a lógica (`em markdown`) *antes* de injetar o script em `.sql`, `.php` ou `.js`.
3. **Leia a Documentação Existente:** Consulte os artefatos dentro de `docs/` antes de deduzir qualquer regra de permissão ou fluxo de serviço.
4. **Performance é Inegociável:** Não importe pacotes de dependências (NPM/Composer) desnecessariamente. Se dá para usar API Nativas (ex: Native Fetch API, IntersectionObserver, CSS puro) sem perdas significativas de tempo produtivo, este é o caminho priorizado.

## 3. Estrutura de To-Do List do Projeto

Todo o controle global do desenvolvimento a partir de agora é guiado pelo cronograma estrito no arquivo principal `.docs/TODO.md`. Considere o `TODO.md` a fonte de verdade absoluta de "para onde estamos indo hoje".
