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