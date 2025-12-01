<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header com Fonte Questal</title>
    <style>
        /* Importando a fonte Questal Small Caps Medium */
         @font-face {
            font-family: 'Questal Small Caps Medium';
            src: url('https://fonts.cdnfonts.com/s/97263/QuestalSCMedium.woff') format('woff');
            font-weight: normal;
            font-style: normal;
        }
        
        /* Header */
        header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo {
            font-family: 'Questal Small Caps Medium', serif;
            font-size: 28px;
            font-weight: bold;
            color: #000;
            letter-spacing: 2px;
            text-decoration: none;
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
        
        .user-menu a.profile-link {
            background-color: #f5f5f5;
            color: #8b7355;
        }
        
        .user-menu a.profile-link:hover {
            background-color: #8b7355;
            color: white;
        }
        
        /* Hamburguer Icon - Mobile */
        .hamburguer {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
            background: none;
            border: none;
            width: 30px;
            height: 30px;
            justify-content: center;
            align-items: center;
        }
        
        .hamburguer span {
            width: 25px;
            height: 2px;
            background: #333;
            margin: 2px 0;
            transition: 0.3s;
            display: block;
        }
        
        /* Menu Mobile */
        .mobile-nav {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            z-index: 99;
            padding: 80px 20px 20px;
            overflow-y: auto;
        }
        
        .mobile-nav.active {
            display: block;
        }
        
        .mobile-nav ul {
            list-style: none;
            flex-direction: column;
        }
        
        .mobile-nav li {
            margin: 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .mobile-nav a {
            display: block;
            padding: 15px 0;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 0;
        }
        
        .mobile-nav a:hover {
            color: #8b7355;
            background: none;
        }
        
        .mobile-user-menu {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #eee;
        }
        
        .user-welcome {
            display: block;
            padding: 15px 0;
            color: #8b7355;
            font-weight: 600;
            border-bottom: 1px solid #f0f0f0;
        }
        
        /* Animações do Hamburguer */
        .hamburguer.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        
        .hamburguer.active span:nth-child(2) {
            opacity: 0;
        }
        
        .hamburguer.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -6px);
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
        @media (max-width: 768px) {
            .desktop-nav {
                display: none;
            }
            
            .hamburguer {
                display: flex;
            }
            
            .header-top {
                justify-content: space-between;
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
        }
        
        @media (max-width: 480px) {
            .container {
                padding: 0 15px;
            }
            
            .logo {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <?php
    // Inicializar variáveis de sessão se não estiverem definidas
    if (!isset($usuarioLogado)) {
        $usuarioLogado = false;
        $usuarioNome = "";
        
        // Verificar múltiplos padrões de nomes de sessão
        if (isset($_SESSION['usuario_id']) || isset($_SESSION['id'])) {
            $usuarioLogado = true;
            $usuarioNome = $_SESSION['usuario_nome'] ?? $_SESSION['nome'] ?? 'Usuário';
        }
    }
    ?>
    <header>
      <div class="container">
            <div class="header-top">
                <div class="logo"><?php echo $empresa; ?></div>
                <nav>
                    <ul>
                        <li><a href="index.php">INÍCIO</a></li>
                        <li><a href="paginaprodutos.php"">PRODUTOS</a></li>
                        <li><a href="sobre.php">SOBRE</a></li>
                        <li><a href="contato.php">CONTATO</a></li>
                        
                        <!-- Menu do Usuário -->
                        <?php if ($usuarioLogado): ?>
                            <div class="user-menu">
                                <span style="color: #8b7355; font-weight: 500;">Olá, <?php echo htmlspecialchars($usuarioNome); ?></span>
                              
                                
                                <!-- LINK ADM - APENAS PARA ADMINISTRADOR -->
                                <?php if ($isAdmin): ?>
                                    <li><a href="admin/dashboard.php" class="admin-link">ADM</a></li>
                                <?php endif; ?>
                                 <li>
                                    <button class="cart-icon" onclick="openCartModal()">
                                        CARRINHO
                                        <?php if (count($_SESSION['carrinho']) > 0): ?>
                                            <span class="cart-badge"><?php echo array_sum($_SESSION['carrinho']); ?></span>
                                        <?php endif; ?>
                                    </button>
                                </li>
                                
                            </div>
                        <?php else: ?>
                            <div class="user-menu">
                                <li><a href="login.php">ENTRAR</a></li>
                                <li>
                                    <button class="cart-icon" onclick="openCartModal()">
                                        CARRINHO
                                        <?php if (count($_SESSION['carrinho']) > 0): ?>
                                            <span class="cart-badge"><?php echo array_sum($_SESSION['carrinho']); ?></span>
                                        <?php endif; ?>
                                    </button>
                                </li>
                            </div>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
</body>
</html>