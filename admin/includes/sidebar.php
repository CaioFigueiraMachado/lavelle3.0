<?php
// admin/includes/sidebar.php
?>
<style>
    /* Estilos para o logo na sidebar */
    .sidebar-logo {
        padding: 20px;
        margin-bottom: 20px;
        text-align: center;
         font-family: 'Questal Small Caps Medium', serif;
        font-size: 28px;
        font-weight: bold;
        color: #ffffffff;
        letter-spacing: 2px;
        text-decoration: none;
        display: block;
        text-align: center;
    }
    
    .sidebar-logo .logo {
        font-family: 'Questal Small Caps Medium', serif;
        font-size: 28px;
        font-weight: bold;
        color: #ffffffff;
        letter-spacing: 2px;
        text-decoration: none;
        display: block;
        text-align: center;
    }
    
    /* Estilos responsivos para o logo */
    @media (max-width: 768px) {
        .sidebar-logo .logo {
            font-size: 24px;
        }
        
        .sidebar-logo {
            padding: 15px;
            margin-bottom: 15px;
        }
    }
</style>

<aside class="sidebar">
    <!-- Logo LAVELLE -->

        <span class="sidebar-logo">LAVELLE</span>
   
    
    <nav class="sidebar-nav">
        <ul>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <a href="dashboard.php">
                    <span class="nav-icon"></span>
                    Dashboard
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'usuarios.php' ? 'active' : ''; ?>">
                <a href="usuarios.php">
                    <span class="nav-icon"></span>
                    Gerenciar Usuários
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'produtos.php' ? 'active' : ''; ?>">
                <a href="produtos.php">
                    <span class="nav-icon"></span>
                    Gerenciar Produtos
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'pedidos.php' ? 'active' : ''; ?>">
                <a href="pedidos.php">
                    <span class="nav-icon"></span>
                    Gerenciar Pedidos
                </a>
            </li>
            <li class="nav-item <?php echo basename($_SERVER['PHP_SELF']) == 'admin_contato.php' ? 'active' : ''; ?>">
                <a href="admin_contato.php">
                    <span class="nav-icon"></span>
                    Gerenciar Contatos
                </a>
            </li>
            <li class="nav-item">
                <a href="../paginaprodutos.php">
                    <span class="nav-icon"></span>
                    Ver Loja
                </a>
            </li>
            <li class="nav-item logout-item">
                <a href="../logout.php" class="logout-link">
                    <span class="nav-icon"></span>
                    Sair
                </a>
            </li>
        </ul>
    </nav>
</aside>

<div class="main-content-wrapper">