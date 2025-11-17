<?php
// admin/dashboard.php
session_start();
if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

include 'config/database.php';
include '../receipt_generator.php'; // Incluir o gerador de comprovantes

$database = new Database();
$db = $database->getConnection();

// Estatísticas - com tratamento de erro
$total_usuarios = 0;
$total_produtos = 0;
$total_pedidos = 0;
$receita_total = 0;
$ultimos_usuarios = [];
$ultimos_pedidos = [];

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
    // Contar pedidos e calcular receita
    $query_pedidos = "SELECT COUNT(*) as total, COALESCE(SUM(total), 0) as receita FROM pedidos";
    $stmt_pedidos = $db->prepare($query_pedidos);
    $stmt_pedidos->execute();
    $result_pedidos = $stmt_pedidos->fetch(PDO::FETCH_ASSOC);
    $total_pedidos = $result_pedidos['total'];
    $receita_total = $result_pedidos['receita'];
} catch(PDOException $e) {
    $total_pedidos = 0;
    $receita_total = 0;
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

try {
    // Últimos pedidos
    $query_ultimos_pedidos = "
        SELECT p.id, p.data_pedido, p.total, p.status, u.nome as cliente_nome 
        FROM pedidos p 
        LEFT JOIN usuarios u ON p.usuario_id = u.id 
        ORDER BY p.data_pedido DESC LIMIT 5
    ";
    $stmt_ultimos_pedidos = $db->prepare($query_ultimos_pedidos);
    $stmt_ultimos_pedidos->execute();
    $ultimos_pedidos = $stmt_ultimos_pedidos->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $ultimos_pedidos = [];
}

// Processar geração de comprovante
if (isset($_GET['gerar_comprovante'])) {
    $pedido_id = $_GET['gerar_comprovante'];
    $receiptGenerator = new ReceiptGenerator();
    $filename = $receiptGenerator->generateReceipt($pedido_id, $db);
    
    // Mensagem de sucesso
    $_SESSION['mensagem_sucesso'] = "Comprovante gerado com sucesso!";
    header("Location: pedidos.php");
    exit;
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <h1>Dashboard Administrativo</h1>
        <p>Bem-vindo ao painel de controle da LAVELLE PERFUMES</p>
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
                <div class="stat-number"><?php echo $total_pedidos; ?></div>
                <div class="stat-label">Total de Pedidos</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-info">
                <div class="stat-number">R$ <?php echo number_format($receita_total, 2, ',', '.'); ?></div>
                <div class="stat-label">Receita Total</div>
            </div>
        </div>
    </div>

    <div class="content-grid">
        <div class="content-card">
            <div class="card-header">
                <h2>Últimos Pedidos</h2>
                <a href="pedidos.php" class="btn-link">Ver Todos</a>
            </div>
            <div class="table-responsive">
                <?php if(!empty($ultimos_pedidos)): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Data</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($ultimos_pedidos as $pedido): ?>
                            <tr>
                                <td>#<?php echo str_pad($pedido['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo htmlspecialchars($pedido['cliente_nome']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($pedido['data_pedido'])); ?></td>
                                <td>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $pedido['status']; ?>">
                                        <?php echo ucfirst($pedido['status']); ?>
                                    </span>
                                </td>
                                <td>
                                  
                                    <a href="?gerar_comprovante=<?php echo $pedido['id']; ?>" class="btn-small btn-success">Comprovante</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 2rem;">
                        Nenhum pedido encontrado.
                    </p>
                <?php endif; ?>
            </div>
        </div>

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
                        <img src="../img/novouser.png" alt="Novo Usuário" class="action-icon-img">
                    </div>
                    <span>Novo Usuário</span>
                </a>
                <a href="produtos.php?action=create" class="quick-action-btn">
                    <div class="action-icon">
                        <img src="../img/novo.png" alt="Novo Produto" class="action-icon-img">
                    </div>
                    <span>Novo Produto</span>
                </a>
                <a href="pedidos.php" class="quick-action-btn">
                    <div class="action-icon">
                        <img src="../img/iconmais.png" alt="Gerenciar Pedidos" class="action-icon-img">
                    </div>
                    <span>Gerenciar Pedidos</span>
                </a>
                <a href="produtos.php" class="quick-action-btn">
                    <div class="action-icon">
                        <img src="../img/gerenciarprodutos.png" alt="Gerenciar Produtos" class="action-icon-img">
                    </div>
                    <span>Gerenciar Produtos</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>