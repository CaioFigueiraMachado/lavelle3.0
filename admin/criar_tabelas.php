<?php
// admin/criar_tabelas.php
session_start();
if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

include 'config/database.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Criar tabela de produtos se não existir
    $query_produtos = "CREATE TABLE IF NOT EXISTS produtos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nome VARCHAR(255) NOT NULL,
        descricao TEXT,
        preco DECIMAL(10,2) NOT NULL,
        imagem VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $stmt_produtos = $db->prepare($query_produtos);
    $stmt_produtos->execute();
    
    echo "<h2>Tabela 'produtos' criada/configurada com sucesso!</h2>";
    
    // Inserir alguns produtos de exemplo
    $produtos_exemplo = [
        ['Lavelle Aureum', 'Fragrância exclusiva com notas amadeiradas', 299.90, 'lavelleaureum.jpg'],
        ['Lavelle Horizon', 'Perfume oriental com toques especiados', 349.90, 'horizon.png'],
        ['Lavelle Rose Sublime', 'Essência floral romântica e suave', 279.90, 'Lavelle Rose Sublime.jpg']
    ];
    
    foreach($produtos_exemplo as $produto) {
        $query_verificar = "SELECT COUNT(*) as total FROM produtos WHERE nome = ?";
        $stmt_verificar = $db->prepare($query_verificar);
        $stmt_verificar->execute([$produto[0]]);
        
        if($stmt_verificar->fetch(PDO::FETCH_ASSOC)['total'] == 0) {
            $query_inserir = "INSERT INTO produtos (nome, descricao, preco, imagem) VALUES (?, ?, ?, ?)";
            $stmt_inserir = $db->prepare($query_inserir);
            $stmt_inserir->execute($produto);
        }
    }
    
    echo "<p>Produtos de exemplo inseridos!</p>";
    
    // Verificar e ajustar tabela usuarios
    $query_check_usuarios = "SHOW COLUMNS FROM usuarios LIKE 'created_at'";
    $stmt_check = $db->prepare($query_check_usuarios);
    $stmt_check->execute();
    
    if($stmt_check->rowCount() == 0) {
        $query_alter_usuarios = "ALTER TABLE usuarios ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $stmt_alter = $db->prepare($query_alter_usuarios);
        $stmt_alter->execute();
        echo "<p>Coluna 'created_at' adicionada à tabela 'usuarios'!</p>";
    } else {
        echo "<p>Tabela 'usuarios' já possui a coluna 'created_at'!</p>";
    }
    
    echo "<br><a href='dashboard.php' style='background: #000; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Voltar ao Dashboard</a>";
    
} catch(PDOException $e) {
    echo "<div style='color: red;'><h2>Erro ao criar tabelas:</h2><p>" . $e->getMessage() . "</p></div>";
}
?>