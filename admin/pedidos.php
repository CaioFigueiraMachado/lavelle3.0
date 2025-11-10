<?php
// admin/pedidos.php
session_start();
if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

include 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Buscar todos os pedidos
$pedidos = [];
try {
    $query = "SELECT p.*, u.nome as usuario_nome, u.email as usuario_email 
              FROM pedidos p 
              LEFT JOIN usuarios u ON p.usuario_id = u.id 
              ORDER BY p.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $pedidos = [];
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <h1>Gerenciar Pedidos</h1>
        <p>Visualize e gerencie todos os pedidos do sistema</p>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h2>Todos os Pedidos</h2>
        </div>
        <div class="table-responsive">
            <?php if(!empty($pedidos)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Email</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Método Pagamento</th>
                            <th>Data</th>
                          
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pedidos as $pedido): ?>
                        <tr>
                            <td>#<?php echo $pedido['id']; ?></td>
                            <td><?php echo htmlspecialchars($pedido['usuario_nome'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($pedido['usuario_email'] ?? 'N/A'); ?></td>
                            <td>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower($pedido['status']); ?>">
                                    <?php echo ucfirst($pedido['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($pedido['metodo_pagamento'] ?? 'N/A'); ?></td>
                            <td>
                                <?php 
                                if(isset($pedido['created_at'])) {
                                    echo date('d/m/Y H:i', strtotime($pedido['created_at']));
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </td>
                          
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 2rem;">
                    Nenhum pedido encontrado.
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-completado {
    background-color: #d4edda;
    color: #155724;
}

.status-processando {
    background-color: #fff3cd;
    color: #856404;
}

.status-pendente {
    background-color: #f8d7da;
    color: #721c24;
}

.btn-small {
    padding: 4px 8px;
    font-size: 12px;
    margin: 2px;
    display: inline-block;
}

.btn-primary {
    background-color: #007bff;
    color: white;
}

.btn-warning {
    background-color: #ffc107;
    color: #212529;
}

.action-buttons {
    display: flex;
    gap: 5px;
}
</style>

<?php include 'includes/footer.php'; ?>