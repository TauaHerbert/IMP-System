## [1.3.0] - 2026-07-07
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