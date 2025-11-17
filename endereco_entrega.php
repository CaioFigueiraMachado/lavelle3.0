<?php
session_start();
include 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Verificar se há itens no carrinho
if (empty($_SESSION['carrinho'])) {
    header('Location: paginaprodutos.php');
    exit();
}

$usuario_id = $_SESSION['id'];
$mensagem = "";
$tipoMensagem = "";

// Buscar dados do usuário e endereço salvo
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Processar envio do formulário de endereço
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmar_endereco'])) {
    // Validar dados obrigatórios
    $required_fields = ['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado'];
    $campos_faltando = [];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $campos_faltando[] = $field;
        }
    }
    
    if (!empty($campos_faltando)) {
        $mensagem = "Por favor, preencha todos os campos obrigatórios.";
        $tipoMensagem = "error";
    } else {
        // Coletar dados do endereço
        $endereco = [
            'cep' => $_POST['cep'],
            'logradouro' => $_POST['logradouro'],
            'numero' => $_POST['numero'],
            'complemento' => $_POST['complemento'] ?? '',
            'bairro' => $_POST['bairro'],
            'cidade' => $_POST['cidade'],
            'estado' => $_POST['estado'],
            'ponto_referencia' => $_POST['ponto_referencia'] ?? ''
        ];
        
        // Salvar endereço na sessão para o pedido atual
        $_SESSION['endereco_entrega'] = $endereco;
        
        // Atualizar endereço no perfil do usuário (opcional)
        $sql_update = "UPDATE usuarios SET 
                      cep = ?, 
                      endereco = ?, 
                      cidade = ?, 
                      estado = ? 
                      WHERE id = ?";
        
        $stmt_update = $con->prepare($sql_update);
        $endereco_completo = $endereco['logradouro'] . ', ' . $endereco['numero'] . 
                            ($endereco['complemento'] ? ' - ' . $endereco['complemento'] : '') . 
                            ' - ' . $endereco['bairro'];
        
        if ($stmt_update->execute([
            $endereco['cep'],
            $endereco_completo,
            $endereco['cidade'],
            $endereco['estado'],
            $usuario_id
        ])) {
            // Endereço salvo no perfil com sucesso
        }
        
        // Configurar redirecionamento para o método de pagamento escolhido
        $metodo_pagamento = $_SESSION['metodo_pagamento'];
        
        // Definir URL de redirecionamento baseada no método de pagamento
        switch($metodo_pagamento) {
            case 'credit':
                $redirect_url = 'pagamento_cartao.php';
                break;
            case 'pix':
                $redirect_url = 'pagamento_pix.php';
                break;
            case 'boleto':
                $redirect_url = 'pagamento_boleto.php';
                break;
            default:
                $redirect_url = 'paginaprodutos.php';
                break;
        }
        
        // Armazenar a URL de redirecionamento na sessão para usar no JavaScript
        $_SESSION['redirect_url'] = $redirect_url;
        
        // Em vez de redirecionar imediatamente, vamos mostrar o SweetAlert
        // O redirecionamento será feito via JavaScript após o usuário confirmar
    }
}

// Tentar preencher automaticamente com dados do perfil se disponível
$endereco_preenchido = [];
if (!empty($usuario['cep'])) {
    $endereco_preenchido['cep'] = $usuario['cep'];
}

// Tentar extrair informações do endereço completo se disponível
if (!empty($usuario['endereco'])) {
    // Tentar extrair logradouro, número, complemento e bairro do endereço completo
    $endereco_completo = $usuario['endereco'];
    $endereco_preenchido['logradouro'] = $endereco_completo;
    
    // Tentativa básica de extrair número (procura por vírgula seguida de números)
    if (preg_match('/(.*),\s*(\d+)/', $endereco_completo, $matches)) {
        $endereco_preenchido['logradouro'] = trim($matches[1]);
        // O número seria $matches[2], mas não vamos preencher automaticamente por segurança
    }
}

if (!empty($usuario['cidade'])) {
    $endereco_preenchido['cidade'] = $usuario['cidade'];
}
if (!empty($usuario['estado'])) {
    $endereco_preenchido['estado'] = $usuario['estado'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endereço de Entrega - LAVELLE</title>
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

        /* Seção Principal */
        .main-content {
            padding: 80px 0;
        }

        .page-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .page-header h1 {
            font-size: 42px;
            color: #000;
            margin-bottom: 15px;
            letter-spacing: 2px;
        }

        .page-header p {
            font-size: 18px;
            color: #666;
        }

        /* Layout Principal */
        .main-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
            align-items: start;
        }

        /* Container do Endereço */
        .address-container {
            background: white;
            border-radius: 15px;
            padding: 50px 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .address-header {
            display: flex;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 25px;
            border-bottom: 2px solid #f0f0f0;
        }

        .address-icon {
            font-size: 28px;
            margin-right: 20px;
            color: #8b7355;
        }

        .address-title {
            font-size: 28px;
            font-weight: 600;
            color: #000;
        }

        /* Sidebar do Resumo */
        .order-sidebar {
            background: white;
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: sticky;
            top: 100px;
        }

        .summary-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .summary-icon {
            font-size: 24px;
            margin-right: 15px;
            color: #8b7355;
        }

        .summary-title {
            font-size: 24px;
            font-weight: 600;
            color: #000;
        }

        .summary-items {
            margin-bottom: 25px;
            max-height: 300px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .item-info {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            color: #000;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .item-quantity {
            color: #666;
            font-size: 12px;
        }

        .item-price {
            font-weight: 600;
            color: #8b7355;
            font-size: 14px;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #eee;
            font-weight: 700;
            font-size: 20px;
            color: #000;
        }

        .delivery-info {
            background: rgba(139, 115, 85, 0.05);
            border-radius: 10px;
            padding: 20px;
            margin-top: 25px;
            border: 1px solid rgba(139, 115, 85, 0.1);
        }

        .delivery-info h4 {
            color: #8b7355;
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: 600;
        }

        .delivery-info p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Formulário */
        .form-section {
            margin-bottom: 40px;
        }

        .section-title {
            font-size: 24px;
            color: #000;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
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

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
            margin-bottom: 25px;
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

        .btn-primary {
            background-color: #8b7355;
        }

        .btn-primary:hover {
            background-color: #7a6345;
        }

        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .btn i {
            margin-right: 10px;
        }

        /* Alertas */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            font-size: 14px;
            text-align: center;
        }

        .alert-error {
            background: #ffeaea;
            color: #8a1b1b;
            border-left: 4px solid #e74c3c;
        }

        /* Botão de preenchimento automático */
        .auto-fill-btn {
            background-color: #8b7355;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 12px;
            margin-left: 10px;
            transition: all 0.3s;
        }

        .auto-fill-btn:hover {
            background-color: #7a6345;
            transform: translateY(-2px);
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

        /* Responsividade */
        @media (max-width: 1024px) {
            .main-layout {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .order-sidebar {
                order: 2;
                position: static;
            }
            
            .address-container {
                order: 1;
            }
        }

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

            .main-content {
                padding: 60px 0;
            }

            .page-header h1 {
                font-size: 32px;
            }

            .address-container {
                padding: 30px 25px;
            }

            .order-sidebar {
                padding: 30px 25px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .btn-container {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .page-header h1 {
                font-size: 28px;
            }

            .address-container {
                padding: 25px 20px;
            }

            .order-sidebar {
                padding: 25px 20px;
            }

            .address-header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .address-icon {
                margin-right: 0;
            }
        }

        /* Estilos para o select */
        select.form-input {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238b7355' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 20px center;
            background-size: 16px;
            padding-right: 50px;
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

    <section class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Endereço de Entrega</h1>
                <p>Informe onde deseja receber seus produtos</p>
            </div>
            
            <?php if ($mensagem): ?>
                <div class="alert alert-error">
                    <?php echo $mensagem; ?>
                </div>
            <?php endif; ?>
            
            <div class="main-layout">
                <!-- Formulário de Endereço -->
                <div class="address-container">
                    <div class="address-header">
                        <div class="address-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="address-title">Informações de Entrega</div>
                    </div>
                    
                    <form method="POST" action="" id="addressForm">
                        <div class="form-section">
                            <h3 class="section-title">Endereço de Entrega</h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="cep">
                                        <i class=""></i>CEP <span style="color: #e74c3c;">*</span>
                                        <?php if (!empty($endereco_preenchido['cep'])): ?>
                                            <button type="button" class="auto-fill-btn" onclick="fillAddressFromProfile()">
                                                <i class="fas fa-user-check"></i> Usar do perfil
                                            </button>
                                        <?php endif; ?>
                                    </label>
                                    <input type="text" class="form-input" id="cep" name="cep" 
                                           value="<?php echo $_POST['cep'] ?? $endereco_preenchido['cep'] ?? ''; ?>" 
                                           required maxlength="9" placeholder="00000-000">
                                    <div class="form-note">Digite o CEP para preenchimento automático</div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="numero">
                                        <i class=""></i>Número <span style="color: #e74c3c;">*</span>
                                    </label>
                                    <input type="text" class="form-input" id="numero" name="numero" 
                                           value="<?php echo $_POST['numero'] ?? ''; ?>" 
                                           required placeholder="123">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="logradouro">
                                    <i class=""></i>Logradouro <span style="color: #e74c3c;">*</span>
                                </label>
                                <input type="text" class="form-input" id="logradouro" name="logradouro" 
                                       value="<?php echo $_POST['logradouro'] ?? $endereco_preenchido['logradouro'] ?? ''; ?>" 
                                       required placeholder="Rua, Avenida, etc.">
                            </div>
                            
                            <div class="form-group">
                                <label class="" for="complemento">
                                    <i class=""></i>Complemento
                                </label>
                                <input type="text" class="form-input" id="complemento" name="complemento" 
                                       value="<?php echo $_POST['complemento'] ?? ''; ?>" 
                                       placeholder="Apartamento, Bloco, etc.">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="bairro">
                                        <i class=""></i>Bairro <span style="color: #e74c3c;">*</span>
                                    </label>
                                    <input type="text" class="form-input" id="bairro" name="bairro" 
                                           value="<?php echo $_POST['bairro'] ?? ''; ?>" 
                                           required placeholder="Centro">
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="cidade">
                                        <i class=""></i>Cidade <span style="color: #e74c3c;">*</span>
                                    </label>
                                    <input type="text" class="form-input" id="cidade" name="cidade" 
                                           value="<?php echo $_POST['cidade'] ?? $endereco_preenchido['cidade'] ?? ''; ?>" 
                                           required placeholder="São Paulo">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label" for="estado">
                                        <i class=""></i>Estado <span style="color: #e74c3c;">*</span>
                                    </label>
                                    <select class="form-input" id="estado" name="estado" required>
                                        <option value="">Selecione o estado</option>
                                        <option value="AC" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'AC' ? 'selected' : ''; ?>>Acre</option>
                                        <option value="AL" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'AL' ? 'selected' : ''; ?>>Alagoas</option>
                                        <option value="AP" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'AP' ? 'selected' : ''; ?>>Amapá</option>
                                        <option value="AM" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'AM' ? 'selected' : ''; ?>>Amazonas</option>
                                        <option value="BA" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'BA' ? 'selected' : ''; ?>>Bahia</option>
                                        <option value="CE" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'CE' ? 'selected' : ''; ?>>Ceará</option>
                                        <option value="DF" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'DF' ? 'selected' : ''; ?>>Distrito Federal</option>
                                        <option value="ES" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'ES' ? 'selected' : ''; ?>>Espírito Santo</option>
                                        <option value="GO" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'GO' ? 'selected' : ''; ?>>Goiás</option>
                                        <option value="MA" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'MA' ? 'selected' : ''; ?>>Maranhão</option>
                                        <option value="MT" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'MT' ? 'selected' : ''; ?>>Mato Grosso</option>
                                        <option value="MS" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'MS' ? 'selected' : ''; ?>>Mato Grosso do Sul</option>
                                        <option value="MG" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'MG' ? 'selected' : ''; ?>>Minas Gerais</option>
                                        <option value="PA" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'PA' ? 'selected' : ''; ?>>Pará</option>
                                        <option value="PB" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'PB' ? 'selected' : ''; ?>>Paraíba</option>
                                        <option value="PR" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'PR' ? 'selected' : ''; ?>>Paraná</option>
                                        <option value="PE" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'PE' ? 'selected' : ''; ?>>Pernambuco</option>
                                        <option value="PI" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'PI' ? 'selected' : ''; ?>>Piauí</option>
                                        <option value="RJ" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'RJ' ? 'selected' : ''; ?>>Rio de Janeiro</option>
                                        <option value="RN" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'RN' ? 'selected' : ''; ?>>Rio Grande do Norte</option>
                                        <option value="RS" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'RS' ? 'selected' : ''; ?>>Rio Grande do Sul</option>
                                        <option value="RO" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'RO' ? 'selected' : ''; ?>>Rondônia</option>
                                        <option value="RR" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'RR' ? 'selected' : ''; ?>>Roraima</option>
                                        <option value="SC" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'SC' ? 'selected' : ''; ?>>Santa Catarina</option>
                                        <option value="SP" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'SP' ? 'selected' : ''; ?>>São Paulo</option>
                                        <option value="SE" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'SE' ? 'selected' : ''; ?>>Sergipe</option>
                                        <option value="TO" <?php echo ($_POST['estado'] ?? $endereco_preenchido['estado'] ?? '') == 'TO' ? 'selected' : ''; ?>>Tocantins</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label" for="ponto_referencia">
                                        <i class=""></i>Ponto de Referência
                                    </label>
                                    <input type="text" class="form-input" id="ponto_referencia" name="ponto_referencia" 
                                           value="<?php echo $_POST['ponto_referencia'] ?? ''; ?>" 
                                           placeholder="Próximo ao...">
                                </div>
                            </div>
                        </div>
                        
                        <div class="btn-container">
                            <button type="submit" name="confirmar_endereco" class="btn btn-primary">
                                <i class="fas fa-check-circle"></i> Confirmar Endereço
                            </button>
                            
                            <a href="paginaprodutos.php" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Voltar aos Produtos
                            </a>
                        </div>
                    </form>
                </div>
                
                <!-- Sidebar do Resumo -->
                <div class="order-sidebar">
                    <div class="summary-header">
                        <div class="summary-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="summary-title">Resumo do Pedido</div>
                    </div>
                    
                    <div class="summary-items">
                        <?php 
                        $total_pedido = 0;
                        if (isset($_SESSION['produtos_carrinho'])) {
                            foreach ($_SESSION['produtos_carrinho'] as $item) {
                                $produto = $item['produto'];
                                $quantidade = $item['quantidade'];
                                $subtotal = $item['subtotal'];
                                $total_pedido += $subtotal;
                        ?>
                            <div class="summary-item">
                                <div class="item-info">
                                    <div class="item-name"><?php echo $produto['nome']; ?></div>
                                    <div class="item-quantity">Quantidade: <?php echo $quantidade; ?>x</div>
                                </div>
                                <div class="item-price">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></div>
                            </div>
                        <?php 
                            }
                        }
                        ?>
                    </div>
                    
                    <div class="summary-total">
                        <span>Total:</span>
                        <span>R$ <?php echo number_format($total_pedido, 2, ',', '.'); ?></span>
                    </div>
                    
                    <div class="delivery-info">
                        <h4><i class="fas fa-info-circle"></i> Informações de Entrega</h4>
                        <p>O prazo de entrega é de 5 a 10 dias úteis após a confirmação do pagamento.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>CONTATO</h3>
                    <div class="contact-info">
                        <p><i class="fas fa-envelope"></i> E-mail: contatolavelle@gmail.com</p>
                        <p><i class="fas fa-map-marker-alt"></i> Endereço: Rua das Fragrâncias, 123 - Jardim Perfumado</p>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>REDES SOCIAIS</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i> Facebook</a><br>
                        <a href="#"><i class="fab fa-instagram"></i> Instagram</a><br>
                        <a href="#"><i class="fab fa-twitter"></i> Twitter</a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>POLÍTICAS</h3>
                    <ul>
                        <li><a href="#"><i class="fas fa-shield-alt"></i> Política de Privacidade</a></li>
                        <li><a href="#"><i class="fas fa-file-contract"></i> Termos de Uso</a></li>
                        <li><a href="#"><i class="fas fa-exchange-alt"></i> Trocas e Devoluções</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>INFORMAÇÕES</h3>
                    <ul>
                        <li><a href="sobre.php"><i class="fas fa-info-circle"></i> Sobre Nós</a></li>
                        <li><a href="#"><i class="fas fa-history"></i> Nossa História</a></li>
                        <li><a href="#"><i class="fas fa-briefcase"></i> Trabalhe Conosco</a></li>
                        <li><a href="#"><i class="fas fa-question-circle"></i> FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> LAVELLE Perfumes. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Função auxiliar para formatar CEP
        function formatarCEP(cep) {
            return cep.replace(/\D/g, '').replace(/(\d{5})(\d{3})/, '$1-$2');
        }

        // Máscara para CEP - Versão melhorada
        document.getElementById('cep')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0,5) + '-' + value.substring(5,8);
            }
            e.target.value = value;
        });

        // Adicionar evento para buscar CEP quando o campo perder o foco (mais intuitivo)
        document.getElementById('cep')?.addEventListener('blur', function(e) {
            let cep = e.target.value.replace(/\D/g, '');
            if (cep.length === 8) {
                buscarEnderecoPorCEP(cep);
            }
        });
        
        // Melhorar a função de busca por CEP
        function buscarEnderecoPorCEP(cep) {
            cep = cep.replace(/\D/g, ''); // Remove qualquer caractere não numérico
            
            if (cep.length !== 8) {
                Swal.fire({
                    title: 'CEP inválido',
                    text: 'O CEP deve conter 8 dígitos.',
                    icon: 'warning',
                    confirmButtonColor: '#8b7355'
                });
                return;
            }
            
            // Mostrar loading
            Swal.fire({
                title: 'Buscando endereço...',
                text: 'Aguarde enquanto buscamos o endereço.',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    
                    if (!data.erro) {
                        document.getElementById('logradouro').value = data.logradouro || '';
                        document.getElementById('bairro').value = data.bairro || '';
                        document.getElementById('cidade').value = data.localidade || '';
                        document.getElementById('estado').value = data.uf || '';
                        
                        // Focar no campo número
                        document.getElementById('numero').focus();
                        
                        Swal.fire({
                            title: 'Endereço encontrado!',
                            text: 'Endereço preenchido automaticamente. Verifique e complete as informações.',
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: 'CEP não encontrado',
                            text: 'Verifique o CEP digitado e tente novamente.',
                            icon: 'warning',
                            confirmButtonColor: '#8b7355'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Erro ao buscar CEP:', error);
                    Swal.fire({
                        title: 'Erro de conexão',
                        text: 'Não foi possível buscar o endereço. Verifique sua conexão e tente novamente.',
                        icon: 'error',
                        confirmButtonColor: '#8b7355'
                    });
                });
        }
        
        // Função para preencher com dados do perfil
        function fillAddressFromProfile() {
            Swal.fire({
                title: 'Usar endereço do perfil?',
                text: 'Os dados do seu perfil serão preenchidos automaticamente.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, usar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#8b7355',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    const cepPerfil = '<?php echo $endereco_preenchido["cep"] ?? ""; ?>';
                    if (cepPerfil) {
                        // Formatar o CEP corretamente (garantir formato 00000-000)
                        let cepFormatado = cepPerfil.replace(/\D/g, '');
                        if (cepFormatado.length === 8) {
                            cepFormatado = cepFormatado.substring(0,5) + '-' + cepFormatado.substring(5,8);
                        }
                        
                        document.getElementById('cep').value = cepFormatado;
                        
                        // Buscar endereço usando o CEP formatado
                        buscarEnderecoPorCEP(cepFormatado);
                        
                        // Preencher outros campos diretamente do perfil
                        <?php if (!empty($endereco_preenchido['cidade'])): ?>
                            document.getElementById('cidade').value = '<?php echo $endereco_preenchido["cidade"]; ?>';
                        <?php endif; ?>
                        
                        <?php if (!empty($endereco_preenchido['estado'])): ?>
                            document.getElementById('estado').value = '<?php echo $endereco_preenchido["estado"]; ?>';
                        <?php endif; ?>
                        
                    } else {
                        Swal.fire({
                            title: 'Dados não encontrados',
                            text: 'Não há endereço salvo no seu perfil.',
                            icon: 'info',
                            confirmButtonColor: '#8b7355'
                        });
                    }
                }
            });
        }
        
        // Validação do formulário
        document.getElementById('addressForm').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let valid = true;
            let firstInvalidField = null;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    valid = false;
                    field.style.borderColor = '#e74c3c';
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                } else {
                    field.style.borderColor = '#eee';
                }
            });
            
            if (!valid) {
                e.preventDefault();
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
                Swal.fire({
                    title: 'Campos obrigatórios',
                    text: 'Por favor, preencha todos os campos marcados com *',
                    icon: 'warning',
                    confirmButtonColor: '#8b7355'
                });
            }
        });

        // Verificar se há redirecionamento pendente (após o formulário ser enviado com sucesso)
        <?php if (isset($_SESSION['redirect_url'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Endereço confirmado!',
                text: 'Endereço salvo com sucesso. Redirecionando para a página de pagamento...',
                icon: 'success',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                window.location.href = '<?php echo $_SESSION['redirect_url']; ?>';
            });
        });
        <?php 
            // Limpar a URL de redirecionamento da sessão após usar
            unset($_SESSION['redirect_url']);
        endif; 
        ?>
    </script>
</body>
</html>