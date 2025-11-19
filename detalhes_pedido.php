<?php
// detalhes_pedido.php
session_start();
include 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Verificar se o ID do pedido foi passado
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: perfil.php');
    exit();
}

$pedido_id = $_GET['id'];
$usuario_id = $_SESSION['id'];

// Buscar dados do pedido (apenas se pertencer ao usuário)
try {
    $sql = "SELECT p.*, u.nome as cliente_nome, u.email, u.telefone 
            FROM pedidos p 
            LEFT JOIN usuarios u ON p.usuario_id = u.id 
            WHERE p.id = ? AND p.usuario_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$pedido_id, $usuario_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pedido) {
        header('Location: perfil.php');
        exit();
    }
    
    // Buscar itens do pedido
    $sql_itens = "SELECT pi.*, pr.nome as produto_nome, pr.imagem as produto_imagem
                  FROM pedido_itens pi 
                  LEFT JOIN produtos pr ON pi.produto_id = pr.id 
                  WHERE pi.pedido_id = ?";
    $stmt_itens = $con->prepare($sql_itens);
    $stmt_itens->execute([$pedido_id]);
    $itens_pedido = $stmt_itens->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    header('Location: perfil.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido - LAVELLE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos similares ao perfil.php */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f5f0;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .page-header {
            text-align: center;
            padding: 60px 0 40px;
        }
        
        .page-header h1 {
            font-size: 36px;
            color: #000;
            margin-bottom: 10px;
        }
        
        .page-header p {
            color: #666;
            font-size: 18px;
        }
        
        .pedido-detalhes-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #8b7355;
        }
        
        .info-card h3 {
            color: #8b7355;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .info-item {
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        
        .status-pendente { background: #fff3cd; color: #856404; }
        .status-confirmado { background: #d1ecf1; color: #0c5460; }
        .status-enviado { background: #d4edda; color: #155724; }
        .status-entregue { background: #e8f5e8; color: #155724; }
        .status-cancelado { background: #f8d7da; color: #721c24; }
        
        .produtos-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        
        .produtos-table th,
        .produtos-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .produtos-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .produto-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .produto-imagem {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .total-section {
            text-align: right;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        
        .total-grande {
            font-size: 24px;
            font-weight: bold;
            color: #8b7355;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: #000;
            color: white;
        }
        
        .btn-primary:hover {
            background: #333;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #000;
            color: #000;
        }
        
        .btn-outline:hover {
            background: #000;
            color: white;
        }
        
        @media (max-width: 768px) {
            .pedido-detalhes-container {
                padding: 25px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Detalhes do Pedido</h1>
            <p>Pedido #<?php echo str_pad($pedido_id, 3, '0', STR_PAD_LEFT); ?></p>
        </div>
        
        <div class="pedido-detalhes-container">
            <!-- Informações do Pedido -->
            <div class="info-grid">
                <div class="info-card">
                    <h3>Informações do Pedido</h3>
                    <div class="info-item">
                        <span class="info-label">Número:</span>
                        <span>#<?php echo str_pad($pedido_id, 3, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Data:</span>
                        <span><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="status-badge status-<?php echo strtolower($pedido['status']); ?>">
                            <?php echo ucfirst($pedido['status']); ?>
                        </span>
                    </div>
                </div>
                
                <div class="info-card">
                    <h3>Pagamento</h3>
                    <div class="info-item">
                        <span class="info-label">Método:</span>
                        <span><?php echo ucfirst($pedido['metodo_pagamento']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Total:</span>
                        <span class="total-grande">R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></span>
                    </div>
                </div>
                
                <?php if (!empty($pedido['endereco_entrega'])): ?>
                <div class="info-card">
                    <h3>Endereço de Entrega</h3>
                    <div style="line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($pedido['endereco_entrega'])); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Produtos do Pedido -->
            <h3 style="margin-bottom: 20px; color: #000;">Produtos do Pedido</h3>
            <table class="produtos-table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($itens_pedido as $item): ?>
                    <tr>
                        <td>
                            <div class="produto-info">
                                <?php if (!empty($item['produto_imagem'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['produto_imagem']); ?>" 
                                         alt="<?php echo htmlspecialchars($item['produto_nome']); ?>" 
                                         class="produto-imagem">
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($item['produto_nome']); ?></span>
                            </div>
                        </td>
                        <td><?php echo $item['quantidade']; ?></td>
                        <td>R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="total-section">
                <div class="total-grande">
                    Total do Pedido: R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?>
                </div>
            </div>
        </div>
        
        <div class="actions">
            <?php 
            $comprovante_path = "comprovantes/comprovante_pedido_{$pedido_id}.html";
            if (file_exists($comprovante_path)): 
            ?>
                <a href="<?php echo $comprovante_path; ?>" target="_blank" class="btn btn-success">
                    <i class="fas fa-file-invoice"></i>
                    Ver Comprovante
                </a>
            <?php endif; ?>
            
            <a href="perfil.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                Voltar para Meus Pedidos
            </a>
            
            <a href="paginaprodutos.php" class="btn btn-outline">
                <i class="fas fa-shopping-cart"></i>
                Continuar Comprando
            </a>
        </div>
    </div>
</body>
</html>