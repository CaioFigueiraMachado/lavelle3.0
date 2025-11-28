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
    <style>
        @font-face {
            font-family: 'Questal Small Caps Medium';
            src: url('https://fonts.cdnfonts.com/s/97263/QuestalSCMedium.woff') format('woff');
            font-weight: normal;
            font-style: normal;
        }
        
        /* Estilos do header */
        header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
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
            color: #000000ff;
            text-decoration: none;
            letter-spacing: 1px;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            align-items: center;
        }
        
        nav li {
            margin-left: 25px;
        }
        
        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: color 0.3s;
        }
        
        nav a:hover {
            color: #8b7355;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
        }
        
        .user-menu span {
            margin-right: 15px;
        }
        
        .profile-link {
            color: #8b7355 !important;
        }
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
            font-weight: 400;
            margin: 0;
            padding: 0;
            letter-spacing: 3px;
            color: #f5f5f5;
        }
    </style>
        <div class="container">
            <div class="header-top">
                <div class="logo"><?php echo $empresa ?? 'LAVELLE'; ?></div>
                <nav>
                    <ul>
                        <li><a href="index.php">INÍCIO</a></li>
                        <li><a href="paginaprodutos.php">PRODUTOS</a></li>
                       
                        <li><a href="sobre.php">SOBRE</a></li>
                        <li><a href="contato.php">CONTATO</a></li>
                        
                        <!-- Menu do Usuário - CORRIGIDO -->
                        <?php if ($usuarioLogado): ?>
                            <div class="user-menu">
                               
                          
                                
                                <li><a href="logout.php">SAIR</a></li>
                            </div>
                        <?php else: ?>
                            <div class="user-menu">
                                <li><a href="login.php">ENTRAR</a></li>
                              
                            </div>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>