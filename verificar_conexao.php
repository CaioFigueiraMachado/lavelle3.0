<?php
// verificar_conexao.php
session_start();

echo "<h2>Verificação do Sistema</h2>";

// Testar conexão com o banco
try {
    include 'conexao.php';
    
    if (!isset($database)) {
        throw new Exception("Objeto database não definido");
    }
    
    $db = $database->getConnection();
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ Conexão com o banco de dados estabelecida com sucesso!";
    echo "</div>";
    
    // Verificar tabelas existentes
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tabelas no banco de dados:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>{$table}</li>";
    }
    echo "</ul>";
    
    // Verificar tabelas necessárias
    $required_tables = ['usuarios', 'produtos', 'pedidos', 'pedido_itens'];
    $missing_tables = [];
    
    foreach ($required_tables as $table) {
        if (!in_array($table, $tables)) {
            $missing_tables[] = $table;
        }
    }
    
    if (empty($missing_tables)) {
        echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ Todas as tabelas necessárias existem!";
        echo "</div>";
    } else {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "⚠️ Tabelas faltando: " . implode(', ', $missing_tables);
        echo "<br><a href='criar_tabelas_completo.php' style='color: #856404;'>Criar tabelas faltantes</a>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ Erro na conexão: " . $e->getMessage();
    echo "</div>";
}

// Links úteis
echo "<div style='margin-top: 20px;'>";
echo "<h3>Links Úteis:</h3>";
echo "<ul>";
echo "<li><a href='criar_tabelas_completo.php'>Criar Tabelas do Sistema</a></li>";
echo "<li><a href='testar_pedido.php'>Testar Sistema de Pedidos</a></li>";
echo "<li><a href='admin/dashboard.php'>Dashboard Administrativo</a></li>";
echo "<li><a href='paginaprodutos.php'>Página de Produtos</a></li>";
echo "</ul>";
echo "</div>";
?>