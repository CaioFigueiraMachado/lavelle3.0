<?php
session_start();
include 'conexao.php';

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['id'];
$mensagem_id = $_GET['id'] ?? 0;

// Buscar dados da mensagem
$sql_mensagem = "SELECT cm.*, u.nome 
                 FROM contato_mensagens cm 
                 LEFT JOIN usuarios u ON cm.usuario_id = u.id 
                 WHERE cm.id = ? AND cm.usuario_id = ?";
$stmt_mensagem = $con->prepare($sql_mensagem);
$stmt_mensagem->execute([$mensagem_id, $usuario_id]);
$mensagem = $stmt_mensagem->fetch(PDO::FETCH_ASSOC);

if (!$mensagem) {
    header('Location: perfil.php#tab-contato');
    exit();
}

// Buscar histórico de mensagens
$sql_historico = "SELECT * FROM contato_respostas 
                  WHERE mensagem_id = ? 
                  ORDER BY created_at ASC";
$stmt_historico = $con->prepare($sql_historico);
$stmt_historico->execute([$mensagem_id]);
$historico = $stmt_historico->fetchAll(PDO::FETCH_ASSOC);

// Marcar mensagens do admin como lidas
$sql_marcar_lidas = "UPDATE contato_respostas SET lida = 1 
                     WHERE mensagem_id = ? AND remetente = 'admin' AND lida = 0";
$stmt_marcar_lidas = $con->prepare($sql_marcar_lidas);
$stmt_marcar_lidas->execute([$mensagem_id]);

// Processar nova resposta
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nova_mensagem'])) {
    $nova_mensagem = trim($_POST['nova_mensagem']);
    
    if (!empty($nova_mensagem)) {
        $sql_responder = "INSERT INTO contato_respostas (mensagem_id, remetente, mensagem) VALUES (?, 'cliente', ?)";
        $stmt_responder = $con->prepare($sql_responder);
        $stmt_responder->execute([$mensagem_id, $nova_mensagem]);
        
        // Atualizar status
        $sql_status = "UPDATE contato_mensagens SET status = 'aberta', updated_at = NOW() WHERE id = ?";
        $stmt_status = $con->prepare($sql_status);
        $stmt_status->execute([$mensagem_id]);
        
        header("Location: chat_contato.php?id=$mensagem_id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat - Mensagem #<?php echo $mensagem_id; ?> - LAVELLE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--cream);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Banner - Estilo LAVELLE */
        .header-banner {
            background-color: #000;
            color: #ffffff;
            text-align: center;
            padding: 8px 0;
            font-size: 14px;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
        }
        
        .header-banner h1 {
            font-size: 14px;
            font-weight: 300;
            margin: 0;
            padding: 0;
            letter-spacing: 3px;
            color: #f5f5f5;
        }
        
        /* Header */
        header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            letter-spacing: 2px;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            align-items: center;
        }
        
        nav ul li {
            margin-left: 20px;
            position: relative;
        }
        
        nav ul li a {
            text-decoration: none;
            color: #000;
            font-weight: 500;
            transition: color 0.3s;
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 5px;
        }
        
        nav ul li a:hover {
            color: var(--primary);
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 20px;
            padding-left: 20px;
            border-left: 1px solid #eee;
        }
        
        .user-menu a {
            font-size: 13px;
            padding: 6px 12px;
        }
        
        .user-menu a.profile-link {
            background-color: #f5f5f5;
            color: var(--primary);
        }
        
        .user-menu a.profile-link:hover {
            background-color: var(--primary);
            color: white;
        }
        
        /* Main Content */
        .main-container {
            padding: 40px 0;
            min-height: calc(100vh - 200px);
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="rgba(255,255,255,0.1)"><path d="M30,30 Q50,10 70,30 T90,50 T70,70 T50,90 T30,70 T10,50 T30,30 Z"/></svg>');
            background-size: contain;
            opacity: 0.3;
        }
        
        .page-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
            font-weight: bold;
        }
        
        .page-header p {
            font-size: 18px;
            opacity: 0.9;
        }
        
        .header-actions {
            margin-top: 20px;
        }
        
        .btn-outline {
            background: transparent;
            color: var(--white);
            border: 2px solid var(--white);
            padding: 12px 24px;
            border-radius: 30px;
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
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .content-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--border);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--cream);
        }
        
        .card-header h2 {
            color: #2c3e50;
            font-size: 22px;
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
        
        /* Info Grid */
        .info-grid {
            display: flex;
            flex-direction: column;
            gap: 0;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 15px 0;
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
            min-width: 160px;
        }
        
        .info-value {
            color: #2c3e50;
            text-align: right;
            flex: 1;
        }
        
        /* Chat Container - Estilo Profissional */
        .chat-container {
            border: 1px solid var(--border);
            border-radius: 15px;
            background: white;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .chat-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 25px 30px;
            border-bottom: 1px solid var(--primary-dark);
            color: white;
        }
        
        .chat-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .chat-title-section h2 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .chat-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .chat-meta {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .messages-count {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 11px;
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
        
        /* Chat Body */
        .chat-body {
            padding: 0;
            max-height: 500px;
            overflow-y: auto;
            background: var(--cream);
        }
        
        .conversation-timeline {
            padding: 25px;
        }
        
        /* Mensagem Original - Estilo Destaque */
        .original-message-section {
            margin-bottom: 30px;
        }
        
        .original-message-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
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
            top: 15px;
            right: 15px;
            background: var(--primary);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .message-sender-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .sender-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        }
        
        .sender-details h4 {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 2px;
        }
        
        .sender-role {
            font-size: 11px;
            color: var(--primary);
            font-weight: 500;
        }
        
        .message-time {
            font-size: 12px;
            color: #6c757d;
        }
        
        .message-content {
            background: var(--cream);
            padding: 20px;
            border-radius: 10px;
            margin-top: 15px;
        }
        
        .message-subject {
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }
        
        .message-text {
            line-height: 1.6;
            color: #2c3e50;
        }
        
        /* Mensagens do Chat */
        .message-group {
            margin-bottom: 25px;
        }
        
        .message {
            display: flex;
            margin-bottom: 20px;
            animation: fadeInUp 0.4s ease;
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
            padding: 15px 20px;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .message.admin .message-bubble {
            border-top-left-radius: 5px;
            background: linear-gradient(135deg, #f8fdff 0%, #e8f4ff 100%);
            border: 1px solid #e1f0ff;
        }
        
        .message.cliente .message-bubble {
            border-top-right-radius: 5px;
            background: linear-gradient(135deg, var(--cream) 0%, white 100%);
            border: 1px solid var(--border);
        }
        
        .message-bubble-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .bubble-sender {
            font-weight: 600;
            font-size: 13px;
            color: #2c3e50;
        }
        
        .bubble-time {
            font-size: 11px;
            color: #6c757d;
        }
        
        .bubble-text {
            line-height: 1.5;
            color: #2c3e50;
            font-size: 14px;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Empty State */
        .empty-chat {
            padding: 60px 40px;
            text-align: center;
            color: #6c757d;
            background: white;
            border-radius: 10px;
            margin: 20px;
            border: 2px dashed var(--border);
        }
        
        .empty-icon {
            font-size: 64px;
            color: #ced4da;
            margin-bottom: 20px;
        }
        
        .empty-chat h3 {
            margin-bottom: 12px;
            color: #495057;
            font-size: 20px;
            font-weight: 600;
        }
        
        .empty-chat p {
            font-size: 15px;
            margin: 0;
        }
        
        /* Chat Form */
        .chat-form {
            padding: 25px 30px;
            border-top: 1px solid var(--border);
            background: white;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 15px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            resize: vertical;
            transition: all 0.3s ease;
            font-family: inherit;
            line-height: 1.5;
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
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .form-actions {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }
        
        .btn {
            display: inline-block;
            background-color: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            text-align: center;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .btn-outline-secondary {
            background: transparent;
            color: #6c757d;
            border: 2px solid #6c757d;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }
        
        /* Chat Closed */
        .chat-closed {
            padding: 40px 30px;
            text-align: center;
            background: var(--cream);
            border-top: 1px solid var(--border);
        }
        
        .closed-icon {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        .closed-text h4 {
            margin-bottom: 8px;
            color: #495057;
            font-size: 18px;
        }
        
        .closed-text p {
            color: #6c757d;
            margin-bottom: 20px;
        }
        
        /* Scrollbar */
        .chat-body::-webkit-scrollbar {
            width: 6px;
        }
        
        .chat-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .chat-body::-webkit-scrollbar-thumb {
            background: var(--primary-light);
            border-radius: 3px;
        }
        
        .chat-body::-webkit-scrollbar-thumb:hover {
            background: var(--primary);
        }
        
        /* Footer */
        footer {
            background-color: #000;
            color: white;
            padding: 60px 0 30px;
            margin-top: 0;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-column h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #fff;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 10px;
        }
        
        .footer-column ul li a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-column ul li a:hover {
            color: #fff;
        }
        
        .contact-info {
            color: #ccc;
        }
        
        .contact-info p {
            margin-bottom: 10px;
        }
        
        .social-links a {
            color: #ccc;
            margin-right: 15px;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .social-links a:hover {
            color: #fff;
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #444;
            color: #999;
            font-size: 14px;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .info-item {
                flex-direction: column;
                gap: 5px;
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
            
            .chat-body {
                max-height: 400px;
            }
            
            .conversation-timeline {
                padding: 20px;
            }
            
            .chat-header-content {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
        }
        
        @media (max-width: 480px) {
            .page-header {
                padding: 30px 20px;
            }
            
            .page-header h1 {
                font-size: 24px;
            }
            
            .message-bubble {
                max-width: 90%;
                padding: 12px 15px;
            }
            
            .content-card {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Banner com a frase estilizada -->
    <div class="header-banner">
        <h1>O perfume certo transforma a presença em memória.</h1>
    </div>
    
    <header>
       <?php include 'header4.php'; ?>
    </header>

   <br><br> <div class="container main-container">
        <div class="page-header">
            <h1>Central de Atendimento</h1>
            <p>Ticket #<?php echo str_pad($mensagem_id, 4, '0', STR_PAD_LEFT); ?> - <?php echo htmlspecialchars($mensagem['assunto']); ?></p>
            <div class="header-actions">
                <a href="perfil.php#tab-contato" class="btn-outline">
                    <i class="fas fa-arrow-left"></i> Voltar às Mensagens
                </a>
            </div>
        </div>

        <div class="content-grid">
            <!-- Informações do Ticket -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Detalhes do Ticket</h2>
                    <span class="ticket-id">#<?php echo str_pad($mensagem_id, 4, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-tag"></i>
                                Assunto:
                            </div>
                            <div class="info-value"><?php echo htmlspecialchars($mensagem['assunto']); ?></div>
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
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-info-circle"></i>
                                Status:
                            </div>
                            <div class="info-value">
                                <span class="status-badge status-<?php echo $mensagem['status']; ?>">
                                    <?php echo ucfirst($mensagem['status']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Ações</h2>
                </div>
                <div class="card-body">
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <a href="perfil.php#tab-contato" class="btn" style="text-decoration: none; text-align: center;">
                            <i class="fas fa-list"></i> Ver Todos os Tickets
                        </a>
                        <a href="contato.php" class="btn" style="text-decoration: none; text-align: center; background: #17a2b8;">
                            <i class="fas fa-plus"></i> Novo Ticket
                        </a>
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

                    <div class="chat-body" id="chatBody">
                        <div class="conversation-timeline">
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
                                                <h4><?php echo htmlspecialchars($mensagem['nome'] ?: 'Você'); ?></h4>
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
                                <?php foreach($historico as $msg): ?>
                                <div class="message-group">
                                    <div class="message <?php echo $msg['remetente']; ?>">
                                        <div class="message-bubble">
                                            <div class="message-bubble-header">
                                                <div class="bubble-sender">
                                                    <?php if($msg['remetente'] == 'admin'): ?>
                                                        Suporte LAVELLE
                                                    <?php else: ?>
                                                        <?php echo htmlspecialchars($mensagem['nome'] ?: 'Você'); ?>
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
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-chat">
                                    <div class="empty-icon">
                                        <i class="fas fa-comments"></i>
                                    </div>
                                    <h3>Nenhuma resposta ainda</h3>
                                    <p>O suporte ainda não respondeu sua mensagem.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Formulário de Resposta -->
                    <?php if($mensagem['status'] != 'fechada'): ?>
                    <div class="chat-form">
                        <form method="POST" id="respostaForm">
                            <div class="form-group">
                                <label for="nova_mensagem">
                                    <i class="fas fa-edit"></i>
                                    Sua Resposta:
                                </label>
                                <textarea name="nova_mensagem" id="nova_mensagem" class="form-control" rows="4" 
                                          placeholder="Digite sua resposta aqui..." required></textarea>
                                <div class="char-count">
                                    <span id="charCount">0</span> caracteres
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn-outline-secondary" onclick="limparResposta()">
                                    <i class="fas fa-eraser"></i> Limpar
                                </button>
                                <button type="submit" class="btn">
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
                        <div class="closed-actions">
                            <a href="contato.php" class="btn">
                                <i class="fas fa-plus"></i> Abrir Novo Ticket
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <br><br></div>
    
   <br><br><br> <footer>
        <?php include 'footer.php'; ?>
    </footer>

    <script>
        // Rolagem automática para a última mensagem
        document.addEventListener('DOMContentLoaded', function() {
            const chatBody = document.getElementById('chatBody');
            if (chatBody) {
                chatBody.scrollTop = chatBody.scrollHeight;
            }
            
            // Contador de caracteres
            const textarea = document.getElementById('nova_mensagem');
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
                        charCount.style.color = '#dc3545';
                        charCount.style.fontWeight = 'bold';
                    } else if (this.value.length > 300) {
                        charCount.style.color = '#ffc107';
                        charCount.style.fontWeight = 'bold';
                    } else {
                        charCount.style.color = '#6c757d';
                        charCount.style.fontWeight = 'normal';
                    }
                });
                
                // Inicializar contador
                charCount.textContent = textarea.value.length;
                
                // Focus no textarea
                textarea.focus();
            }
        });
        
        function limparResposta() {
            const textarea = document.getElementById('nova_mensagem');
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
        
        // Observar mudanças no chat para scroll automático
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    const chatBody = document.getElementById('chatBody');
                    if (chatBody) {
                        setTimeout(() => {
                            chatBody.scrollTop = chatBody.scrollHeight;
                        }, 100);
                    }
                }
            });
        });
        
        const chatBody = document.getElementById('chatBody');
        if (chatBody) {
            observer.observe(chatBody, {
                childList: true,
                subtree: true
            });
        }
    </script>
</body>
</html>