CBC Teste Técnico
Este projeto é uma API para gerenciar recursos financeiros de clubes, desenvolvida como parte de um teste técnico.

Estrutura do Projeto

app: Contém a lógica de negócios e a estrutura MVC.

Config: Configurações do banco de dados.

Controllers: Controladores para gerenciar as requisições.

Core: Conexão com o banco de dados.

Models: Modelos que representam as entidades do sistema.

public: Contém o ponto de entrada da aplicação.

index.php: Roteamento de requisições.

Endpoints

GET /clubes: Lista todos os clubes.

POST /clubes: Cadastra um novo clube.

POST /consumir: Consome um recurso.

GET /recursos: Lista todos os recursos.

Configuração

Banco de Dados:

Configure o arquivo config.php com as credenciais do seu banco de dados.

Criação das Tabelas:

Execute o seguinte script SQL no seu banco de dados:

CREATE TABLE tbl_clubes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nm_nome VARCHAR(100) NOT NULL,
    val_saldo_disponivel DECIMAL(10,2) NOT NULL CHECK (val_saldo_disponivel >= 0)
);

CREATE TABLE tbl_recursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ds_descricao VARCHAR(100) NOT NULL,
    val_saldo_disponivel DECIMAL(10,2) NOT NULL CHECK (val_saldo_disponivel >= 0)
);

Inserção de Dados:

Insira os dados iniciais:

INSERT INTO tbl_clubes (nm_nome, val_saldo_disponivel) VALUES
('Clube A', 2000.00),
('Clube B', 3000.00);

INSERT INTO tbl_recursos (ds_descricao, val_saldo_disponivel) VALUES
('Recurso para passagens', 10000.00),
('Recurso para hospedagens', 10000.00);

Como Executar

Servidor Local:

Coloque o projeto na pasta htdocs do XAMPP.

Inicie o servidor Apache no XAMPP.

Acesse a API:

Use um cliente HTTP como Postman para testar os endpoints.

Segurança

Valide todas as entradas de dados.


Configure corretamente o php.ini para desativar display_errors.

Problemas Conhecidos:

Observação - Erro do Intelephense: Separei o http_response_code e echo em linhas diferentes para evitar erros.
