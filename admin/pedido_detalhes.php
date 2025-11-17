<?php
// admin/pedido_detalhes.php - VERSÃO CORRIGIDA
session_start();
if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

include 'config/database.php';
include '../receipt_generator.php';

$database = new Database();
$db = $database->getConnection();

// Verificar se o ID do pedido foi passado
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: pedidos.php");
    exit;
}

$pedido_id = $_GET['id'];

// Buscar dados completos do pedido
try {
    // Dados básicos do pedido
    $stmt = $db->prepare("
        SELECT p.*, u.nome as cliente_nome, u.email, u.telefone, u.endereco as endereco_cliente
        FROM pedidos p 
        LEFT JOIN usuarios u ON p.usuario_id = u.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$pedido_id]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if(!$pedido) {
        $_SESSION['mensagem_erro'] = "Pedido não encontrado.";
        header("Location: pedidos.php");
        exit;
    }
    
    // Buscar itens do pedido
    $stmt = $db->prepare("
        SELECT pi.*, pr.nome as produto_nome, pr.imagem as produto_imagem
        FROM pedido_itens pi 
        LEFT JOIN produtos pr ON pi.produto_id = pr.id 
        WHERE pi.pedido_id = ?
    ");
    $stmt->execute([$pedido_id]);
    $itens_pedido = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar histórico do pedido (se a tabela existir)
    $historico = [];
    try {
        $stmt = $db->prepare("
            SELECT * FROM pedido_historico 
            WHERE pedido_id = ? 
            ORDER BY data_alteracao DESC
        ");
        $stmt->execute([$pedido_id]);
        $historico = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Tabela de histórico não existe, ignorar erro
        $historico = [];
    }
    
} catch(PDOException $e) {
    $_SESSION['mensagem_erro'] = "Erro ao carregar dados do pedido: " . $e->getMessage();
    header("Location: pedidos.php");
    exit;
}

// Processar alteração de status
if(isset($_POST['alterar_status'])) {
    $novo_status = $_POST['novo_status'];
    $observacao = $_POST['observacao'] ?? '';
    
    try {
        // Atualizar status do pedido
        $stmt = $db->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
        if($stmt->execute([$novo_status, $pedido_id])) {
            $_SESSION['mensagem_sucesso'] = "Status do pedido atualizado para: " . ucfirst($novo_status);
            
            // Tentar registrar histórico se houver observação E a tabela existir
            if(!empty($observacao)) {
                try {
                    $stmt = $db->prepare("
                        INSERT INTO pedido_historico (pedido_id, status, observacao, usuario_id) 
                        VALUES (?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $pedido_id, 
                        $novo_status, 
                        $observacao,
                        $_SESSION['id'] // ID do admin que fez a alteração
                    ]);
                } catch (PDOException $e) {
                    // Se der erro na inserção do histórico, apenas logar e continuar
                    error_log("Erro ao salvar histórico: " . $e->getMessage());
                }
            }
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao atualizar status do pedido.";
        }
    } catch(PDOException $e) {
        $_SESSION['mensagem_erro'] = "Erro ao atualizar status: " . $e->getMessage();
    }
    
    header("Location: pedido_detalhes.php?id=" . $pedido_id);
    exit;
}

// Processar geração de comprovante
if(isset($_POST['gerar_comprovante'])) {
    try {
        $receiptGenerator = new ReceiptGenerator();
        $filename = $receiptGenerator->generateReceipt($pedido_id, $db);
        
        $_SESSION['mensagem_sucesso'] = "Comprovante gerado com sucesso!";
        
        // Redirecionar para o comprovante
        header("Location: ../comprovantes/{$filename}");
        exit;
        
    } catch(Exception $e) {
        $_SESSION['mensagem_erro'] = "Erro ao gerar comprovante: " . $e->getMessage();
        header("Location: pedido_detalhes.php?id=" . $pedido_id);
        exit;
    }
}

// Função para verificar se comprovante existe
function comprovanteExiste($pedido_id) {
    $possible_files = [
        "../comprovantes/comprovante_pedido_{$pedido_id}.php",
        "../comprovantes/comprovante_pedido_{$pedido_id}.html",
        "comprovantes/comprovante_pedido_{$pedido_id}.php",
        "comprovantes/comprovante_pedido_{$pedido_id}.html"
    ];
    
    foreach ($possible_files as $file) {
        if (file_exists($file)) {
            return $file;
        }
    }
    return false;
}

// Verificar se comprovante já existe
$comprovante_path = comprovanteExiste($pedido_id);
$comprovante_existe = ($comprovante_path !== false);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <div class="header-left">
            <h1>Detalhes do Pedido</h1>
            <p>Pedido #<?php echo str_pad($pedido_id, 3, '0', STR_PAD_LEFT); ?></p>
        </div>
        <div class="header-actions">
            <a href="pedidos.php" class="btn-outline">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <a href="?id=<?php echo $pedido_id; ?>" class="btn-primary">
                <i class="fas fa-sync"></i> Atualizar
            </a>
        </div>
    </div>

    <?php if(isset($_SESSION['mensagem_sucesso'])): ?>
        <div class="alert success">
            <?php echo $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['mensagem_erro'])): ?>
        <div class="alert error">
            <?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?>
        </div>
    <?php endif; ?>

    <div class="details-grid">
        <!-- Informações do Pedido -->
        <div class="details-card">
            <div class="card-header">
                <h2>Informações do Pedido</h2>
                <span class="status-badge status-<?php echo $pedido['status']; ?>">
                    <?php echo ucfirst($pedido['status']); ?>
                </span>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Número do Pedido:</label>
                        <span>#<?php echo str_pad($pedido_id, 3, '0', STR_PAD_LEFT); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Data do Pedido:</label>
                        <span><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Status:</label>
                        <span class="status-text status-<?php echo $pedido['status']; ?>">
                            <?php echo ucfirst($pedido['status']); ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <label>Método de Pagamento:</label>
                        <span><?php echo ucfirst($pedido['metodo_pagamento']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Valor Total:</label>
                        <span class="price">R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações do Cliente -->
        <div class="details-card">
            <div class="card-header">
                <h2>Informações do Cliente</h2>
            </div>
            <div class="card-body">
                <div class="info-grid">
                    <div class="info-item">
                        <label>Nome:</label>
                        <span><?php echo htmlspecialchars($pedido['cliente_nome']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Email:</label>
                        <span><?php echo htmlspecialchars($pedido['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Telefone:</label>
                        <span><?php echo $pedido['telefone'] ? htmlspecialchars($pedido['telefone']) : 'Não informado'; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Endereço de Entrega -->
        <div class="details-card full-width">
            <div class="card-header">
                <h2>Endereço de Entrega</h2>
            </div>
            <div class="card-body">
                <div class="address-box">
                    <?php echo nl2br(htmlspecialchars($pedido['endereco_cliente'])); ?>
                </div>
            </div>
        </div>

        <!-- Itens do Pedido -->
        <div class="details-card full-width">
            <div class="card-header">
                <h2>Itens do Pedido</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Quantidade</th>
                                <th>Preço Unitário</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($itens_pedido as $item): ?>
                            <tr>
                                <td>
                                    <div class="product-info">
                                        <div class="product-name">
                                            <?php echo htmlspecialchars($item['produto_nome']); ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?php echo $item['quantidade']; ?></td>
                                <td>R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                                <td class="price">R$ <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total do Pedido:</strong></td>
                                <td class="price total"><strong>R$ <?php echo number_format($pedido['total'], 2, ',', '.'); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Histórico do Pedido (se existir) -->
        <?php if(!empty($historico)): ?>
        <div class="details-card full-width">
            <div class="card-header">
                <h2>Histórico do Pedido</h2>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <?php foreach($historico as $evento): ?>
                    <div class="timeline-item">
                        <div class="timeline-marker"></div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="status-badge status-<?php echo $evento['status']; ?>">
                                    <?php echo ucfirst($evento['status']); ?>
                                </span>
                                <span class="timeline-date">
                                    <?php echo date('d/m/Y H:i', strtotime($evento['data_alteracao'])); ?>
                                </span>
                            </div>
                            <?php if(!empty($evento['observacao'])): ?>
                                <div class="timeline-observacao">
                                    <?php echo htmlspecialchars($evento['observacao']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Ações do Pedido -->
        <div class="details-card full-width">
            <div class="card-header">
                <h2>Ações do Pedido</h2>
            </div>
            <div class="card-body">
                <div class="actions-grid">
                    <!-- Alterar Status -->
                    <form method="POST" class="action-form">
                        <div class="form-group">
                            <label for="novo_status">Alterar Status:</label>
                            <select name="novo_status" id="novo_status" class="status-select" required>
                                <option value="pendente" <?php echo $pedido['status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                <option value="confirmado" <?php echo $pedido['status'] == 'confirmado' ? 'selected' : ''; ?>>Confirmado</option>
                                <option value="enviado" <?php echo $pedido['status'] == 'enviado' ? 'selected' : ''; ?>>Enviado</option>
                                <option value="entregue" <?php echo $pedido['status'] == 'entregue' ? 'selected' : ''; ?>>Entregue</option>
                                <option value="cancelado" <?php echo $pedido['status'] == 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="observacao">Observação (opcional):</label>
                            <textarea name="observacao" id="observacao" rows="2" placeholder="Observações sobre a alteração de status..."></textarea>
                            <small class="form-help">
                                A observação será salva no histórico do pedido.
                            </small>
                        </div>
                        <button type="submit" name="alterar_status" class="btn-primary">
                            <i class="fas fa-sync"></i> Atualizar Status
                        </button>
                    </form>

                    <!-- Gerar/Abrir Comprovante -->
                    <div class="action-form">
                        <div class="form-group">
                            <label>Comprovante:</label>
                            <?php if($comprovante_existe): ?>
                                <p class="form-help success">Comprovante já gerado. Clique para abrir.</p>
                                <a href="<?php echo $comprovante_path; ?>" 
                                   class="btn-success"
                                   target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Abrir Comprovante
                                </a>
                            <?php else: ?>
                                <p class="form-help">Gere um comprovante em PDF do pedido.</p>
                                <form method="POST" style="display: inline;">
                                    <button type="submit" name="gerar_comprovante" class="btn-primary">
                                        <i class="fas fa-file-pdf"></i> Gerar Comprovante
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Excluir Pedido -->
                    <div class="action-form">
                        <div class="form-group">
                            <label>Excluir Pedido:</label>
                            <p class="form-help danger">Esta ação não pode ser desfeita.</p>
                        </div>
                        <a href="pedidos.php?excluir_pedido=<?php echo $pedido_id; ?>" 
                           class="btn-danger"
                           onclick="return confirm('Tem certeza que deseja excluir este pedido? Esta ação é irreversível!')">
                            <i class="fas fa-trash"></i> Excluir Pedido
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ... (todo o CSS anterior permanece igual) ... */

/* Estilos para o histórico/timeline */
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.timeline-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #8b7355;
    border: 2px solid white;
    box-shadow: 0 0 0 2px #8b7355;
}
.btn-danger{
     text-decoration: none;
}
.timeline-content {
    background: white;
}

.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.timeline-date {
    color: #666;
    font-size: 12px;
}

.timeline-observacao {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    border-left: 3px solid #8b7355;
    font-size: 14px;
    line-height: 1.4;
}

.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 30px;
}

.details-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.details-card.full-width {
    grid-column: 1 / -1;
}

.card-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h2 {
    margin: 0;
    font-size: 18px;
    color: #333;
}

.card-body {
    padding: 20px;
}

.info-grid {
    display: grid;
    gap: 15px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f8f9fa;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item label {
    font-weight: 600;
    color: #666;
    min-width: 150px;
}

.info-item span {
    color: #333;
}

.price {
    color: #28a745;
    font-weight: 600;
}

.price.total {
    font-size: 1.1em;
    color: #155724;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-text {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 600;
}

.status-pendente { background: #fff3cd; color: #856404; }
.status-confirmado { background: #d1ecf1; color: #0c5460; }
.status-enviado { background: #d4edda; color: #155724; }
.status-entregue { background: #e2e3e5; color: #383d41; }
.status-cancelado { background: #f8d7da; color: #721c24; }

.address-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    border-left: 4px solid #8b7355;
    line-height: 1.6;
}

.product-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.product-name {
    font-weight: 500;
}

.actions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.action-form {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
    color: #333;
}

.form-help {
    font-size: 12px;
    color: #666;
    margin: 5px 0 0 0;
}

.form-help.success {
    color: #28a745;
}

.form-help.danger {
    color: #dc3545;
}

.status-select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    resize: vertical;
    min-height: 60px;
}

.text-right {
    text-align: right;
}

.header-left h1 {
    margin: 0 0 5px 0;
}

.header-left p {
    margin: 0;
    color: #666;
}

.header-actions {
    display: flex;
    gap: 10px;
}

@media (max-width: 768px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .header-actions {
        flex-direction: column;
    }
}
.btn-success{
    text-decoration: none;
}
</style>

<script>
// Confirmação antes de alterar status
document.addEventListener('DOMContentLoaded', function() {
    const statusForm = document.querySelector('form[method="POST"]');
    
    if(statusForm) {
        statusForm.addEventListener('submit', function(e) {
            const novoStatus = document.getElementById('novo_status').value;
            const statusAtual = '<?php echo $pedido['status']; ?>';
            
            if(novoStatus === statusAtual) {
                e.preventDefault();
                alert('O status selecionado é o mesmo do atual. Por favor, selecione um status diferente.');
                return false;
            }
            
            if(!confirm(`Deseja realmente alterar o status do pedido de "${statusAtual}" para "${novoStatus}"?`)) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>