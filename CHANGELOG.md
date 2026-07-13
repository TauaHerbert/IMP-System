##  [1.4.0] - 2026-07-13 . . . . . . . . . . . . . . . . . .
###  Adicionado
- 🗄️ **Sistema de Arquivamento (Soft Delete):** Adicionada a coluna `status` no banco de dados para permitir arquivar impressoras sem quebrar o histórico de leituras e trocas.
- 🏚️ **Tela de Depósito:** Criada a página `imp_inativas.php` para listar e gerenciar exclusivamente os equipamentos inativos/arquivados.
- ⚡ **Ações de Gerenciamento:** Implementados os métodos de Reativar (volta para produção) e Exclusão Definitiva (Hard Delete) direto no painel do depósito.
###  Alterado
- 🧹 **Filtro Principal:** O painel de cadastro e listagem principal agora oculta automaticamente as impressoras inativas, mantendo a tela limpa.
- 🎨 **UI/UX:** Adicionada a coluna "Ações" na listagem de equipamentos com botões de Desativar (amarelo/perigo) e alertas de confirmação para prevenir cliques acidentais.
###  Corrigido
- 🔗 Correção visual nos links que funcionam como botões (remoção do `text-decoration` sublinhado padrão do navegador).
- 🔌 Resolução de erro de conexão PDO (`query` vs `prepare`) nos métodos de exclusão.
##  [1.3.0] - 2026-07-08 . . . . . . . . . . . . . . . . . .
###  Adicionado & Melhorado
- 🖨️ **Finalização do Dashboard Individual:** Implementação completa da interface em `dashboard_imp.php`, incluindo barra de progresso horizontal, motor preditivo e tabelas dinâmicas de histórico.
- 💾 **Lógica de Salvamento de Leituras:** Criação do método `registrarLeitura` no `LeituraDAO` para efetivar a gravação das contagens a partir do modal interativo.
- 📊 **Mini Sensor Vertical:** Substituição do status de texto por um gráfico de barras vertical no painel principal (`index.php`), que enche de baixo para cima e altera sua cor (Verde, Amarelo, Vermelho) conforme o consumo.
- 💬 **Tooltip Customizado:** Implementação de um balão de informação flutuante (CSS puro) na visão geral, ativado ao passar o mouse sobre o gráfico, com delay suave de 0.7s para melhorar a usabilidade sem poluir a tela.
- ⚙️ **Upgrade de Consultas:** Criação do método `listarTodasComStatus()` no `ImpDAO` com subqueries SQL avançadas para unificar os dados da impressora, última leitura e marco zero em uma única chamada.
- 🐍 **Easter Egg (Modo Resenha - Santa Cruz):** Criação de um tema oculto com as cores do tricolor do Arruda (Preto, Branco e Vermelho). Ativado pelo botão "Modo Escuro" no menu (`menu.php`), o recurso utiliza um fundo em degradê e transições CSS de 0.8s para uma mudança suave de layout.
###  Corrigido
- 🚨 **Correção de Undefined Array Key:** Ajuste na query SQL principal do `ImpDAO` para incluir as colunas ausentes (`ultima_leitura`, `ultima_data_leitura` e `ultima_data_troca`) que causavam erro fatal no `index.php`.
- 🧹 **Refatoração de Estilos:** Limpeza de tags `<style>` do HTML e centralização de todo o código visual (Tooltip e Modo Resenha) no arquivo `css/style.css` para melhorar a performance.
- 🎨 **Prioridade de CSS no Modo Resenha:** Resolução do bug de herança de cores na página `cad_imp.php`, forçando as cores corretas nos títulos (`<h2>`, `<h3>`) sobrepondo estilos inline com o uso da regra `!important`.
## [1.3.0] - 2026-07-07 . . . . . . . . . . . . . . . . . .
### Adicionado
- 🖨️ **Dashboard Individual de Impressoras** (`dashboard_imp.php`) com interface segura baseada em modais.
- 🚥 **Sensor Visual de Toner**: Cálculo dinâmico de consumo relativo com transição de cores em tempo real (Verde, Amarelo, Vermelho).
- 📍 **Lógica de "Marco Zero"**: Banco de dados atualizado para registrar a `leitura na troca`, permitindo isolar o consumo do toner da vida útil total da máquina.
- 🔮 **Motor Preditivo**: Algoritmo matemático que calcula a média diária de impressões e estima a data de esgotamento do suprimento.
- 🎨 **Estilo Dedicado**: Criação do `dashboard.css` para isolar a camada visual do painel preditivo.
- 🛠️ **Novos DAOs e Métodos**: Criação do `H_trocaDAO` e atualização do `LeituraDAO` para inserção e listagem em tempo real das interações.

### Corrigido
- 🔌 Padronização da injeção da conexão PDO (`Conexao::getConexao()`) nos arquivos `H_trocaDAO.php` e `LeituraDAO.php`.
- 🚨 Resolução de `Fatal error` (PDO null reference) adaptando as consultas SQL para a arquitetura correta do sistema.
- 🏷️ Correção de nomenclatura de classe para carregamento correto do objeto de trocas.
## [1.2.0] - 2026-07-06 . . . . . . . . . . . . . . . . . .
### Adicionado
- 📊 Listagem dinâmica de impressoras no painel principal (`index.php`) com datas de leitura e troca.
- 🗃️ Classes de Modelo e DAO para o histórico de impressões (`Leitura.php` e `LeituraDAO.php`).
- 🪟 Componente de Modal (Vanilla JS e CSS) para atualização ágil do contador de páginas.
- 👁️ Tabelas de listagem integradas diretamente nas telas de cadastro (`cad_dep.php` e `cad_imp.php`).
- 📐 Novas classes de *CSS Grid* no arquivo central (`style.css`) para layouts de múltiplas colunas.

### Alterado
- 💅 Revitalização do design dos botões (transições suaves, sombras dinâmicas e feedback tátil).
- 📱 Reestruturação visual das telas de cadastro (Grid em duas colunas para Departamentos e Empilhamento Vertical para Impressoras).
- 🧹 Remoção dos botões redundantes de "Voltar ao Painel" para melhorar a UX e focar no menu lateral.
- ♻️ Padronização rigorosa de Orientação a Objetos: adequação da classe `Impressora` para `Imp`.

### Corrigido
- 🐛 Ajuste no alinhamento das colunas do cabeçalho (`<thead>`) com os dados do painel.
- 🐛 Correção de exceções de tipagem (`TypeError`) e carregamento de classe (`Class not found`) na integração Model/DAO.
- 🐛 Implementação do Operador de Coalescência Nula (`?? ''`) para prevenir avisos de *Deprecated* do `htmlspecialchars()` no PHP 8+.
## [1.1.0] - 2026-07-03 . . . . . . . . . . . . . . . . . .
### Adicionado
- ✨ Arquitetura Orientada a Objetos com pastas separadas (`modelsLibrary` e `daoLibrary`).
- 🔒 Conexão segura com o banco de dados usando PDO (`conection.php` e `config.ini`).
- 🙈 Arquivo `.gitignore` para proteger credenciais locais.
- 🖨️ Classes de Modelo e DAO para Impressoras (`Imp.php` e `ImpDAO.php`).
- 🏢 Classes de Modelo e DAO para Departamentos (`Dep.php` e `DepDAO.php`).
- 🖥️ Telas de cadastro funcionais (`cad_imp.php` e `cad_dep.php`).

### Alterado
- ♻️ Atualização da tela `cad_imp.php` para utilizar um *combobox* dinâmico puxando os departamentos direto do banco de dados, substituindo a digitação manual.
- 📝 Atualização da documentação no `README.md` com as instruções do banco de dados.

### Corrigido
- 🐛 Ajuste de rotas relativas (`../`) e nomenclatura de arquivos para a importação correta das classes via `require_once`.
## [1.0.0] - 2026-07-02 . . . . . . . . . . . . . . . . . .
### Adicionado
- 🎉 Estrutura inicial **IMP System**.
- 📑 Menu principal `menu.php`.
- 📑 Pagina principal `index.php`
- 📑 Arquivo **CSS**/`style.css`.
- 📑 **README**
- 📑 **CHANGELOG**