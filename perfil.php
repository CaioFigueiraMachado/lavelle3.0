<?php
session_start();
include 'conexao.php';

// CORREÇÃO: Verificar a sessão correta
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$usuario_id = $_SESSION['id'];
$mensagem = "";
$tipoMensagem = "";

// Buscar dados do usuário
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar pedidos do usuário - VERSÃO CORRIGIDA
try {
    $sql_pedidos = "SELECT * FROM pedidos WHERE usuario_id = ? ORDER BY data_pedido DESC LIMIT 5";
    $stmt_pedidos = $con->prepare($sql_pedidos);
    $stmt_pedidos->execute([$usuario_id]);
    $pedidos = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);
    
    // Usar os pedidos normais (sem JOIN com enderecos_entrega)
    $pedidos_completos = $pedidos;
    
} catch (PDOException $e) {
    $pedidos = [];
    $pedidos_completos = [];
    error_log("Erro ao buscar pedidos: " . $e->getMessage());
}

// Buscar favoritos do usuário (se a tabela existir)
try {
    $sql_favoritos = "SELECT p.* FROM produtos p 
                     INNER JOIN favoritos f ON p.id = f.produto_id 
                     WHERE f.usuario_id = ? LIMIT 6";
    $stmt_favoritos = $con->prepare($sql_favoritos);
    $stmt_favoritos->execute([$usuario_id]);
    $favoritos = $stmt_favoritos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $favoritos = [];
}

// Verificar se a coluna foto_perfil existe
$coluna_existe = false;
try {
    $test_sql = "SELECT foto_perfil FROM usuarios WHERE id = ?";
    $test_stmt = $con->prepare($test_sql);
    $test_stmt->execute([$usuario_id]);
    $coluna_existe = true;
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'foto_perfil') !== false) {
        try {
            $alter_sql = "ALTER TABLE usuarios ADD COLUMN foto_perfil VARCHAR(255) DEFAULT NULL";
            $con->exec($alter_sql);
            $coluna_existe = true;
        } catch (PDOException $alter_e) {
            $mensagem = "Erro ao configurar banco de dados para fotos: " . $alter_e->getMessage();
            $tipoMensagem = "error";
        }
    }
}

// Diretório para upload de imagens
$upload_dir = "uploads/perfis/";
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Atualizar dados básicos
    if (isset($_POST['nome'])) {
        $nome = trim($_POST['nome']);
        $telefone = trim($_POST['telefone']);
        $endereco = trim($_POST['endereco']);
        $cidade = trim($_POST['cidade']);
        $estado = trim($_POST['estado']);
        $cep = trim($_POST['cep']);
        
        $sql = "UPDATE usuarios SET nome = ?, telefone = ?, endereco = ?, cidade = ?, estado = ?, cep = ? WHERE id = ?";
        $stmt = $con->prepare($sql);
        
        if ($stmt->execute([$nome, $telefone, $endereco, $cidade, $estado, $cep, $usuario_id])) {
            $_SESSION['nome'] = $nome;
            $mensagem = "Perfil atualizado com sucesso!";
            $tipoMensagem = "success";
            
            $usuario['nome'] = $nome;
            $usuario['telefone'] = $telefone;
            $usuario['endereco'] = $endereco;
            $usuario['cidade'] = $cidade;
            $usuario['estado'] = $estado;
            $usuario['cep'] = $cep;
        } else {
            $mensagem = "Erro ao atualizar perfil. Tente novamente.";
            $tipoMensagem = "error";
        }
    }
    
    // Alteração de senha
    if (isset($_POST['senha_atual']) && !empty($_POST['senha_atual'])) {
        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];
        
        if (password_verify($senha_atual, $usuario['senha'])) {
            if ($nova_senha === $confirmar_senha) {
                if (strlen($nova_senha) >= 6) {
                    $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                    $sql = "UPDATE usuarios SET senha = ? WHERE id = ?";
                    $stmt = $con->prepare($sql);
                    
                    if ($stmt->execute([$nova_senha_hash, $usuario_id])) {
                        $mensagem = "Senha alterada com sucesso!";
                        $tipoMensagem = "success";
                    } else {
                        $mensagem = "Erro ao alterar senha. Tente novamente.";
                        $tipoMensagem = "error";
                    }
                } else {
                    $mensagem = "A nova senha deve ter pelo menos 6 caracteres.";
                    $tipoMensagem = "error";
                }
            } else {
                $mensagem = "As senhas não coincidem.";
                $tipoMensagem = "error";
            }
        } else {
            $mensagem = "Senha atual incorreta.";
            $tipoMensagem = "error";
        }
    }
    
    // Upload de foto de perfil
    if ($coluna_existe && isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        $foto = $_FILES['foto_perfil'];
        $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));
        $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($extensao, $extensoes_permitidas)) {
            if ($foto['size'] <= 2 * 1024 * 1024) {
                $nome_arquivo = 'perfil_' . $usuario_id . '_' . time() . '.' . $extensao;
                $caminho_arquivo = $upload_dir . $nome_arquivo;
                
                if (move_uploaded_file($foto['tmp_name'], $caminho_arquivo)) {
                    if (!empty($usuario['foto_perfil']) && file_exists($usuario['foto_perfil'])) {
                        unlink($usuario['foto_perfil']);
                    }
                    
                    $sql = "UPDATE usuarios SET foto_perfil = ? WHERE id = ?";
                    $stmt = $con->prepare($sql);
                    
                    if ($stmt->execute([$caminho_arquivo, $usuario_id])) {
                        $usuario['foto_perfil'] = $caminho_arquivo;
                        if (empty($mensagem)) {
                            $mensagem = "Foto de perfil atualizada com sucesso!";
                            $tipoMensagem = "success";
                        }
                    }
                }
            }
        }
    }
}

// Buscar dados atualizados do usuário
if ($coluna_existe) {
    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt = $con->prepare($sql);
    $stmt->execute([$usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - LAVELLE Perfumes</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
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
            overflow-x: hidden;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header - Igual ao index.php */
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
            color: #8b7355;
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
            color: #8b7355;
        }
        
        .user-menu a.profile-link:hover {
            background-color: #8b7355;
            color: white;
        }

        /* Seção de Perfil - Estilo similar ao index.php */
        .profile-section {
            padding: 80px 0;
        }

        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .profile-header h1 {
            font-size: 42px;
            color: #000;
            margin-bottom: 15px;
            letter-spacing: 2px;
        }

        .profile-header p {
            font-size: 18px;
            color: #666;
        }

        /* Abas de navegação */
        .profile-tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 40px;
            border-bottom: 2px solid #eee;
            flex-wrap: wrap;
        }

        .profile-tab {
            padding: 15px 30px;
            background: none;
            border: none;
            font-size: 16px;
            font-weight: 500;
            color: #666;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .profile-tab:hover {
            color: #8b7355;
        }

        .profile-tab.active {
            color: #8b7355;
            font-weight: 600;
        }

        .profile-tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #8b7355;
        }

        /* Conteúdo das abas */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Layout do Perfil */
        .profile-layout {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 50px;
            align-items: start;
        }

        /* Sidebar do Perfil */
        .profile-sidebar {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }

        .profile-avatar-container {
            margin-bottom: 30px;
        }

        .profile-avatar {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #8b7355;
            margin: 0 auto 20px;
            display: block;
        }

        .profile-avatar-placeholder {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: linear-gradient(135deg, #8b7355, #000);
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 60px;
            font-weight: bold;
            border: 4px solid #8b7355;
        }

        .upload-btn {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            border: none;
            margin-bottom: 10px;
        }

        .upload-btn:hover {
            background-color: #333;
            transform: translateY(-2px);
        }

        .file-input {
            display: none;
        }

        .file-info {
            font-size: 12px;
            color: #666;
            margin-top: 10px;
        }

        .profile-stats {
            margin-top: 30px;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .stat-item:last-child {
            border-bottom: none;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
        }

        .stat-value {
            font-weight: bold;
            color: #000;
        }

        /* Formulário do Perfil */
        .profile-form-container {
            background: white;
            border-radius: 15px;
            padding: 50px 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .form-section {
            margin-bottom: 40px;
        }

        .form-section h3 {
            font-size: 24px;
            color: #000;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-label {
            display: block;
            margin-bottom: 10px;
            color: #000;
            font-weight: 500;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #eee;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: #f9f9f9;
        }

        .form-input:focus {
            outline: none;
            border-color: #8b7355;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(139, 115, 85, 0.1);
        }

        .form-input:disabled {
            background-color: #f5f5f5;
            color: #666;
        }

        .form-note {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }

        /* Botões - Estilo igual ao index.php */
        .btn {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
        }
        
        .btn:hover {
            background-color: #333;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 2px solid #000;
            color: #000;
        }
        
        .btn-outline:hover {
            background-color: #000;
            color: white;
        }

        .profile-actions {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        /* Alertas */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            font-size: 14px;
            text-align: center;
        }

        .alert-success {
            background: #eaf8ef;
            color: #1f7a34;
            border-left: 4px solid #27ae60;
        }

        .alert-error {
            background: #ffeaea;
            color: #8a1b1b;
            border-left: 4px solid #e74c3c;
        }

        /* Seções adicionais */
        .pedidos-list, .favoritos-grid {
            margin-top: 30px;
        }

        .pedido-item, .favorito-item {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 4px solid #8b7355;
        }

        .pedido-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .pedido-id {
            font-weight: bold;
            color: #000;
        }

        .pedido-status {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pendente { background: #fff3cd; color: #856404; }
        .status-processando { background: #cce7ff; color: #004085; }
        .status-enviado { background: #d1ecf1; color: #0c5460; }
        .status-entregue { background: #d4edda; color: #155724; }

        .pedido-detalhes {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            font-size: 14px;
        }

        .pedido-valor {
            font-weight: bold;
            color: #8b7355;
            font-size: 16px;
        }

        .favoritos-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .favorito-item {
            text-align: center;
            padding: 20px;
            transition: transform 0.3s;
        }

        .favorito-item:hover {
            transform: translateY(-5px);
        }

        .favorito-imagem {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .favorito-nome {
            font-weight: bold;
            margin-bottom: 10px;
            color: #000;
        }

        .favorito-preco {
            color: #8b7355;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #ddd;
        }

        /* Footer - Igual ao index.php */
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
            .header-top {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 15px;
                justify-content: center;
                flex-wrap: wrap;
            }
            
            nav ul li {
                margin: 5px 8px;
            }
            
            .user-menu {
                margin-left: 0;
                padding-left: 0;
                border-left: none;
                justify-content: center;
                width: 100%;
                margin-top: 10px;
            }

            .profile-layout {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .profile-sidebar {
                padding: 30px 25px;
            }

            .profile-form-container {
                padding: 30px 25px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .profile-actions {
                flex-direction: column;
            }

            .profile-actions .btn {
                width: 100%;
                margin-bottom: 10px;
            }

            .profile-avatar,
            .profile-avatar-placeholder {
                width: 140px;
                height: 140px;
            }

            .pedido-detalhes {
                grid-template-columns: 1fr;
            }

            .favoritos-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .profile-header h1 {
                font-size: 32px;
            }

            .profile-form-container {
                padding: 25px 20px;
            }

            .profile-sidebar {
                padding: 25px 20px;
            }

            .profile-avatar,
            .profile-avatar-placeholder {
                width: 120px;
                height: 120px;
            }

            .profile-tabs {
                flex-direction: column;
            }

            .profile-tab {
                text-align: center;
            }
        }
        /* Header Banner */
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
        
    </style>
</head>
<body>
    <div class="header-banner">
        <h1>O perfume certo transforma a presença em memória.</h1>
    </div>
    
    <header>
        <div class="container">
            <div class="header-top">
                <div class="logo">LAVELLE</div>
                <nav>
                    <ul>
                        <li><a href="index.php">INÍCIO</a></li>
                        <li><a href="paginaprodutos.php">PRODUTOS</a></li>
                        <li><a href="sobre.php">SOBRE</a></li>
                        <li><a href="contato.php">CONTATO</a></li>
                        
                        <div class="user-menu">
                            <span style="color: #8b7355; font-weight: 600;">Olá, <?php echo htmlspecialchars($usuario['nome']); ?></span>
                            <li><a href="perfil.php" class="profile-link">MEU PERFIL</a></li>
                            <li><a href="logout.php">SAIR</a></li>
                        </div>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <section class="profile-section">
        <div class="container">
            <div class="profile-container">
                <div class="profile-header">
                    <h1>Meu Perfil</h1>
                    <p>Gerencie suas informações pessoais e preferências</p>
                </div>
                
                <?php if ($mensagem): ?>
                    <div class="alert alert-<?php echo $tipoMensagem === 'success' ? 'success' : 'error'; ?>">
                        <?php echo $mensagem; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Abas de navegação -->
                <div class="profile-tabs">
                    <button class="profile-tab active" data-tab="perfil">Perfil</button>
                   
                    <button class="profile-tab" data-tab="seguranca">Segurança</button>
                </div>
                
                <!-- Conteúdo das abas -->
                <div class="tab-content active" id="tab-perfil">
                    <div class="profile-layout">
                        <!-- Sidebar do Perfil -->
                        <div class="profile-sidebar">
                            <div class="profile-avatar-container">
                                <?php if (!empty($usuario['foto_perfil'])): ?>
                                    <img src="<?php echo htmlspecialchars($usuario['foto_perfil']); ?>" 
                                         alt="Foto de perfil" class="profile-avatar" id="profile-avatar">
                                <?php else: ?>
                                    <div class="profile-avatar-placeholder">
                                        <?php echo strtoupper(substr($usuario['nome'], 0, 1)); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" action="" enctype="multipart/form-data" id="photo-form">
                                    <input type="file" name="foto_perfil" id="foto_perfil" class="file-input" 
                                           accept="image/jpeg, image/png, image/gif" <?php echo !$coluna_existe ? 'disabled' : ''; ?>>
                                    <label for="foto_perfil" class="upload-btn" <?php echo !$coluna_existe ? 'style="background-color: #ccc; cursor: not-allowed;"' : ''; ?>>
                                        <?php echo $coluna_existe ? 'Alterar Foto' : 'Upload Indisponível'; ?>
                                    </label>
                                    <div class="file-info">
                                        <?php if ($coluna_existe): ?>
                                            Formatos: JPG, PNG, GIF (Máx. 2MB)
                                        <?php else: ?>
                                            Sistema de fotos em manutenção
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="profile-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Membro desde</span>
                                    <span class="stat-value">
                                        <?php echo date('d/m/Y', strtotime($usuario['created_at'] ?? $usuario['data_cadastro'] ?? '2024-01-01')); ?>
                                    </span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Pedidos</span>
                                    <span class="stat-value"><?php echo count($pedidos); ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Favoritos</span>
                                    <span class="stat-value"><?php echo count($favoritos); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Formulário do Perfil -->
                        <div class="profile-form-container">
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="form-section">
                                    <h3>Informações Pessoais</h3>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label" for="nome">
                                                <i class=""></i>Nome Completo
                                            </label>
                                            <input type="text" class="form-input" id="nome" name="nome" 
                                                   value="<?php echo htmlspecialchars($usuario['nome']); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label" for="email">
                                                <i class=""></i>E-mail
                                            </label>
                                            <input type="email" class="form-input" id="email" 
                                                   value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled>
                                            <div class="form-note">O e-mail não pode ser alterado</div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label" for="telefone">
                                                <i class=""></i>Telefone
                                            </label>
                                            <input type="tel" class="form-input" id="telefone" name="telefone" 
                                                   value="<?php echo htmlspecialchars($usuario['telefone'] ?? ''); ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label" for="cep">
                                                <i class=""></i>CEP
                                            </label>
                                            <input type="text" class="form-input" id="cep" name="cep" 
                                                   value="<?php echo htmlspecialchars($usuario['cep'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-section">
                                    <h3>Endereço</h3>
                                    
                                    <div class="form-group">
                                        <label class="form-label" for="endereco">
                                            <i class=""></i>Endereço Completo
                                        </label>
                                        <input type="text" class="form-input" id="endereco" name="endereco" 
                                               value="<?php echo htmlspecialchars($usuario['endereco'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label class="form-label" for="cidade">
                                                <i class=""></i>Cidade
                                            </label>
                                            <input type="text" class="form-input" id="cidade" name="cidade" 
                                                   value="<?php echo htmlspecialchars($usuario['cidade'] ?? ''); ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label class="form-label" for="estado">
                                                <i class=""></i>Estado
                                            </label>
                                            <input type="text" class="form-input" id="estado" name="estado" 
                                                   value="<?php echo htmlspecialchars($usuario['estado'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="profile-actions">
                                    <button type="submit" class="btn">
                                        <i class="fas fa-save"></i> Salvar Alterações
                                    </button>
                                    <a href="index.php" class="btn btn-outline">
                                        <i class="fas fa-arrow-left"></i> Voltar para Início
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Aba de Pedidos - VERSÃO CORRIGIDA -->
                <div class="tab-content" id="tab-pedidos">
                    <div class="profile-form-container">
                        <h3>Meus Pedidos Recentes</h3>
                        
                        <?php if (count($pedidos_completos) > 0): ?>
                            <div class="pedidos-list">
                                <?php foreach ($pedidos_completos as $pedido): ?>
                                    <div class="pedido-item">
                                        <div class="pedido-header">
                                            <span class="pedido-id">Pedido #<?php echo $pedido['id']; ?></span>
                                            <span class="pedido-status status-<?php echo strtolower($pedido['status'] ?? 'pendente'); ?>">
                                                <?php echo $pedido['status'] ?? 'Pendente'; ?>
                                            </span>
                                        </div>
                                        <div class="pedido-detalhes">
                                            <div>
                                                <strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?>
                                            </div>
                                            <div>
                                                <strong>Total:</strong> R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?>
                                            </div>
                                            <div>
                                                <strong>Método de Pagamento:</strong> <?php echo $pedido['metodo_pagamento'] ?? 'Não informado'; ?>
                                            </div>
                                            <div class="pedido-valor">
                                                <strong>Status:</strong> <?php echo $pedido['status']; ?>
                                            </div>
                                            <?php if (!empty($pedido['endereco_entrega'])): ?>
                                            <div style="grid-column: 1 / -1; margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(139, 115, 85, 0.1); background: rgba(139, 115, 85, 0.03); padding: 15px; border-radius: 8px;">
                                                <strong style="color: #8b7355;">Endereço de Entrega:</strong><br>
                                                <?php echo nl2br(htmlspecialchars($pedido['endereco_entrega'])); ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-shopping-bag"></i>
                                <h3>Nenhum pedido encontrado</h3>
                                <p>Você ainda não realizou nenhum pedido em nossa loja.</p>
                                <a href="paginaprodutos.php" class="btn" style="margin-top: 20px;">Explorar Produtos</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Aba de Favoritos -->
                <div class="tab-content" id="tab-favoritos">
                    <div class="profile-form-container">
                        <h3>Meus Favoritos</h3>
                        
                        <?php if (count($favoritos) > 0): ?>
                            <div class="favoritos-grid">
                                <?php foreach ($favoritos as $favorito): ?>
                                    <div class="favorito-item">
                                        <img src="<?php echo htmlspecialchars($favorito['imagem'] ?? 'images/produto-placeholder.jpg'); ?>" 
                                             alt="<?php echo htmlspecialchars($favorito['nome']); ?>" class="favorito-imagem">
                                        <div class="favorito-nome"><?php echo htmlspecialchars($favorito['nome']); ?></div>
                                        <div class="favorito-preco">R$ <?php echo number_format($favorito['preco'], 2, ',', '.'); ?></div>
                                        <a href="produto.php?id=<?php echo $favorito['id']; ?>" class="btn" style="padding: 10px 20px; font-size: 14px; width: 100%;">Ver Produto</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-heart"></i>
                                <h3>Nenhum favorito adicionado</h3>
                                <p>Adicione produtos aos seus favoritos para vê-los aqui.</p>
                                <a href="paginaprodutos.php" class="btn" style="margin-top: 20px;">Explorar Produtos</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Aba de Segurança -->
                <div class="tab-content" id="tab-seguranca">
                    <div class="profile-form-container">
                        <form method="POST" action="">
                            <div class="form-section">
                                <h3>Alterar Senha</h3>
                                
                                <div class="form-group">
                                    <label class="form-label" for="senha_atual">
                                        <i class=""></i>Senha Atual
                                    </label>
                                    <input type="password" class="form-input" id="senha_atual" name="senha_atual" placeholder="Digite sua senha atual">
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="" for="nova_senha">
                                            <i class=""></i>Nova Senha
                                        </label>
                                        <input type="password" class="form-input" id="nova_senha" name="nova_senha" placeholder="Digite a nova senha">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="form-label" for="confirmar_senha">
                                            <i class=""></i>Confirmar Nova Senha
                                        </label>
                                        <input type="password" class="form-input" id="confirmar_senha" name="confirmar_senha" placeholder="Confirme a nova senha">
                                    </div>
                                </div>
                                
                                <div class="form-note">
                                    <i class="fas fa-info-circle"></i> A senha deve ter pelo menos 6 caracteres.
                                </div>
                            </div>
                            
                            <div class="profile-actions">
                                <button type="submit" class="btn">
                                    <i class="fas fa-sync-alt"></i> Alterar Senha
                                </button>
                                <a href="index.php" class="btn btn-outline">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <footer>
       <?php include 'footer.php';?>
    </footer>

    <script>
        // Sistema de abas
        document.querySelectorAll('.profile-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove classe active de todas as abas
                document.querySelectorAll('.profile-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                
                // Adiciona classe active à aba clicada
                tab.classList.add('active');
                const tabId = tab.getAttribute('data-tab');
                document.getElementById(`tab-${tabId}`).classList.add('active');
            });
        });
        
        // Upload automático da foto quando selecionada
        document.getElementById('foto_perfil')?.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let avatar = document.getElementById('profile-avatar');
                    if (!avatar) {
                        const placeholder = document.querySelector('.profile-avatar-placeholder');
                        if (placeholder) {
                            placeholder.style.display = 'none';
                            const newAvatar = document.createElement('img');
                            newAvatar.id = 'profile-avatar';
                            newAvatar.className = 'profile-avatar';
                            newAvatar.src = e.target.result;
                            placeholder.parentNode.insertBefore(newAvatar, placeholder.nextSibling);
                        }
                    } else {
                        avatar.src = e.target.result;
                    }
                }
                reader.readAsDataURL(this.files[0]);
                document.getElementById('photo-form').submit();
            }
        });
        
        // Máscara para CEP
        document.getElementById('cep')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0,5) + '-' + value.substring(5,8);
            }
            e.target.value = value;
        });
        
        // Máscara para telefone
        document.getElementById('telefone')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = '(' + value.substring(0,2) + ') ' + value.substring(2);
            }
            if (value.length > 10) {
                value = value.substring(0,10) + '-' + value.substring(10,14);
            }
            e.target.value = value;
        });
    </script>
</body>
</html>