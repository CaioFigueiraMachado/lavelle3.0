<?php
// criar_tabelas_completo.php
session_start();
include 'conexao.php';

try {
    // Verificar conexão
    if (!isset($database)) {
        die("Erro: Conexão com o banco de dados não estabelecida.");
    }
    
    $db = $database->getConnection();
    
    echo "<h2>Criando Tabelas do Sistema</h2>";
    
    // Criar tabela pedidos se não existir
    $db->exec("
        CREATE TABLE IF NOT EXISTS pedidos (
            id INT PRIMARY KEY AUTO_INCREMENT,
            usuario_id INT NOT NULL,
            data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
            total DECIMAL(10,2) NOT NULL,
            status VARCHAR(50) DEFAULT 'pendente',
            metodo_pagamento VARCHAR(50),
            endereco_entrega TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<p>✅ Tabela 'pedidos' criada/verificada</p>";
    
    // Criar tabela pedido_itens se não existir
    $db->exec("
        CREATE TABLE IF NOT EXISTS pedido_itens (
            id INT PRIMARY KEY AUTO_INCREMENT,
            pedido_id INT NOT NULL,
            produto_id INT NOT NULL,
            quantidade INT NOT NULL,
            preco_unitario DECIMAL(10,2) NOT NULL,
            subtotal DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "<p>✅ Tabela 'pedido_itens' criada/verificada</p>";
    
    // Verificar se tabela usuarios existe
    $tables = $db->query("SHOW TABLES LIKE 'usuarios'")->fetch();
    if (!$tables) {
        echo "<p style='color: orange;'>⚠️ Tabela 'usuarios' não encontrada. Execute o script de instalação original.</p>";
    } else {
        echo "<p>✅ Tabela 'usuarios' encontrada</p>";
    }
    
    // Verificar se tabela produtos existe
    $tables = $db->query("SHOW TABLES LIKE 'produtos'")->fetch();
    if (!$tables) {
        echo "<p style='color: orange;'>⚠️ Tabela 'produtos' não encontrada. Execute o script de instalação original.</p>";
    } else {
        echo "<p>✅ Tabela 'produtos' encontrada</p>";
    }
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h3>✅ Tabelas criadas com sucesso!</h3>";
    echo "<p>Agora você pode executar o <a href='testar_pedido.php'>teste do sistema</a>.</p>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "<h3>❌ Erro ao criar tabelas:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>