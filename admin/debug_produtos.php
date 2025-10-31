<?php
include 'conexao.php';

echo "<h2>Debug - Produtos no Banco</h2>";

try {
    $query = "SELECT * FROM produtos";
    $stmt = $con->prepare($query);
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Total de produtos: " . count($produtos) . "</p>";
    
    foreach($produtos as $produto) {
        echo "<div style='border:1px solid #ccc; padding:10px; margin:10px;'>";
        echo "<strong>ID:</strong> " . $produto['id'] . "<br>";
        echo "<strong>Nome:</strong> " . $produto['nome'] . "<br>";
        echo "<strong>Preço:</strong> R$ " . $produto['preco'] . "<br>";
        echo "<strong>Categoria:</strong> " . $produto['categoria'] . "<br>";
        echo "<strong>Imagem:</strong> " . $produto['imagem'] . "<br>";
        echo "</div>";
    }
    
} catch(PDOException $e) {
    echo "<p style='color:red;'>Erro: " . $e->getMessage() . "</p>";
}
?>