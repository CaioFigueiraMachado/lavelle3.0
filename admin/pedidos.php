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

// Verificar/Criar pasta comprovantes
$comprovantes_dir = '../comprovantes';
if (!is_dir($comprovantes_dir)) {
    if (!mkdir($comprovantes_dir, 0755, true)) {
        $_SESSION['mensagem_erro'] = "Erro ao criar pasta comprovantes. Verifique as permissões.";
    }
}

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
        
        // Redirecionar de volta para a lista de pedidos
        $filtros_url = '';
        if ($filtro_status) $filtros_url .= '&status=' . $filtro_status;
        if ($filtro_data) $filtros_url .= '&data=' . $filtro_data;
        
        header("Location: pedidos.php?" . ltrim($filtros_url, '&'));
        exit;
        
    } catch (Exception $e) {
        $_SESSION['mensagem_erro'] = "Erro ao gerar comprovante: " . $e->getMessage();
        
        // Manter os filtros ao redirecionar
        $filtros_url = '';
        if ($filtro_status) $filtros_url .= '&status=' . $filtro_status;
        if ($filtro_data) $filtros_url .= '&data=' . $filtro_data;
        
        header("Location: pedidos.php?" . ltrim($filtros_url, '&'));
        exit;
    }
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

// Função para verificar se comprovante existe
function comprovanteExiste($pedido_id) {
    $possible_files = [
        "../comprovantes/comprovante_pedido_{$pedido_id}.php",
        "../comprovantes/comprovante_pedido_{$pedido_id}.html",
        "comprovantes/comprovante_pedido_{$pedido_id}.php",
        "comprovantes/comprovante_pedido_{$pedido_id}.html"
    ];
    
    foreach ($possible_files as $file) {
        if (file_exists($file)) {
            return $file;
        }
    }
    return false;
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <h1>Gestão de Pedidos</h1>
        <p>Gerencie todos os pedidos do sistema</p>
        <div class="header-actions">
           
        </div>
    </div>

    <?php if(isset($_SESSION['mensagem_sucesso'])): ?>
        <div class="alert success">
            <i class="fas fa-check-circle"></i>
            <?php echo $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['mensagem_erro'])): ?>
        <div class="alert error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?>
        </div>
    <?php endif; ?>

    <!-- Dashboard Stats -->
    <div class="stats-grid-small">
        <div class="stat-card-small">
            <div class="">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="stat-number"><?php echo count($pedidos); ?></div>
            <div class="stat-label">Total de Pedidos</div>
        </div>
        <div class="stat-card-small">
            <div class="">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number"><?php echo count(array_filter($pedidos, function($p) { return $p['status'] == 'pendente'; })); ?></div>
            <div class="stat-label">Pendentes</div>
        </div>
        <div class="stat-card-small">
            <div class="">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number"><?php echo count(array_filter($pedidos, function($p) { return $p['status'] == 'confirmado'; })); ?></div>
            <div class="stat-label">Confirmados</div>
        </div>
        <div class="stat-card-small">
            <div class="">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-number">R$ <?php echo number_format(array_sum(array_column($pedidos, 'total')), 2, ',', '.'); ?></div>
            <div class="stat-label">Faturamento Total</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-card">
        <div class="filter-header">
            <h3><i class="fas fa-filter"></i> Filtros Avançados</h3>
            <span class="results-info"><?php echo count($pedidos); ?> pedido(s) encontrado(s)</span>
        </div>
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Status do Pedido:</label>
                    <select name="status" class="status-filter">
                        <option value="">Todos os Status</option>
                        <option value="pendente" <?php echo $filtro_status == 'pendente' ? 'selected' : ''; ?>>🟡 Pendente</option>
                        <option value="confirmado" <?php echo $filtro_status == 'confirmado' ? 'selected' : ''; ?>>🟢 Confirmado</option>
                        <option value="enviado" <?php echo $filtro_status == 'enviado' ? 'selected' : ''; ?>>🔵 Enviado</option>
                        <option value="entregue" <?php echo $filtro_status == 'entregue' ? 'selected' : ''; ?>>⚫ Entregue</option>
                        <option value="cancelado" <?php echo $filtro_status == 'cancelado' ? 'selected' : ''; ?>>🔴 Cancelado</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Data do Pedido:</label>
                    <input type="date" name="data" value="<?php echo $filtro_data; ?>" class="date-filter">
                </div>
                
                <div class="filter-group">
                    <label class="hidden-label">Ações</label>
                    <div class="filter-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-filter"></i> Aplicar Filtros
                        </button>
                        <a href="pedidos.php" class="btn-outline">
                            <i class="fas fa-times"></i> Limpar
                        </a>
                    </div>
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
                <div class="export-actions">
                    
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <?php if(!empty($pedidos)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th class="col-cliente">Cliente</th>
                            <th class="col-data">Data/Hora</th>
                            <th class="col-total">Total</th>
                            <th class="col-status">Status</th>
                            <th class="col-pagamento">Pagamento</th>
                            <th class="col-acoes" width="200">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pedidos as $pedido): 
                            $comprovante_path = comprovanteExiste($pedido['id']);
                            $comprovante_existe = ($comprovante_path !== false);
                        ?>
                        <tr class="order-row" data-status="<?php echo $pedido['status']; ?>">
                            <td class="col-id">
                                <div class="order-info">
                                    <strong class="order-number">#<?php echo str_pad($pedido['id'], 4, '0', STR_PAD_LEFT); ?></strong>
                                    <?php if($pedido['status'] == 'pendente'): ?>
                                        <span class="badge-new">NOVO</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="col-cliente">
                                <span class="customer-name"><?php echo htmlspecialchars($pedido['cliente_nome']); ?></span>
                            </td>
                            <td class="col-data">
                                <div class="datetime-info">
                                    <span class="date-text"><?php echo date('d/m/Y', strtotime($pedido['data_pedido'])); ?></span>
                                    <span class="time-text"><?php echo date('H:i', strtotime($pedido['data_pedido'])); ?></span>
                                </div>
                            </td>
                            <td class="col-total">
                                <strong class="amount-value">R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></strong>
                            </td>
                            <td class="col-status">
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
                            <td class="col-pagamento">
                                <span class="payment-method">
                                    <?php 
                                    $metodo = $pedido['metodo_pagamento'] ?? 'Não informado';
                                    echo ucfirst($metodo);
                                    ?>
                                </span>
                            </td>
                            <td class="col-acoes">
                                <div class="action-buttons">
                                    <button class="btn-action btn-view" onclick="window.location.href='pedido_detalhes.php?id=<?php echo $pedido['id']; ?>'" title="Ver detalhes completos">
                                        <i class="fas fa-eye"></i>
                                        <span class="btn-text">Detalhes</span>
                                    </button>
                                    <button class="btn-action btn-generate" onclick="window.location.href='?gerar_comprovante=<?php echo $pedido['id']; ?>'" title="Gerar comprovante">
                                        <i class="fas fa-file-pdf"></i>
                                        <span class="btn-text">Gerar</span>
                                    </button>
                                    <?php if($comprovante_existe): ?>
                                        <button class="btn-action btn-open" onclick="window.open('<?php echo $comprovante_path; ?>', '_blank')" title="Abrir comprovante">
                                            <i class="fas fa-external-link-alt"></i>
                                            <span class="btn-text">Abrir</span>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-action btn-open disabled" title="Comprovante não gerado" disabled>
                                            <i class="fas fa-external-link-alt"></i>
                                            <span class="btn-text">Abrir</span>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn-action btn-delete" onclick="confirmarExclusao(<?php echo $pedido['id']; ?>)" title="Excluir pedido">
                                        <i class="fas fa-trash"></i>
                                        <span class="btn-text">Excluir</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>Nenhum pedido encontrado</h3>
                    <p>Não há pedidos correspondentes aos critérios de pesquisa selecionados.</p>
                    <a href="pedidos.php" class="btn-primary">
                        <i class="fas fa-refresh"></i> Limpar Filtros
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.stats-grid-small {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card-small {
    background: white;
    padding: 25px 20px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #ffffffff;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.stat-card-small:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
}

.stat-card-small .stat-icon {
    width: 50px;
    height: 50px;
    background: #ffffffff;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
    color: white;
    font-size: 20px;
}

.stat-card-small .stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #000000ff;
    margin-bottom: 8px;
    font-family: 'Arial', sans-serif;
}

.stat-card-small .stat-label {
    color: #666;
    font-size: 0.9rem;
    font-weight: 500;
}

.header-actions {
    margin-top: 10px;
}

/* Alertas Melhorados */
.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
    border: 1px solid transparent;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 500;
}

.alert.success {
    background: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
    border-left: 4px solid #28a745;
}

.alert.error {
    background: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
    border-left: 4px solid #dc3545;
}

.alert i {
    font-size: 18px;
}

/* Filtros Melhorados */
.filters-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 25px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.filter-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f8f9fa;
}

.filter-header h3 {
    color: #2c3e50;
    font-size: 18px;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.results-info {
    background: #000000ff;
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.filter-form {
    margin-top: 0;
}

.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-group label {
    font-weight: 600;
    font-size: 14px;
    color: #495057;
}

.hidden-label {
    opacity: 0;
    pointer-events: none;
}

.status-filter, .date-filter {
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.status-filter:focus, .date-filter:focus {
    outline: none;
    border-color: #8b7355;
    box-shadow: 0 0 0 3px rgba(139, 115, 85, 0.1);
}

.filter-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

/* Tabela Melhorada */
.content-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 2px solid #f8f9fa;
}

.card-header h2 {
    color: #2c3e50;
    font-size: 22px;
    font-weight: 700;
    margin: 0;
}

.card-actions {
    display: flex;
    align-items: center;
    gap: 20px;
}

.total-info {
    color: #6c757d;
    font-size: 14px;
    font-weight: 500;
    background: #f8f9fa;
    padding: 8px 16px;
    border-radius: 20px;
    border: 1px solid #e9ecef;
}

.export-actions {
    display: flex;
    gap: 10px;
}

.table-responsive {
    overflow-x: auto;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    min-width: 1000px;
}

.data-table th {
    background: #f8f9fa;
    padding: 16px 12px;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #e9ecef;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table td {
    padding: 16px 12px;
    border-bottom: 1px solid #f1f3f4;
    font-size: 14px;
    color: #495057;
}

.data-table tr:last-child td {
    border-bottom: none;
}

.order-row:hover {
    background: #f8f9fa;
}

/* Colunas Específicas */
.col-id {
    width: 120px;
}

.col-cliente {
    width: 200px;
}

.col-data {
    width: 140px;
}

.col-total {
    width: 120px;
}

.col-status {
    width: 160px;
}

.col-pagamento {
    width: 130px;
}

.col-acoes {
    width: 200px;
}

.order-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.order-number {
    font-family: 'Courier New', monospace;
    font-weight: 600;
}

.customer-name {
    font-weight: 500;
    color: #2c3e50;
}

.datetime-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.date-text {
    font-weight: 500;
    color: #2c3e50;
}

.time-text {
    font-size: 12px;
    color: #6c757d;
}

.amount-value {
    color: #28a745;
    font-weight: 600;
}

/* Badges e Status */
.badge-new {
    background: #e74c3c;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.payment-method {
    background: #e8f5e8;
    color: #28a745;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    border: 1px solid #d4edda;
}

/* Status Select Melhorado */
.status-form {
    margin: 0;
}

.status-select {
    padding: 8px 12px;
    border: 2px solid #e9ecef;
    border-radius: 6px;
    font-size: 12px;
    cursor: pointer;
    background: white;
    transition: all 0.3s ease;
    min-width: 140px;
    font-weight: 500;
}

.status-select:focus {
    outline: none;
    border-color: #8b7355;
    box-shadow: 0 0 0 2px rgba(139, 115, 85, 0.1);
}

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

/* Botões de Ação Melhorados */
.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-action {
    padding: 8px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 500;
    text-decoration: none;
    min-width: 80px;
    justify-content: center;
}

.btn-action:hover:not(.disabled) {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.btn-view {
    background: #17a2b8;
    color: white;
}

.btn-generate {
    background: #28a745;
    color: white;
}

.btn-open {
    background: #ffc107;
    color: #212529;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-action.disabled {
    background: #6c757d;
    color: white;
    cursor: not-allowed;
    opacity: 0.6;
}

.btn-action.disabled:hover {
    transform: none;
    box-shadow: none;
}

.btn-text {
    font-size: 11px;
}

/* Botões Principais */
.btn-primary {
    background: #000000ff;
    color: white;
    padding: 10px 18px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background: #7a6347;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    color: white;
    text-decoration: none;
}

.btn-outline {
    background: white;
    color: #000000ff;
    padding: 10px 18px;
    border: 2px solid #000000ff;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-outline:hover {
    background: #8b7355;
    color: white;
    transform: translateY(-1px);
    text-decoration: none;
}

.btn-small {
    padding: 6px 12px;
    font-size: 12px;
}

/* Estado Vazio Melhorado */
.empty-state {
    text-align: center;
    padding: 60px 40px;
    color: #6c757d;
    background: #f8f9fa;
    border-radius: 12px;
    border: 2px dashed #dee2e6;
}

.empty-icon {
    font-size: 64px;
    color: #ced4da;
    margin-bottom: 20px;
}

.empty-state h3 {
    margin-bottom: 12px;
    color: #495057;
    font-size: 20px;
    font-weight: 600;
}

.empty-state p {
    font-size: 15px;
    margin-bottom: 25px;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.5;
}

/* Destaque para pedidos pendentes */
.order-row[data-status="pendente"] {
    background: linear-gradient(90deg, #fff9e6 0%, white 50px);
    border-left: 4px solid #ffc107;
}

/* Responsividade */
@media (max-width: 768px) {
    .stats-grid-small {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .filter-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .filter-actions {
        flex-direction: column;
    }
    
    .card-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .card-actions {
        width: 100%;
        justify-content: space-between;
    }
    
    .action-buttons {
        justify-content: flex-start;
    }
    
    .btn-action {
        min-width: 70px;
        padding: 6px 8px;
    }
    
    .btn-text {
        display: none;
    }
}

@media (max-width: 480px) {
    .stats-grid-small {
        grid-template-columns: 1fr;
    }
    
    .filter-header {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 8px;
    }
    
    .btn-action {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function atualizarStatus(select) {
    const novoStatus = select.value;
    const pedidoId = select.form.querySelector('input[name="pedido_id"]').value;
    const statusAtual = select.options[select.selectedIndex].textContent;
    
    if (confirm(`Deseja alterar o status do pedido #${pedidoId} para "${statusAtual}"?`)) {
        // Feedback visual
        select.disabled = true;
        select.style.opacity = '0.7';
        select.style.cursor = 'wait';
        
        // Enviar formulário
        select.form.submit();
    } else {
        // Restaurar valor original
        select.blur();
    }
}

function confirmarExclusao(pedidoId) {
    Swal.fire({
        title: '⚠️ Confirmação de Exclusão',
        html: `<strong>Tem certeza que deseja excluir o pedido #${pedidoId}?</strong><br><br>
              <div style="text-align: left; background: #fff3f3; padding: 12px; border-radius: 8px; border-left: 4px solid #dc3545;">
                <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>
                <strong style="color: #721c24;">Atenção:</strong>
                <ul style="margin: 8px 0 0 0; padding-left: 20px; color: #721c24;">
                    <li>Esta ação é <strong>IRREVERSÍVEL</strong></li>
                    <li>Todos os dados do pedido serão permanentemente removidos</li>
                    <li>Itens do pedido também serão excluídos</li>
                    <li>Comprovantes associados serão removidos</li>
                </ul>
              </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir pedido!',
        cancelButtonText: 'Cancelar',
        customClass: {
            popup: 'sweetalert-custom',
            confirmButton: 'sweetalert-confirm-delete',
            cancelButton: 'sweetalert-cancel'
        },
        buttonsStyling: true,
        reverseButtons: true,
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Excluindo pedido...',
                text: 'Aguarde enquanto removemos o pedido do sistema',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Redirecionar para exclusão
            window.location.href = `?excluir_pedido=${pedidoId}`;
        }
    });
    
    return false;
}

function exportarDados() {
    // Simulação de exportação - pode ser implementada posteriormente
    Swal.fire({
        title: 'Exportar Dados',
        text: 'Funcionalidade de exportação será implementada em breve!',
        icon: 'info',
        confirmButtonText: 'Entendi',
        confirmButtonColor: '#8b7355'
    });
}

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    // Destacar pedidos pendentes
    document.querySelectorAll('.order-row[data-status="pendente"]').forEach(row => {
        row.style.background = 'linear-gradient(90deg, #fff9e6 0%, white 50px)';
        row.style.borderLeft = '4px solid #ffc107';
    });
    
    // Auto-submit no filtro de data
    const dataInput = document.querySelector('.date-filter');
    if (dataInput && dataInput.value) {
        dataInput.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    // Adicionar tooltips para botões de ação
    const tooltips = {
        'btn-view': 'Visualizar detalhes completos do pedido',
        'btn-generate': 'Gerar comprovante em PDF',
        'btn-open': 'Abrir comprovante gerado',
        'btn-delete': 'Excluir pedido permanentemente'
    };
    
    document.querySelectorAll('.btn-action').forEach(btn => {
        const className = Array.from(btn.classList).find(cls => cls.startsWith('btn-'));
        if (className && tooltips[className]) {
            btn.setAttribute('title', tooltips[className]);
        }
    });
});

// Estilos customizados para o SweetAlert2
const style = document.createElement('style');
style.textContent = `
    .sweetalert-custom {
        border-radius: 12px;
        border: 2px solid #e9ecef;
    }
    .sweetalert-confirm-delete {
        background: #dc3545 !important;
        border: none !important;
        padding: 10px 25px !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
    }
    .sweetalert-confirm-delete:hover {
        background: #c82333 !important;
        transform: translateY(-1px);
    }
    .sweetalert-cancel {
        background: #6c757d !important;
        border: none !important;
        padding: 10px 25px !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
    }
    .sweetalert-cancel:hover {
        background: #5a6268 !important;
        transform: translateY(-1px);
    }
    .swal2-popup {
        font-size: 16px !important;
    }
`;
document.head.appendChild(style);
</script>

<?php include 'includes/footer.php'; ?>