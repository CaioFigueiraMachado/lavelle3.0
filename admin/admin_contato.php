<?php
// admin_contato.php
session_start();
if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

include 'config/database.php';
$database = new Database();
$db = $database->getConnection();

// Processar filtros
$filtro_status = $_GET['status'] ?? '';
$filtro_data = $_GET['data'] ?? '';
$filtro_assunto = $_GET['assunto'] ?? '';

// Processar fechamento de ticket
if (isset($_GET['action']) && $_GET['action'] == 'fechar' && isset($_GET['id'])) {
    $mensagem_id = $_GET['id'];
    
    try {
        $query_fechar = "UPDATE contato_mensagens SET status = 'fechada', updated_at = NOW() WHERE id = ?";
        $stmt_fechar = $db->prepare($query_fechar);
        $stmt_fechar->execute([$mensagem_id]);
        
        $_SESSION['mensagem_sucesso'] = "Ticket #" . $mensagem_id . " fechado com sucesso!";
    } catch (PDOException $e) {
        $_SESSION['mensagem_erro'] = "Erro ao fechar ticket: " . $e->getMessage();
    }
    
    // Manter os filtros ao redirecionar
    $filtros_url = '';
    if ($filtro_status) $filtros_url .= '&status=' . $filtro_status;
    if ($filtro_data) $filtros_url .= '&data=' . $filtro_data;
    if ($filtro_assunto) $filtros_url .= '&assunto=' . $filtro_assunto;
    
    header("Location: admin_contato.php?" . ltrim($filtros_url, '&'));
    exit;
}

// Processar reabertura de ticket
if (isset($_GET['action']) && $_GET['action'] == 'reabrir' && isset($_GET['id'])) {
    $mensagem_id = $_GET['id'];
    
    try {
        $query_reabrir = "UPDATE contato_mensagens SET status = 'aberta', updated_at = NOW() WHERE id = ?";
        $stmt_reabrir = $db->prepare($query_reabrir);
        $stmt_reabrir->execute([$mensagem_id]);
        
        $_SESSION['mensagem_sucesso'] = "Ticket #" . $mensagem_id . " reaberto com sucesso!";
    } catch (PDOException $e) {
        $_SESSION['mensagem_erro'] = "Erro ao reabrir ticket: " . $e->getMessage();
    }
    
    // Manter os filtros ao redirecionar
    $filtros_url = '';
    if ($filtro_status) $filtros_url .= '&status=' . $filtro_status;
    if ($filtro_data) $filtros_url .= '&data=' . $filtro_data;
    if ($filtro_assunto) $filtros_url .= '&assunto=' . $filtro_assunto;
    
    header("Location: admin_contato.php?" . ltrim($filtros_url, '&'));
    exit;
}

// Processar exclusão de mensagem
if (isset($_GET['excluir_mensagem'])) {
    $mensagem_id = $_GET['excluir_mensagem'];
    
    try {
        // Iniciar transação
        $db->beginTransaction();
        
        // Primeiro excluir as respostas
        $stmt = $db->prepare("DELETE FROM contato_respostas WHERE mensagem_id = ?");
        $stmt->execute([$mensagem_id]);
        
        // Depois excluir a mensagem
        $stmt = $db->prepare("DELETE FROM contato_mensagens WHERE id = ?");
        if ($stmt->execute([$mensagem_id])) {
            $db->commit();
            $_SESSION['mensagem_sucesso'] = "Mensagem #" . $mensagem_id . " excluída com sucesso!";
        } else {
            $db->rollBack();
            $_SESSION['mensagem_erro'] = "Erro ao excluir mensagem.";
        }
        
    } catch (PDOException $e) {
        $db->rollBack();
        $_SESSION['mensagem_erro'] = "Erro ao excluir mensagem: " . $e->getMessage();
    }
    
    header("Location: admin_contato.php");
    exit;
}

// Buscar mensagens com filtros
$query = "
    SELECT cm.*, u.nome, u.email, 
          (SELECT COUNT(*) FROM contato_respostas cr WHERE cr.mensagem_id = cm.id AND cr.remetente = 'cliente' AND cr.lida = 0) as novas_respostas
    FROM contato_mensagens cm 
    LEFT JOIN usuarios u ON cm.usuario_id = u.id 
    WHERE 1=1
";

$params = [];

if ($filtro_status) {
    $query .= " AND cm.status = ?";
    $params[] = $filtro_status;
}

if ($filtro_data) {
    $query .= " AND DATE(cm.created_at) = ?";
    $params[] = $filtro_data;
}

if ($filtro_assunto) {
    $query .= " AND cm.assunto LIKE ?";
    $params[] = '%' . $filtro_assunto . '%';
}

$query .= " ORDER BY 
    CASE 
        WHEN cm.status = 'aberta' THEN 1
        WHEN cm.status = 'respondida' THEN 2
        ELSE 3
    END,
    cm.updated_at DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$mensagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contadores para estatísticas
$total_mensagens = count($mensagens);
$mensagens_abertas = array_filter($mensagens, function($msg) { return $msg['status'] == 'aberta'; });
$mensagens_respondidas = array_filter($mensagens, function($msg) { return $msg['status'] == 'respondida'; });
$mensagens_fechadas = array_filter($mensagens, function($msg) { return $msg['status'] == 'fechada'; });

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <h1>Gestão de Mensagens</h1>
        <p>Gerencie todas as mensagens de contato do sistema</p>
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
          
           <br> <div class="stat-number"><?php echo $total_mensagens; ?></div>
            <div class="stat-label">Total de Mensagens</div>
        </div>
        <div class="stat-card-small">
            
            <br><div class="stat-number"><?php echo count($mensagens_abertas); ?></div>
            <div class="stat-label">Abertas</div>
        </div>
        <div class="stat-card-small">
           
           <br> <div class="stat-number"><?php echo count($mensagens_respondidas); ?></div>
            <div class="stat-label">Respondidas</div>
        </div>
        <div class="stat-card-small">
          
          <br>  <div class="stat-number"><?php echo count($mensagens_fechadas); ?></div>
            <div class="stat-label">Fechadas</div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="filters-card">
        <div class="filter-header">
            <h3><i class="fas fa-filter"></i> Filtros Avançados</h3>
            <span class="results-info"><?php echo count($mensagens); ?> mensagem(ns) encontrada(s)</span>
        </div>
        <form method="GET" class="filter-form">
            <div class="filter-row">
                <div class="filter-group">
                    <label>Status:</label>
                    <select name="status" class="status-filter">
                        <option value="">Todos os Status</option>
                        <option value="aberta" <?php echo $filtro_status == 'aberta' ? 'selected' : ''; ?>>🔴 Aberta</option>
                        <option value="respondida" <?php echo $filtro_status == 'respondida' ? 'selected' : ''; ?>>🟡 Respondida</option>
                        <option value="fechada" <?php echo $filtro_status == 'fechada' ? 'selected' : ''; ?>>🟢 Fechada</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Data:</label>
                    <input type="date" name="data" value="<?php echo $filtro_data; ?>" class="date-filter">
                </div>
                
                <div class="filter-group">
                    <label>Assunto:</label>
                    <input type="text" name="assunto" value="<?php echo htmlspecialchars($filtro_assunto); ?>" placeholder="Buscar por assunto..." class="search-filter">
                </div>

                <div class="filter-group">
                    <label class="hidden-label">Ações</label>
                    <div class="filter-actions">
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-filter"></i> Aplicar Filtros
                        </button>
                        <a href="admin_contato.php" class="btn-outline">
                            <i class="fas fa-times"></i> Limpar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Mensagens -->
    <div class="content-card">
        <div class="card-header">
            <h2>Lista de Mensagens</h2>
            <div class="card-actions">
                <span class="total-info">Mostrando <?php echo count($mensagens); ?> mensagem(ns)</span>
               
            </div>
        </div>
        
        <div class="table-responsive">
            <?php if(!empty($mensagens)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="col-id">ID</th>
                            <th class="col-cliente">Cliente</th>
                            <th class="col-assunto">Assunto</th>
                            <th class="col-status">Status</th>
                            <th class="col-data">Data</th>
                            <th class="col-atualizacao">Atualização</th>
                            <th class="col-novas">Novas</th>
                            <th class="col-acoes" width="220">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($mensagens as $mensagem): ?>
                        <tr class="message-row <?php echo $mensagem['novas_respostas'] > 0 ? 'new-message' : ''; ?>" data-status="<?php echo $mensagem['status']; ?>">
                            <td class="col-id">
                                <div class="message-info">
                                    <strong class="message-number">#<?php echo str_pad($mensagem['id'], 4, '0', STR_PAD_LEFT); ?></strong>
                                    <?php if($mensagem['status'] == 'aberta'): ?>
                                        <span class="badge-new">NOVA</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="col-cliente">
                                <div class="user-info">
                                    <span class="customer-name"><?php echo htmlspecialchars($mensagem['nome'] ?: 'Visitante'); ?></span>
                                    <span class="customer-email"><?php echo htmlspecialchars($mensagem['email'] ?: 'Sem e-mail'); ?></span>
                                </div>
                            </td>
                            <td class="col-assunto">
                                <div class="subject-info">
                                    <strong class="subject-text"><?php echo htmlspecialchars($mensagem['assunto']); ?></strong>
                                    <div class="message-preview">
                                        <?php echo htmlspecialchars(substr($mensagem['mensagem'], 0, 80) . (strlen($mensagem['mensagem']) > 80 ? '...' : '')); ?>
                                    </div>
                                </div>
                            </td>
                            <td class="col-status">
                                <span class="status-badge status-<?php echo $mensagem['status']; ?>">
                                    <?php echo ucfirst($mensagem['status']); ?>
                                </span>
                            </td>
                            <td class="col-data">
                                <div class="datetime-info">
                                    <span class="date-text"><?php echo date('d/m/Y', strtotime($mensagem['created_at'])); ?></span>
                                    <span class="time-text"><?php echo date('H:i', strtotime($mensagem['created_at'])); ?></span>
                                </div>
                            </td>
                            <td class="col-atualizacao">
                                <div class="datetime-info">
                                    <span class="date-text"><?php echo date('d/m/Y', strtotime($mensagem['updated_at'])); ?></span>
                                    <span class="time-text"><?php echo date('H:i', strtotime($mensagem['updated_at'])); ?></span>
                                </div>
                            </td>
                            <td class="col-novas">
                                <?php if($mensagem['novas_respostas'] > 0): ?>
                                    <span class="notification-badge"><?php echo $mensagem['novas_respostas']; ?></span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="col-acoes">
                                <div class="action-buttons">
                                    <button class="btn-action btn-view" onclick="window.location.href='admin_chat.php?id=<?php echo $mensagem['id']; ?>'" title="Responder mensagem">
                                        <i class="fas fa-reply"></i>
                                        <span class="btn-text">Responder</span>
                                    </button>
                                    
                                    <?php if($mensagem['status'] != 'fechada'): ?>
                                        <button class="btn-action btn-close" onclick="fecharTicket(<?php echo $mensagem['id']; ?>)" title="Fechar ticket">
                                            <i class="fas fa-lock"></i>
                                            <span class="btn-text">Fechar</span>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn-action btn-reopen" onclick="reabrirTicket(<?php echo $mensagem['id']; ?>)" title="Reabrir ticket">
                                            <i class="fas fa-unlock"></i>
                                            <span class="btn-text">Reabrir</span>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button class="btn-action btn-delete" onclick="confirmarExclusao(<?php echo $mensagem['id']; ?>)" title="Excluir mensagem">
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
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3>Nenhuma mensagem encontrada</h3>
                    <p>Não há mensagens correspondentes aos critérios de pesquisa selecionados.</p>
                    <a href="admin_contato.php" class="btn-primary">
                        <i class="fas fa-refresh"></i> Limpar Filtros
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* ===== COMPONENTES PRINCIPAIS ===== */
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
    background: #8b7355;
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
    color: #2c3e50;
    margin-bottom: 8px;
    font-family: 'Arial', sans-serif;
}

.stat-card-small .stat-label {
    color: #666;
    font-size: 0.9rem;
    font-weight: 500;
}

/* ===== FILTROS ===== */
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

.status-filter, .date-filter, .search-filter {
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    background: white;
}

.status-filter:focus, .date-filter:focus, .search-filter:focus {
    outline: none;
    border-color: #8b7355;
    box-shadow: 0 0 0 3px rgba(139, 115, 85, 0.1);
}

.filter-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

/* ===== TABELA ===== */
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

.message-row:hover {
    background: #f8f9fa;
}

/* ===== ESTILOS ESPECÍFICOS PARA MENSAGENS ===== */
.message-row[data-status="aberta"] {
    background: linear-gradient(90deg, #fff9e6 0%, white 50px);
    border-left: 4px solid #ffc107;
}

.message-row.new-message {
    background: linear-gradient(90deg, #e8f4fd 0%, white 50px);
    border-left: 4px solid #17a2b8;
}

.message-info {
    display: flex;
    align-items: center;
    gap: 8px;
}

.message-number {
    font-family: 'Courier New', monospace;
    font-weight: 600;
}

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

.user-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.customer-name {
    font-weight: 500;
    color: #2c3e50;
}

.customer-email {
    font-size: 12px;
    color: #6c757d;
}

.subject-info {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.subject-text {
    color: #2c3e50;
    font-weight: 600;
}

.message-preview {
    font-size: 12px;
    color: #6c757d;
    line-height: 1.4;
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

.notification-badge {
    background: #dc3545;
    color: white;
    padding: 6px 10px;
    border-radius: 50%;
    font-size: 11px;
    font-weight: bold;
    min-width: 24px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* ===== STATUS BADGES ===== */
.status-badge {
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-block;
    min-width: 80px;
    text-align: center;
}

.status-aberta {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-respondida {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.status-fechada {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

/* ===== BOTÕES DE AÇÃO ===== */
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

.btn-close {
    background: #f39c12;
    color: white;
}

.btn-close:hover {
    background: #e67e22;
}

.btn-reopen {
    background: #27ae60;
    color: white;
}

.btn-reopen:hover {
    background: #219a52;
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

/* ===== BOTÕES PRINCIPAIS ===== */
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

/* ===== ESTADO VAZIO ===== */
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

/* ===== ALERTAS ===== */
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

/* ===== COLUNAS ESPECÍFICAS ===== */
.col-id { width: 100px; }
.col-cliente { width: 180px; }
.col-assunto { width: 250px; }
.col-status { width: 120px; }
.col-data { width: 120px; }
.col-atualizacao { width: 120px; }
.col-novas { width: 80px; }
.col-acoes { width: 220px; }

.text-muted {
    color: #6c757d !important;
    font-style: italic;
}

/* ===== RESPONSIVIDADE ===== */
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
function fecharTicket(mensagemId) {
    Swal.fire({
        title: 'Fechar Ticket',
        html: `<strong>Deseja fechar o ticket #${mensagemId}?</strong><br><br>
              <div style="text-align: left; background: #fff3cd; padding: 12px; border-radius: 8px; border-left: 4px solid #ffc107;">
                <i class="fas fa-info-circle" style="color: #856404;"></i>
                <strong style="color: #856404;">Informação:</strong>
                <ul style="margin: 8px 0 0 0; padding-left: 20px; color: #856404;">
                    <li>O ticket será marcado como "fechado"</li>
                    <li>O cliente será notificado</li>
                    <li>Você pode reabrir o ticket posteriormente</li>
                </ul>
              </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f39c12',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, fechar ticket!',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `admin_contato.php?action=fechar&id=${mensagemId}<?php 
                echo $filtro_status ? '&status=' . $filtro_status : '';
                echo $filtro_data ? '&data=' . $filtro_data : '';
                echo $filtro_assunto ? '&assunto=' . $filtro_assunto : '';
            ?>`;
        }
    });
}

function reabrirTicket(mensagemId) {
    Swal.fire({
        title: 'Reabrir Ticket',
        html: `<strong>Deseja reabrir o ticket #${mensagemId}?</strong>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#27ae60',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, reabrir ticket!',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `admin_contato.php?action=reabrir&id=${mensagemId}<?php 
                echo $filtro_status ? '&status=' . $filtro_status : '';
                echo $filtro_data ? '&data=' . $filtro_data : '';
                echo $filtro_assunto ? '&assunto=' . $filtro_assunto : '';
            ?>`;
        }
    });
}

function confirmarExclusao(mensagemId) {
    Swal.fire({
        title: '⚠️ Confirmação de Exclusão',
        html: `<strong>Tem certeza que deseja excluir a mensagem #${mensagemId}?</strong><br><br>
              <div style="text-align: left; background: #fff3f3; padding: 12px; border-radius: 8px; border-left: 4px solid #dc3545;">
                <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>
                <strong style="color: #721c24;">Atenção:</strong>
                <ul style="margin: 8px 0 0 0; padding-left: 20px; color: #721c24;">
                    <li>Esta ação é <strong>IRREVERSÍVEL</strong></li>
                    <li>Todos os dados da mensagem serão permanentemente removidos</li>
                    <li>Respostas associadas também serão excluídas</li>
                </ul>
              </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir mensagem!',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Excluindo mensagem...',
                text: 'Aguarde enquanto removemos a mensagem do sistema',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Redirecionar para exclusão
            window.location.href = `?excluir_mensagem=${mensagemId}`;
        }
    });
    
    return false;
}

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    // Destacar mensagens com novas respostas
    document.querySelectorAll('.message-row.new-message').forEach(row => {
        row.style.animation = 'pulseBackground 3s infinite';
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
        'btn-view': 'Responder à mensagem',
        'btn-close': 'Fechar ticket',
        'btn-reopen': 'Reabrir ticket',
        'btn-delete': 'Excluir mensagem permanentemente'
    };
    
    document.querySelectorAll('.btn-action').forEach(btn => {
        const className = Array.from(btn.classList).find(cls => cls.startsWith('btn-'));
        if (className && tooltips[className]) {
            btn.setAttribute('title', tooltips[className]);
        }
    });
});

// Animação para mensagens novas
const style = document.createElement('style');
style.textContent = `
    @keyframes pulseBackground {
        0% { background: linear-gradient(90deg, #e8f4fd 0%, white 50px); }
        50% { background: linear-gradient(90deg, #d4edff 0%, white 50px); }
        100% { background: linear-gradient(90deg, #e8f4fd 0%, white 50px); }
    }
`;
document.head.appendChild(style);
</script>

<?php include 'includes/footer.php'; ?>