<?php
// testar_pedido.php - Para testar o sistema sem fazer pagamento
session_start();

// Incluir conexão corretamente
include 'conexao.php';

// Verificar se a conexão foi estabelecida
if (!isset($database) || !$database) {
    die("Erro: Não foi possível conectar ao banco de dados.");
}

// Obter a conexão PDO
$db = $database->getConnection();

include 'receipt_generator.php';

echo "<h2>Teste do Sistema de Comprovantes</h2>";

// Criar um pedido de teste
try {
    // Verificar se o usuário ID 1 existe
    $stmt = $db->prepare("SELECT id FROM usuarios WHERE id = 1");
    $stmt->execute();
    $usuario_existe = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario_existe) {
        echo "<p style='color: orange;'>Aviso: Usuário ID 1 não existe. Criando usuário de teste...</p>";
        
        // Criar usuário de teste
        $stmt = $db->prepare("
            INSERT INTO usuarios (nome, email, senha, telefone, endereco) 
            VALUES ('Cliente Teste', 'teste@lavelle.com', '123456', '(11) 99999-9999', 'Rua Teste, 123 - São Paulo/SP')
        ");
        $stmt->execute();
        echo "<p>Usuário de teste criado com sucesso.</p>";
    }
    
    // Verificar se o produto ID 1 existe
    $stmt = $db->prepare("SELECT id FROM produtos WHERE id = 1");
    $stmt->execute();
    $produto_existe = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$produto_existe) {
        echo "<p style='color: orange;'>Aviso: Produto ID 1 não existe. Criando produto de teste...</p>";
        
        // Criar produto de teste
        $stmt = $db->prepare("
            INSERT INTO produtos (nome, descricao, preco, categoria) 
            VALUES ('Perfume Teste', 'Fragrância de teste para demonstração', 299.90, 'Compartilhável')
        ");
        $stmt->execute();
        echo "<p>Produto de teste criado com sucesso.</p>";
    }

    // Inserir pedido
    $stmt = $db->prepare("
        INSERT INTO pedidos (usuario_id, total, status, metodo_pagamento, endereco_entrega) 
        VALUES (1, 299.90, 'confirmado', 'cartao', 'Rua Teste, 123 - Centro\nSão Paulo/SP - CEP: 01234-567')
    ");
    $stmt->execute();
    
    $pedido_id = $db->lastInsertId();
    
    echo "<p>Pedido #{$pedido_id} criado com sucesso!</p>";
    
    // Adicionar itens de teste
    $stmt = $db->prepare("
        INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario, subtotal) 
        VALUES (?, 1, 1, 299.90, 299.90)
    ");
    $stmt->execute([$pedido_id]);
    
    echo "<p>Item do pedido adicionado com sucesso!</p>";
    
    // Gerar comprovante
    $receiptGenerator = new ReceiptGenerator();
    $filename = $receiptGenerator->generateReceipt($pedido_id, $db);
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 15px 0;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✅ Comprovante gerado com sucesso!</h3>";
    echo "<p><strong>Arquivo:</strong> {$filename}</p>";
    echo "</div>";
    
    // Links de ação
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='comprovantes/{$filename}' target='_blank' style='background: #8b7355; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin-right: 10px; display: inline-block;'>";
    echo "📄 Abrir Comprovante";
    echo "</a>";
    
    echo "<a href='admin/pedidos.php' style='background: #333; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; margin-right: 10px; display: inline-block;'>";
    echo "📊 Ver no Dashboard";
    echo "</a>";
    
    echo "<a href='admin/dashboard.php' style='background: #17a2b8; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>";
    echo "🏠 Ir para Dashboard";
    echo "</a>";
    echo "</div>";
    
    // Informações adicionais
    echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px;'>";
    echo "<h4>Informações do Teste:</h4>";
    echo "<ul>";
    echo "<li><strong>Pedido ID:</strong> {$pedido_id}</li>";
    echo "<li><strong>Status:</strong> Confirmado</li>";
    echo "<li><strong>Método de Pagamento:</strong> Cartão</li>";
    echo "<li><strong>Valor Total:</strong> R$ 299,90</li>";
    echo "<li><strong>Comprovante:</strong> comprovantes/{$filename}</li>";
    echo "</ul>";
    echo "</div>";
    
} catch(PDOException $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "<h3 style='margin-top: 0;'>❌ Erro no teste:</h3>";
    echo "<p><strong>Mensagem:</strong> " . $e->getMessage() . "</p>";
    
    // Verificar se as tabelas existem
    try {
        $tables = $db->query("SHOW TABLES LIKE 'pedidos'")->fetch();
        if (!$tables) {
            echo "<p style='color: #856404;'>⚠️ A tabela 'pedidos' não existe. Execute o script de criação de tabelas.</p>";
        }
        
        $tables = $db->query("SHOW TABLES LIKE 'usuarios'")->fetch();
        if (!$tables) {
            echo "<p style='color: #856404;'>⚠️ A tabela 'usuarios' não existe.</p>";
        }
        
        $tables = $db->query("SHOW TABLES LIKE 'produtos'")->fetch();
        if (!$tables) {
            echo "<p style='color: #856404;'>⚠️ A tabela 'produtos' não existe.</p>";
        }
        
    } catch (Exception $e2) {
        echo "<p><strong>Erro ao verificar tabelas:</strong> " . $e2->getMessage() . "</p>";
    }
    echo "</div>";
}
?>