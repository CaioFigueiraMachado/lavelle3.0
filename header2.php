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
                                <span style="color: #8b7355; font-weight: 500;">Olá, <?php echo htmlspecialchars($usuarioNome); ?></span>
                                
                                
                                <!-- LINK ADM - APENAS PARA ADMINISTRADOR -->
                          
                                
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