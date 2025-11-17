<?php
// admin/pedidos.php
session_start();
if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

include 'config/database.php';
include '../receipt_generator.php';

$database = new Database();
$db = $database->getConnection();

// Processar filtros
$filtro_status = $_GET['status'] ?? '';
$filtro_data = $_GET['data'] ?? '';

// Buscar pedidos
$query = "
    SELECT p.*, u.nome as cliente_nome 
    FROM pedidos p 
    LEFT JOIN usuarios u ON p.usuario_id = u.id 
    WHERE 1=1
";

$params = [];

if ($filtro_status) {
    $query .= " AND p.status = ?";
    $params[] = $filtro_status;
}

if ($filtro_data) {
    $query .= " AND DATE(p.data_pedido) = ?";
    $params[] = $filtro_data;
}

$query .= " ORDER BY p.data_pedido DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Processar geração de comprovante
if (isset($_GET['gerar_comprovante'])) {
    $pedido_id = $_GET['gerar_comprovante'];
    $receiptGenerator = new ReceiptGenerator();
    
    try {
        $filename = $receiptGenerator->generateReceipt($pedido_id, $db);
        $_SESSION['mensagem_sucesso'] = "Comprovante gerado com sucesso!";
        
        // Abrir comprovante em nova aba
        echo "<script>window.open('../comprovantes/{$filename}', '_blank');</script>";
        
    } catch (Exception $e) {
        $_SESSION['mensagem_erro'] = "Erro ao gerar comprovante: " . $e->getMessage();
    }
    
    // Usar JavaScript para redirecionar sem perder a mensagem
    echo "<script>setTimeout(function() { window.location.href = 'pedidos.php'; }, 100);</script>";
    exit;
}

// Processar alteração de status
if (isset($_POST['alterar_status'])) {
    $pedido_id = $_POST['pedido_id'];
    $novo_status = $_POST['novo_status'];
    
    $stmt = $db->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
    if ($stmt->execute([$novo_status, $pedido_id])) {
        $_SESSION['mensagem_sucesso'] = "Status do pedido #" . $pedido_id . " atualizado para: " . $novo_status;
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao atualizar status do pedido.";
    }
    
    // Manter os filtros ao redirecionar
    $filtros_url = '';
    if ($filtro_status) $filtros_url .= '&status=' . $filtro_status;
    if ($filtro_data) $filtros_url .= '&data=' . $filtro_data;
    
    header("Location: pedidos.php?" . ltrim($filtros_url, '&'));
    exit;
}

// Processar exclusão de pedido
if (isset($_GET['excluir_pedido'])) {
    $pedido_id = $_GET['excluir_pedido'];
    
    try {
        // Iniciar transação para garantir que ambos os deletes funcionem
        $db->beginTransaction();
        
        // Primeiro excluir os itens do pedido
        $stmt = $db->prepare("DELETE FROM pedido_itens WHERE pedido_id = ?");
        $stmt->execute([$pedido_id]);
        
        // Depois excluir o pedido
        $stmt = $db->prepare("DELETE FROM pedidos WHERE id = ?");
        if ($stmt->execute([$pedido_id])) {
            $db->commit();
            $_SESSION['mensagem_sucesso'] = "Pedido #" . $pedido_id . " excluído com sucesso!";
        } else {
            $db->rollBack();
            $_SESSION['mensagem_erro'] = "Erro ao excluir pedido.";
        }
        
    } catch (PDOException $e) {
        $db->rollBack();
        $_SESSION['mensagem_erro'] = "Erro ao excluir pedido: " . $e->getMessage();
    }
    
    header("Location: pedidos.php");
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <h1>Gestão de Pedidos</h1>
        <p>Gerencie todos os pedidos do sistema</p>
        <div class="header-actions">
            <a href="verificar_novos_pedidos.php" class="btn-primary">
                <i class="fas fa-sync-alt"></i> Verificar Novos Pedidos
            </a>
        </div>
    </div>

    <?php if(isset($_SESSION['mensagem_sucesso'])): ?>
        <div class="alert success">
            <?php echo $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['mensagem_erro'])): ?>
        <div class="alert error">
            <?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?>
        </div>
    <?php endif; ?>

    <!-- Estatísticas Rápidas -->
    <div class="stats-grid-small">
        <div class="stat-card-small">
            <div class="stat-number"><?php echo count($pedidos); ?></div>
            <div class="stat-label">Total de Pedidos</div>
        </div>
        <div class="stat-card-small">
            <div class="stat-number"><?php echo count(array_filter($pedidos, function($p) { return $p['status'] == 'pendente'; })); ?></div>
            <div class="stat-label">Pendentes</div>
        </div>
        <div class="stat-card-small">
            <div class="stat-number"><?php echo count(array_filter($pedidos, function($p) { return $p['status'] == 'confirmado'; })); ?></div>
            <div class="stat-label">Confirmados</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-card">
        <h3>Filtros</h3>
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Status:</label>
                    <select name="status">
                        <option value="">Todos</option>
                        <option value="pendente" <?php echo $filtro_status == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                        <option value="confirmado" <?php echo $filtro_status == 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                        <option value="enviado" <?php echo $filtro_status == 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                        <option value="entregue" <?php echo $filtro_status == 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                        <option value="cancelado" <?php echo $filtro_status == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Data:</label>
                    <input type="date" name="data" value="<?php echo $filtro_data; ?>">
                </div>
                
                <div class="filter-group">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="pedidos.php" class="btn-outline">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Pedidos -->
    <div class="content-card">
        <div class="card-header">
            <h2>Lista de Pedidos</h2>
            <div class="card-actions">
                <span class="total-info">Mostrando <?php echo count($pedidos); ?> pedido(s)</span>
            </div>
        </div>
        
        <div class="table-responsive">
            <?php if(!empty($pedidos)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Data</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pagamento</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pedidos as $pedido): ?>
                        <tr>
                            <td>
                                <strong>#<?php echo str_pad($pedido['id'], 3, '0', STR_PAD_LEFT); ?></strong>
                                <?php if($pedido['status'] == 'pendente'): ?>
                                    <span class="badge-new">NOVO</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($pedido['cliente_nome']); ?></td>
                            <td>
                                <small><?php echo date('d/m/Y', strtotime($pedido['data_pedido'])); ?></small>
                                <br>
                                <small style="color: #666;"><?php echo date('H:i', strtotime($pedido['data_pedido'])); ?></small>
                            </td>
                            <td><strong>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></strong></td>
                            <td>
                                <form method="POST" action="" class="status-form">
                                    <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                    <select name="novo_status" class="status-select status-<?php echo $pedido['status']; ?>" onchange="atualizarStatus(this)">
                                        <option value="pendente" <?php echo $pedido['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                        <option value="confirmado" <?php echo $pedido['status'] == 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                                        <option value="enviado" <?php echo $pedido['status'] == 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                                        <option value="entregue" <?php echo $pedido['status'] == 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                                        <option value="cancelado" <?php echo $pedido['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                    </select>
                                    <button type="submit" name="alterar_status" class="btn-status" style="display: none;">Atualizar</button>
                                </form>
                            </td>
                            <td>
                                <span class="payment-method">
                                    <?php 
                                    $metodo = $pedido['metodo_pagamento'] ?? 'Não informado';
                                    echo ucfirst($metodo);
                                    ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="pedido_detalhes.php?id=<?php echo $pedido['id']; ?>" class="btn-small btn-info" title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="?gerar_comprovante=<?php echo $pedido['id']; ?>" class="btn-small btn-success" title="Gerar Comprovante">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <a href="../comprovantes/comprovante_pedido_<?php echo $pedido['id']; ?>.html" 
                                       class="btn-small btn-warning" 
                                       target="_blank"
                                       title="Ver Comprovante">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="?excluir_pedido=<?php echo $pedido['id']; ?>" 
                                       class="btn-small btn-danger" 
                                       title="Excluir Pedido"
                                       onclick="return confirmarExclusao(<?php echo $pedido['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3>Nenhum pedido encontrado</h3>
                    <p>Não há pedidos correspondentes aos filtros aplicados.</p>
                    <a href="pedidos.php" class="btn-primary">Ver Todos os Pedidos</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.stats-grid-small {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
}

.stat-card-small {
    background: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-left: 4px solid #8b7355;
}

.stat-card-small .stat-number {
    font-size: 1.8rem;
    font-weight: bold;
    color: #8b7355;
    margin-bottom: 5px;
}

.stat-card-small .stat-label {
    color: #666;
    font-size: 0.9rem;
}

.header-actions {
    margin-top: 10px;
}

.badge-new {
    background: #e74c3c;
    color: white;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: bold;
    margin-left: 5px;
}

.payment-method {
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-icon {
    font-size: 48px;
    color: #ddd;
    margin-bottom: 20px;
}

.empty-state h3 {
    margin-bottom: 10px;
    color: #333;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.card-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.total-info {
    color: #666;
    font-size: 14px;
}

/* Resto do CSS permanece igual */
.status-select {
    padding: 6px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
    background: white;
    transition: all 0.3s;
    min-width: 120px;
}

.status-select:focus {
    outline: none;
    border-color: #8b7355;
    box-shadow: 0 0 5px rgba(139, 115, 85, 0.3);
}

.action-buttons {
    display: flex;
    gap: 5px;
    justify-content: center;
}

.btn-small {
    padding: 6px 8px;
    font-size: 11px;
    text-decoration: none;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    gap: 3px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-small:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

/* Estilos para status */
.status-pendente { 
    background: #fff3cd !important; 
    color: #856404; 
    border-color: #ffeaa7 !important;
}
.status-confirmado { 
    background: #d1ecf1 !important; 
    color: #0c5460; 
    border-color: #bee5eb !important;
}
.status-enviado { 
    background: #d4edda !important; 
    color: #155724; 
    border-color: #c3e6cb !important;
}
.status-entregue { 
    background: #e2e3e5 !important; 
    color: #383d41; 
    border-color: #d6d8db !important;
}
.status-cancelado { 
    background: #f8d7da !important; 
    color: #721c24; 
    border-color: #f5c6cb !important;
}
</style>

<script>
function atualizarStatus(select) {
    const novoStatus = select.value;
    const pedidoId = select.form.querySelector('input[name="pedido_id"]').value;
    const statusAtual = select.options[select.selectedIndex].textContent;
    
    if (confirm(`Deseja realmente alterar o status do pedido #${pedidoId} para "${statusAtual}"?`)) {
        select.disabled = true;
        select.style.background = '#f8f9fa';
        select.style.cursor = 'wait';
        select.form.submit();
    } else {
        // Reverter para o valor original (precisa ser implementado com mais lógica)
        location.reload();
    }
}

function confirmarExclusao(pedidoId) {
    return confirm(`⚠️ ATENÇÃO!\n\nDeseja realmente EXCLUIR o pedido #${pedidoId}?\n\nEsta ação não pode ser desfeita e todos os dados do pedido serão perdidos permanentemente.`);
}

// Atualizar automaticamente a cada 30 segundos para ver novos pedidos
setTimeout(function() {
    console.log('Verificando novos pedidos...');
    // Não recarregar automaticamente, apenas log
}, 30000);
</script>

<?php include 'includes/footer.php'; ?>