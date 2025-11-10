<?php
// admin/includes/sidebar.php
?>
        <aside class="sidebar">
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