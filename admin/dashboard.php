<?php
// admin/dashboard.php
session_start();
if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

include 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Estatísticas - com tratamento de erro
$total_usuarios = 0;
$total_produtos = 0;
$ultimos_usuarios = [];

try {
    // Contar usuários
    $query_usuarios = "SELECT COUNT(*) as total FROM usuarios";
    $stmt_usuarios = $db->prepare($query_usuarios);
    $stmt_usuarios->execute();
    $total_usuarios = $stmt_usuarios->fetch(PDO::FETCH_ASSOC)['total'];
} catch(PDOException $e) {
    $total_usuarios = 0;
}

try {
    // Contar produtos
    $query_produtos = "SELECT COUNT(*) as total FROM produtos";
    $stmt_produtos = $db->prepare($query_produtos);
    $stmt_produtos->execute();
    $total_produtos = $stmt_produtos->fetch(PDO::FETCH_ASSOC)['total'];
} catch(PDOException $e) {
    $total_produtos = 0;
}

try {
    // Últimos usuários cadastrados
    $query_ultimos_usuarios = "SELECT nome, email, created_at FROM usuarios ORDER BY created_at DESC LIMIT 5";
    $stmt_ultimos_usuarios = $db->prepare($query_ultimos_usuarios);
    $stmt_ultimos_usuarios->execute();
    $ultimos_usuarios = $stmt_ultimos_usuarios->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $ultimos_usuarios = [];
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <h1>Dashboard Administrativo</h1>
        <p>Bem-vindo ao painel de controle da LAVELLE Perfumes</p>
        <?php if($total_produtos === 0): ?>
            <div style="margin-top: 10px;">
                <a href="criar_tabelas.php" class="btn-primary" style="font-size: 0.8rem; background: #dc3545;">
                    Configurar Banco de Dados
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if($total_produtos === 0): ?>
        <div class="alert error">
            <strong>Atenção:</strong> O banco de dados não está configurado. 
            <a href="criar_tabelas.php" style="color: #721c24; text-decoration: underline; font-weight: bold;">
                Clique aqui para configurar automaticamente
            </a>
        </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
          
            <div class="stat-info">
                <div class="stat-number"><?php echo $total_usuarios; ?></div>
                <div class="stat-label">Total de Usuários</div>
            </div>
        </div>
        
        <div class="stat-card">
           
            <div class="stat-info">
                <div class="stat-number"><?php echo $total_produtos; ?></div>
                <div class="stat-label">Total de Produtos</div>
            </div>
        </div>
        
        <div class="stat-card">
           
            <div class="stat-info">
                <div class="stat-number">R$ 12.500</div>
                <div class="stat-label">Receita Total</div>
            </div>
        </div>
        
        <div class="stat-card">
            
            <div class="stat-info">
                <div class="stat-number">245</div>
                <div class="stat-label">Pedidos Concluídos</div>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <div class="content-card">
            <div class="card-header">
                <h2>Últimos Usuários Cadastrados</h2>
                <a href="usuarios.php" class="btn-link">Ver Todos</a>
            </div>
            <div class="table-responsive">
                <?php if(!empty($ultimos_usuarios)): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Data Cadastro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ultimos_usuarios as $usuario): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                <td>
                                    <?php 
                                    if(isset($usuario['created_at'])) {
                                        echo date('d/m/Y', strtotime($usuario['created_at']));
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 2rem;">
                        <?php echo $total_usuarios === 0 ? 'Nenhum usuário cadastrado.' : 'Erro ao carregar usuários.'; ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header">
                <h2>Ações Rápidas</h2>
            </div>
            <div class="quick-actions">
                <a href="usuarios.php?action=create" class="quick-action-btn">
                    <div class="action-icon">
                        <img src="../novouser.png" alt="Novo Usuário" class="action-icon-img">
                    </div>
                    <span>Novo Usuário</span>
                </a>
                <a href="produtos.php?action=create" class="quick-action-btn">
                    <div class="action-icon">
                        <img src="../novo.png" alt="Novo Produto" class="action-icon-img">
                    </div>
                    <span>Novo Produto</span>
                </a>
                <a href="usuarios.php" class="quick-action-btn">
                    <div class="action-icon">
                        <img src="../iconmais.png" alt="Gerenciar Usuários" class="action-icon-img">
                    </div>
                    <span>Gerenciar Usuários</span>
                </a>
                <a href="produtos.php" class="quick-action-btn">
                    <div class="action-icon">
                        <img src="../gerenciarprodutos.png" alt="Gerenciar Produtos" class="action-icon-img">
                    </div>
                    <span>Gerenciar Produtos</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>