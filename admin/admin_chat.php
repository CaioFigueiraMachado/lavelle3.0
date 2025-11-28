<?php
// admin_chat.php
session_start();
if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

include 'config/database.php';
$database = new Database();
$db = $database->getConnection();

$mensagem_id = $_GET['id'] ?? 0;

// Buscar dados da mensagem
$query_mensagem = "SELECT cm.*, u.nome, u.email, u.telefone 
                   FROM contato_mensagens cm 
                   LEFT JOIN usuarios u ON cm.usuario_id = u.id 
                   WHERE cm.id = ?";
$stmt_mensagem = $db->prepare($query_mensagem);
$stmt_mensagem->execute([$mensagem_id]);
$mensagem = $stmt_mensagem->fetch(PDO::FETCH_ASSOC);

if (!$mensagem) {
    $_SESSION['mensagem_erro'] = "Ticket não encontrado!";
    header('Location: admin_contato.php');
    exit;
}

// Buscar histórico
$query_historico = "SELECT * FROM contato_respostas 
                    WHERE mensagem_id = ? 
                    ORDER BY created_at ASC";
$stmt_historico = $db->prepare($query_historico);
$stmt_historico->execute([$mensagem_id]);
$historico = $stmt_historico->fetchAll(PDO::FETCH_ASSOC);

// Marcar como lida
$query_marcar_lida = "UPDATE contato_respostas SET lida = 1 
                      WHERE mensagem_id = ? AND remetente = 'cliente' AND lida = 0";
$stmt_marcar_lida = $db->prepare($query_marcar_lida);
$stmt_marcar_lida->execute([$mensagem_id]);

// Processar resposta do admin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['responder'])) {
        $resposta = trim($_POST['resposta']);
        if (!empty($resposta)) {
            try {
                $query_responder = "INSERT INTO contato_respostas (mensagem_id, remetente, mensagem) VALUES (?, 'admin', ?)";
                $stmt_responder = $db->prepare($query_responder);
                $stmt_responder->execute([$mensagem_id, $resposta]);
                
                // Atualizar status
                $query_status = "UPDATE contato_mensagens SET status = 'respondida', updated_at = NOW() WHERE id = ?";
                $stmt_status = $db->prepare($query_status);
                $stmt_status->execute([$mensagem_id]);
                
                $_SESSION['mensagem_sucesso'] = "Resposta enviada com sucesso!";
            } catch (PDOException $e) {
                $_SESSION['mensagem_erro'] = "Erro ao enviar resposta: " . $e->getMessage();
            }
        }
    } elseif (isset($_POST['fechar'])) {
        try {
            $query_fechar = "UPDATE contato_mensagens SET status = 'fechada', updated_at = NOW() WHERE id = ?";
            $stmt_fechar = $db->prepare($query_fechar);
            $stmt_fechar->execute([$mensagem_id]);
            
            $_SESSION['mensagem_sucesso'] = "Ticket #" . $mensagem_id . " fechado com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['mensagem_erro'] = "Erro ao fechar ticket: " . $e->getMessage();
        }
    } elseif (isset($_POST['reabrir'])) {
        try {
            $query_reabrir = "UPDATE contato_mensagens SET status = 'aberta', updated_at = NOW() WHERE id = ?";
            $stmt_reabrir = $db->prepare($query_reabrir);
            $stmt_reabrir->execute([$mensagem_id]);
            
            $_SESSION['mensagem_sucesso'] = "Ticket #" . $mensagem_id . " reaberto com sucesso!";
        } catch (PDOException $e) {
            $_SESSION['mensagem_erro'] = "Erro ao reabrir ticket: " . $e->getMessage();
        }
    }
    
    header("Location: admin_chat.php?id=$mensagem_id");
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<style>
:root {
    --primary: #8B7355;
    --primary-dark: #756049;
    --primary-light: #D4B896;
    --gold: #C9A96E;
    --cream: #F9F5F0;
    --white: #FFFFFF;
    --text: #333333;
    --text-light: #666666;
    --border: #E8E2D6;
    --shadow: rgba(139, 115, 85, 0.15);
}

/* ===== LAYOUT PRINCIPAL ===== */
.main-content {
    background: var(--cream);
    min-height: 100vh;
    padding: 30px;
}

.page-header {
   
    color: var(--white);
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 25px;
    text-align: center;
    position: relative;
    overflow: hidden;
}



.page-header h1 {
    font-size: 28px;
    margin-bottom: 8px;
    font-weight: bold;
}

.page-header p {
    font-size: 16px;
    opacity: 0.9;
    margin-bottom: 20px;
}

.header-actions {
    margin-top: 15px;
}

.btn-outline {
    background: var(--primary);
    color: var(--white);
    border: 2px solid var(--white);
    padding: 10px 20px;
    border-radius: 25px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-outline:hover {
    background: var(--white);
    color: var(--primary);
    transform: translateY(-2px);
}

/* ===== STATS GRID ===== */
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
    background: var(--primary);
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

/* ===== CONTENT GRID ===== */
.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
    margin-bottom: 25px;
}

.content-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid var(--cream);
}

.card-header h2 {
    color: #2c3e50;
    font-size: 20px;
    font-weight: 700;
    margin: 0;
}

.ticket-id {
    background: var(--primary);
    color: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

/* ===== INFORMACOES DO CLIENTE ===== */
.info-grid {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 12px 0;
    border-bottom: 1px solid var(--cream);
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #495057;
    font-size: 14px;
    min-width: 140px;
}

.info-value {
    color: #2c3e50;
    text-align: right;
    flex: 1;
}

.subject-value {
    font-weight: 500;
    color: var(--primary);
}

.email-link {
    color: #3498db;
    text-decoration: none;
    transition: color 0.3s;
}

.email-link:hover {
    color: var(--primary);
    text-decoration: underline;
}

/* ===== AÇÕES DO TICKET ===== */
.action-buttons-vertical {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.action-form {
    margin: 0;
}

.btn-action {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    font-weight: 500;
    width: 100%;
    text-align: left;
    font-size: 14px;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-close {
    background: #be8600;
    color: white;
}

.btn-close:hover {
    background: #400802ff;
}

.btn-reopen {
    background: #27ae60;
    color: white;
}

.btn-reopen:hover {
    background: #219a52;
}

.btn-email {
    background: #3498db;
    color: white;
}

.btn-email:hover {
    background: #2980b9;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

.status-info {
    margin-top: 15px;
    padding: 15px;
    background: var(--cream);
    border-radius: 8px;
    border-left: 4px solid var(--primary);
}

.current-status {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* ===== SISTEMA DE CHAT PROFISSIONAL ===== */
.chat-container {
    border: 1px solid var(--border);
    border-radius: 15px;
    background: white;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.chat-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    padding: 20px 25px;
    border-bottom: 1px solid var(--primary-dark);
    color: white;
}

.chat-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-title-section h2 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 4px;
}

.chat-subtitle {
    font-size: 13px;
    opacity: 0.9;
}

.chat-meta {
    display: flex;
    align-items: center;
    gap: 12px;
}

.messages-count {
    background: rgba(255,255,255,0.2);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 11px;
    font-weight: 500;
}

/* MENSAGEM ORIGINAL - ESTILO DESTAQUE */
.original-message-section {
    padding: 25px;
    background: var(--cream);
    border-bottom: 1px solid var(--border);
}

.original-message-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-left: 4px solid var(--primary);
    position: relative;
    overflow: hidden;
}

.original-message-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, var(--primary), var(--primary-light));
}

.original-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: var(--primary);
    color: white;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.message-sender-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.sender-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
}

.sender-details h4 {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 2px;
    font-size: 14px;
}

.sender-role {
    font-size: 10px;
    color: var(--primary);
    font-weight: 500;
}

.message-time {
    font-size: 11px;
    color: #6c757d;
}

.message-content {
    background: var(--cream);
    padding: 15px;
    border-radius: 8px;
    margin-top: 12px;
}

.message-subject {
    font-weight: 600;
    color: var(--primary-dark);
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid var(--border);
    font-size: 14px;
}

.message-text {
    line-height: 1.5;
    color: #2c3e50;
    font-size: 13px;
}

/* HISTÓRICO DE MENSAGENS */
.chat-messages {
    padding: 20px;
    max-height: 400px;
    overflow-y: auto;
    background: var(--cream);
}

.message-group {
    margin-bottom: 20px;
}

.message {
    display: flex;
    margin-bottom: 16px;
    animation: fadeInUp 0.3s ease;
}

.message.admin {
    justify-content: flex-start;
}

.message.cliente {
    justify-content: flex-end;
}

.message-bubble {
    max-width: 70%;
    background: white;
    padding: 12px 16px;
    border-radius: 15px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    position: relative;
}

.message.admin .message-bubble {
    border-top-left-radius: 4px;
    background: linear-gradient(135deg, #f8fdff 0%, #e8f4ff 100%);
    border: 1px solid #e1f0ff;
}

.message.cliente .message-bubble {
    border-top-right-radius: 4px;
    background: linear-gradient(135deg, var(--cream) 0%, white 100%);
    border: 1px solid var(--border);
}

.message-bubble-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
}

.bubble-sender {
    font-weight: 600;
    font-size: 12px;
    color: #2c3e50;
}

.bubble-time {
    font-size: 10px;
    color: #6c757d;
}

.bubble-text {
    line-height: 1.4;
    color: #2c3e50;
    font-size: 13px;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* FORMULÁRIO DE RESPOSTA */
.chat-form {
    padding: 20px 25px;
    border-top: 1px solid var(--border);
    background: white;
}

.form-group {
    margin-bottom: 16px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
    font-size: 13px;
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 2px solid var(--border);
    border-radius: 8px;
    font-size: 13px;
    resize: vertical;
    transition: all 0.3s ease;
    font-family: inherit;
    line-height: 1.4;
    background: var(--cream);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(139, 115, 85, 0.1);
    background: white;
}

.char-count {
    text-align: right;
    font-size: 11px;
    color: #6c757d;
    margin-top: 4px;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn {
    display: inline-block;
    background-color: var(--primary);
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    text-align: center;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn:hover {
    background-color: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
}

.btn-outline-secondary {
    background: transparent;
    color: #6c757d;
    border: 2px solid #6c757d;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-outline-secondary:hover {
    background: #6c757d;
    color: white;
}

/* ESTADOS VAZIOS E FECHADOS */
.empty-chat {
    padding: 50px 30px;
    text-align: center;
    color: #6c757d;
    background: white;
    border-radius: 8px;
    margin: 15px;
    border: 2px dashed var(--border);
}

.empty-icon {
    font-size: 48px;
    color: #ced4da;
    margin-bottom: 15px;
}

.empty-chat h3 {
    margin-bottom: 8px;
    color: #495057;
    font-size: 18px;
    font-weight: 600;
}

.empty-chat p {
    font-size: 14px;
    margin: 0;
}

.chat-closed {
    padding: 30px 25px;
    text-align: center;
    background: var(--cream);
    border-top: 1px solid var(--border);
}

.closed-icon {
    font-size: 40px;
    color: #6c757d;
    margin-bottom: 15px;
}

.closed-text h4 {
    margin-bottom: 6px;
    color: #495057;
    font-size: 16px;
}

.closed-text p {
    color: #6c757d;
    margin-bottom: 15px;
    font-size: 14px;
}

/* STATUS BADGES */
.status-badge {
    padding: 5px 10px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
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

/* SCROLLBAR PERSONALIZADA */
.chat-messages::-webkit-scrollbar {
    width: 5px;
}

.chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: var(--primary-light);
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: var(--primary);
}

/* ALERTAS */
.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.alert.success {
    background: #d4edda;
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert.error {
    background: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

/* RESPONSIVIDADE */
@media (max-width: 768px) {
    .main-content {
        padding: 20px;
    }
    
    .content-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .stats-grid-small {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .info-item {
        flex-direction: column;
        gap: 4px;
        text-align: left;
    }
    
    .info-value {
        text-align: left;
    }
    
    .message-bubble {
        max-width: 85%;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .chat-messages {
        max-height: 350px;
        padding: 15px;
    }
    
    .chat-header-content {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .stats-grid-small {
        grid-template-columns: 1fr;
    }
    
    .page-header {
        padding: 25px 20px;
    }
    
    .page-header h1 {
        font-size: 24px;
    }
    
    .message-bubble {
        max-width: 90%;
        padding: 10px 12px;
    }
    
    .content-card {
        padding: 20px;
    }
}
</style>

<div class="main-content">
    <div class="page-header">
        <h1>Central de Atendimento - Admin</h1>
        <p>Ticket #<?php echo str_pad($mensagem_id, 4, '0', STR_PAD_LEFT); ?> - <?php echo htmlspecialchars($mensagem['assunto']); ?></p>
        <div class="header-actions">
            <a href="admin_contato.php" class="btn-outline">
                <i class="fas fa-arrow-left"></i> Voltar para Lista
            </a>
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
           
            <div class="stat-number"><?php echo count($historico) + 1; ?></div>
            <div class="stat-label">Total Mensagens</div>
        </div>
        <div class="stat-card-small">
          
            <div class="stat-number"><?php echo date('d/m', strtotime($mensagem['created_at'])); ?></div>
            <div class="stat-label">Data Abertura</div>
        </div>
        <div class="stat-card-small">
           
            <div class="stat-number"><?php echo date('d/m', strtotime($mensagem['updated_at'])); ?></div>
            <div class="stat-label">Última Atualização</div>
        </div>
        <div class="stat-card-small">
          
            <div class="stat-number"><?php echo ucfirst($mensagem['status']); ?></div>
            <div class="stat-label">Status Ticket</div>
        </div>
    </div>
      
    <div class="content-grid">
        <!-- Informações do Cliente -->
        <div class="content-card">
            <div class="card-header">
                <h2>Informações do Cliente</h2>
                <span class="ticket-id">Ticket #<?php echo str_pad($mensagem_id, 4, '0', STR_PAD_LEFT); ?></span>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user"></i>
                            Nome:
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($mensagem['nome'] ?: 'Visitante'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-envelope"></i>
                            E-mail:
                        </div>
                        <div class="info-value">
                            <?php if($mensagem['email']): ?>
                                <a href="mailto:<?php echo htmlspecialchars($mensagem['email']); ?>" class="email-link">
                                    <?php echo htmlspecialchars($mensagem['email']); ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Não informado</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-phone"></i>
                            Telefone:
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($mensagem['telefone'] ?: 'Não informado'); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-tag"></i>
                            Assunto:
                        </div>
                        <div class="info-value subject-value"><?php echo htmlspecialchars($mensagem['assunto']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-calendar-plus"></i>
                            Data de Abertura:
                        </div>
                        <div class="info-value">
                            <?php echo date('d/m/Y H:i', strtotime($mensagem['created_at'])); ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-calendar-check"></i>
                            Última Atualização:
                        </div>
                        <div class="info-value">
                            <?php echo date('d/m/Y H:i', strtotime($mensagem['updated_at'])); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="content-card">
            <div class="card-header">
                <h2>Ações do Ticket</h2>
            </div>
            <div class="card-body">
                <div class="action-buttons-vertical">
                    <?php if($mensagem['status'] != 'fechada'): ?>
                        <form method="POST" class="action-form">
                            <button type="submit" name="fechar" class="btn-action btn-close" onclick="return confirmarFechamento()">
                                <i class="fas fa-lock"></i>
                                <span>Fechar Ticket</span>
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" class="action-form">
                            <button type="submit" name="reabrir" class="btn-action btn-reopen">
                                <i class="fas fa-unlock"></i>
                                <span>Reabrir Ticket</span>
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <a href="admin_contato.php?excluir_mensagem=<?php echo $mensagem_id; ?>" 
                       class="btn-action btn-delete" 
                       onclick="return confirmarExclusao(<?php echo $mensagem_id; ?>)">
                        <i class="fas fa-trash"></i>
                        <span>Excluir Ticket</span>
                    </a>
                    
                    <div class="status-info">
                        <div class="current-status">
                            <strong>Status Atual:</strong>
                            <span class="status-badge status-<?php echo $mensagem['status']; ?>">
                                <?php echo ucfirst($mensagem['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Histórico da Conversa -->
    <div class="content-card">
        <div class="card-header">
            <h2>Histórico da Conversa</h2>
            <div class="chat-info">
                <span class="messages-count"><?php echo count($historico) + 1; ?> mensagem(ns)</span>
            </div>
        </div>
        <div class="card-body">
            <div class="chat-container">
                <div class="chat-header">
                    <div class="chat-header-content">
                        <div class="chat-title-section">
                            <h2><?php echo htmlspecialchars($mensagem['assunto']); ?></h2>
                            <div class="chat-subtitle">Ticket #<?php echo str_pad($mensagem_id, 4, '0', STR_PAD_LEFT); ?></div>
                        </div>
                        <div class="chat-meta">
                            <span class="messages-count">
                                <i class="fas fa-comments"></i>
                                <?php echo count($historico) + 1; ?> mensagens
                            </span>
                            <span class="status-badge status-<?php echo $mensagem['status']; ?>">
                                <?php echo ucfirst($mensagem['status']); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Mensagem Original do Cliente -->
                <div class="original-message-section">
                    <div class="original-message-card">
                        <span class="original-badge">Mensagem Original</span>
                        <div class="message-header">
                            <div class="message-sender-info">
                                <div class="sender-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="sender-details">
                                    <h4><?php echo htmlspecialchars($mensagem['nome'] ?: 'Cliente'); ?></h4>
                                    <span class="sender-role">Cliente</span>
                                </div>
                            </div>
                            <div class="message-time">
                                <?php echo date('d/m/Y H:i', strtotime($mensagem['created_at'])); ?>
                            </div>
                        </div>
                        <div class="message-content">
                            <div class="message-subject">
                                <strong>Assunto:</strong> <?php echo htmlspecialchars($mensagem['assunto']); ?>
                            </div>
                            <div class="message-text">
                                <?php echo nl2br(htmlspecialchars($mensagem['mensagem'])); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Histórico de Respostas -->
                <?php if(!empty($historico)): ?>
                    <div class="chat-messages" id="chatMessages">
                        <?php foreach($historico as $msg): ?>
                            <div class="message <?php echo $msg['remetente']; ?>">
                                <div class="message-bubble">
                                    <div class="message-bubble-header">
                                        <div class="bubble-sender">
                                            <?php if($msg['remetente'] == 'admin'): ?>
                                                Administrador Lavelle
                                            <?php else: ?>
                                                <?php echo htmlspecialchars($mensagem['nome'] ?: 'Cliente'); ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="bubble-time">
                                            <?php echo date('d/m/Y H:i', strtotime($msg['created_at'])); ?>
                                        </div>
                                    </div>
                                    <div class="bubble-text">
                                        <?php echo nl2br(htmlspecialchars($msg['mensagem'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-chat">
                        <div class="empty-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3>Nenhuma resposta ainda</h3>
                        <p>Seja o primeiro a enviar uma resposta nesta conversa.</p>
                    </div>
                <?php endif; ?>
                
                <!-- Formulário de Resposta -->
                <?php if($mensagem['status'] != 'fechada'): ?>
                <div class="chat-form">
                    <form method="POST" id="respostaForm">
                        <div class="form-group">
                            <label for="resposta">
                                <i class="fas fa-edit"></i>
                                Sua Resposta:
                            </label>
                            <textarea name="resposta" id="resposta" class="form-control" rows="4" 
                                      placeholder="Digite sua resposta aqui..." required></textarea>
                            <div class="char-count">
                                <span id="charCount">0</span> caracteres
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-outline-secondary" onclick="limparResposta()">
                                <i class="fas fa-eraser"></i> Limpar
                            </button>
                            <button type="submit" name="responder" class="btn">
                                <i class="fas fa-paper-plane"></i> Enviar Resposta
                            </button>
                        </div>
                    </form>
                </div>
                <?php else: ?>
                <div class="chat-closed">
                    <div class="closed-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="closed-text">
                        <h4>Ticket Fechado</h4>
                        <p>Este ticket está fechado. Não é possível enviar novas respostas.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Rolagem automática para a última mensagem
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Contador de caracteres
    const textarea = document.getElementById('resposta');
    const charCount = document.getElementById('charCount');
    
    if (textarea && charCount) {
        textarea.addEventListener('input', function() {
            // Auto-expand
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            
            // Contador
            charCount.textContent = this.value.length;
            
            // Feedback visual para textos longos
            if (this.value.length > 500) {
                charCount.style.color = '#e74c3c';
                charCount.style.fontWeight = 'bold';
            } else if (this.value.length > 300) {
                charCount.style.color = '#f39c12';
                charCount.style.fontWeight = 'bold';
            } else {
                charCount.style.color = '#6c757d';
                charCount.style.fontWeight = 'normal';
            }
        });
        
        // Inicializar contador
        charCount.textContent = textarea.value.length;
    }
});

function limparResposta() {
    const textarea = document.getElementById('resposta');
    const charCount = document.getElementById('charCount');
    
    if (textarea) {
        textarea.value = '';
        textarea.style.height = 'auto';
        charCount.textContent = '0';
        charCount.style.color = '#6c757d';
        charCount.style.fontWeight = 'normal';
        
        // Feedback visual
        textarea.focus();
    }
}

function confirmarFechamento() {
    return confirm('Tem certeza que deseja fechar este ticket? O cliente será notificado e não será possível enviar novas respostas.');
}

function confirmarExclusao(mensagemId) {
    Swal.fire({
        title: '⚠️ Confirmação de Exclusão',
        html: `<strong>Tem certeza que deseja excluir o ticket #${mensagemId}?</strong><br><br>
              <div style="text-align: left; background: #fff3f3; padding: 12px; border-radius: 8px; border-left: 4px solid #dc3545;">
                <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>
                <strong style="color: #721c24;">Atenção:</strong>
                <ul style="margin: 8px 0 0 0; padding-left: 20px; color: #721c24;">
                    <li>Esta ação é <strong>IRREVERSÍVEL</strong></li>
                    <li>Todos os dados do ticket serão permanentemente removidos</li>
                    <li>Histórico de mensagens será excluído</li>
                </ul>
              </div>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir ticket!',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (!result.isConfirmed) {
            return false;
        }
    });
    
    return false;
}
</script>

<?php include 'includes/footer.php'; ?>