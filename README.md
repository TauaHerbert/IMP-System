# 🖨️ IMP System - Sistema de Monitoramento de Impressoras

Um sistema web corporativo desenvolvido para gerenciar e monitorar o volume de impressões e o ciclo de vida dos toners de impressoras distribuídas pelos departamentos de uma empresa. 

O projeto utiliza uma arquitetura limpa baseada em **Orientação a Objetos (Models e DAOs)**, separando responsabilidades lógicas e visuais para facilitar a manutenção e escalabilidade.

---

## 🚀 Funcionalidades Atuais

- **Dashboard Principal:** Painel de visão geral para monitoramento dos equipamentos.
- **Gestão de Departamentos:** Cadastro de setores operacionais com validação contra duplicidade.
- **Gestão de Impressoras:** Cadastro de máquinas com seleção dinâmica de departamentos via banco de dados.
- **Interface Responsiva:** Layout limpo e profissional construído com CSS puro (Vanilla), sem dependência de frameworks externos.

---

## 💻 Tecnologias Utilizadas

* **Back-end:** PHP (Orientação a Objetos, PDO para segurança e comunicação com banco de dados)
* **Banco de Dados:** MySQL (Relacional, com Integridade Referencial e chaves estrangeiras)
* **Front-end:** HTML5 e CSS3 (Estilização em arquivo único isolado)
* **Padrões de Projeto:** Singleton (Conexão de Banco) e DAO (Data Access Object)
