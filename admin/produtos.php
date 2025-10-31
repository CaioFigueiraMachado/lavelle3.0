<?php
// admin/produtos.php
session_start();
if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../index.php");
    exit;
}

include 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$mensagem = '';
$tipo_mensagem = '';

// Processar ações
if($_POST) {
    $action = $_POST['action'] ?? '';
    
    if($action === 'create') {
        // Criar produto
        $nome = $_POST['nome'];
        $descricao_breve = $_POST['descricao_breve'];
        $descricao_longa = $_POST['descricao_longa'];
        $preco = $_POST['preco'];
        $imagem = $_POST['imagem'];
        $categoria = $_POST['categoria'];
        
        try {
            $query = "INSERT INTO produtos (nome, descricao_breve, descricao_longa, preco, imagem, categoria) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $db->prepare($query);
            $stmt->execute([$nome, $descricao_breve, $descricao_longa, $preco, $imagem, $categoria]);
            
            $mensagem = "Produto criado com sucesso!";
            $tipo_mensagem = "success";
        } catch(PDOException $e) {
            $mensagem = "Erro ao criar produto: " . $e->getMessage();
            $tipo_mensagem = "error";
        }
    } elseif($action === 'update') {
        // Atualizar produto
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $descricao_breve = $_POST['descricao_breve'];
        $descricao_longa = $_POST['descricao_longa'];
        $preco = $_POST['preco'];
        $imagem = $_POST['imagem'];
        $categoria = $_POST['categoria'];
        
        $query = "UPDATE produtos SET nome = ?, descricao_breve = ?, descricao_longa = ?, preco = ?, imagem = ?, categoria = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$nome, $descricao_breve, $descricao_longa, $preco, $imagem, $categoria, $id]);
        
        $mensagem = "Produto atualizado com sucesso!";
        $tipo_mensagem = "success";
    } elseif($action === 'delete') {
        // Excluir produto
        $id = $_POST['id'];
        
        $query = "DELETE FROM produtos WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$id]);
        
        $mensagem = "Produto excluído com sucesso!";
        $tipo_mensagem = "success";
    }
}

// Buscar todos os produtos
$query = "SELECT id, nome, descricao_breve, descricao_longa, preco, imagem, categoria, created_at FROM produtos ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <h1>Gerenciar Produtos</h1>
        <button class="btn-primary" onclick="openModal('create')">Novo Produto</button>
    </div>

    <?php if($mensagem): ?>
        <div class="alert <?php echo $tipo_mensagem; ?>"><?php echo $mensagem; ?></div>
    <?php endif; ?>

    <div class="content-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagem</th>
                        <th>Nome</th>
                        <th>Descrição Breve</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Data Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($produtos as $produto): ?>
                    <tr>
                        <td><?php echo $produto['id']; ?></td>
                        <td>
                            <?php if($produto['imagem']): ?>
                                <img src="../<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>" class="product-thumb">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                        <td><?php echo htmlspecialchars($produto['descricao_breve'] ?? 'Sem descrição'); ?></td>
                        <td>
                            <span class="status-badge 
                                <?php 
                                if($produto['categoria'] == 'Feminino') echo 'status-active';
                                elseif($produto['categoria'] == 'Masculino') echo 'status-pending';
                                else echo 'status-inactive';
                                ?>">
                                <?php echo $produto['categoria']; ?>
                            </span>
                        </td>
                        <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($produto['created_at'])); ?></td>
                        <td class="actions">
                            <button class="btn-edit" onclick="editProduto(
                                <?php echo $produto['id']; ?>, 
                                '<?php echo htmlspecialchars($produto['nome']); ?>', 
                                '<?php echo htmlspecialchars($produto['descricao_breve'] ?? ''); ?>', 
                                '<?php echo htmlspecialchars($produto['descricao_longa'] ?? ''); ?>', 
                                '<?php echo $produto['preco']; ?>', 
                                '<?php echo $produto['imagem']; ?>',
                                '<?php echo $produto['categoria']; ?>'
                            )">Editar</button>
                            <button class="btn-delete" onclick="deleteProduto(<?php echo $produto['id']; ?>)">Excluir</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Produto -->
<div id="produtoModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Novo Produto</h2>
        <form id="produtoForm" method="POST">
            <input type="hidden" id="action" name="action" value="create">
            <input type="hidden" id="produtoId" name="id">
            
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required class="form-input" placeholder="Ex: Lavelle Exclusive">
            </div>

            <div class="form-group">
                <label for="descricao_breve">Descrição Breve (para o card):</label>
                <textarea id="descricao_breve" name="descricao_breve" required class="form-input" rows="2" maxlength="150" placeholder="Descrição curta que aparece no card do produto (máx. 150 caracteres)"></textarea>
                <small style="color: #666; font-size: 12px;">Máximo 150 caracteres. Esta descrição aparece no card do produto.</small>
            </div>

            <div class="form-group">
                <label for="descricao_longa">Descrição Longa (para detalhes):</label>
                <textarea id="descricao_longa" name="descricao_longa" required class="form-input" rows="5" placeholder="Descrição completa que aparece quando o cliente clica em 'Detalhes'"></textarea>
                <small style="color: #666; font-size: 12px;">Descrição completa que aparece na página de detalhes do produto.</small>
            </div>

            <div class="form-group">
                <label for="preco">Preço:</label>
                <input type="number" id="preco" name="preco" step="0.01" required class="form-input" placeholder="299.90">
            </div>

            <div class="form-group">
                <label for="categoria">Categoria:</label>
                <select id="categoria" name="categoria" required class="form-input">
                    <option value="Feminino">Feminino</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Compartilhável">Compartilhável</option>
                </select>
            </div>

            <div class="form-group">
                <label for="imagem">URL da Imagem:</label>
                <input type="text" id="imagem" name="imagem" class="form-input" placeholder="ex: imagens/meuproduto.jpg">
                <small style="color: #666;">Use imagens na pasta 'imagens/' ou URLs externas</small>
            </div>
            
            <div class="form-actions">
                <button type="button" class="btn-outline" onclick="closeModal()">Cancelar</button>
                <button type="submit" class="btn-primary">Salvar</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Confirmação Exclusão -->
<div id="confirmModal" class="modal">
    <div class="modal-content">
        <h2>Confirmar Exclusão</h2>
        <p>Tem certeza que deseja excluir este produto?</p>
        <form id="deleteForm" method="POST">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" id="deleteId" name="id">
            <div class="form-actions">
                <button type="button" class="btn-outline" onclick="closeConfirmModal()">Cancelar</button>
                <button type="submit" class="btn-delete">Excluir</button>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
function openModal(action) {
    document.getElementById('produtoModal').style.display = 'block';
    document.getElementById('action').value = action;
    
    if(action === 'create') {
        document.getElementById('modalTitle').textContent = 'Novo Produto';
        document.getElementById('produtoForm').reset();
        document.getElementById('categoria').value = 'Compartilhável';
    }
}

function closeModal() {
    document.getElementById('produtoModal').style.display = 'none';
}

function editProduto(id, nome, descricaoBreve, descricaoLonga, preco, imagem, categoria) {
    document.getElementById('produtoModal').style.display = 'block';
    document.getElementById('modalTitle').textContent = 'Editar Produto';
    document.getElementById('action').value = 'update';
    document.getElementById('produtoId').value = id;
    document.getElementById('nome').value = nome;
    document.getElementById('descricao_breve').value = descricaoBreve;
    document.getElementById('descricao_longa').value = descricaoLonga;
    document.getElementById('preco').value = preco;
    document.getElementById('imagem').value = imagem;
    document.getElementById('categoria').value = categoria;
}

function deleteProduto(id) {
    document.getElementById('confirmModal').style.display = 'block';
    document.getElementById('deleteId').value = id;
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}

// Contador de caracteres para descrição breve
document.getElementById('descricao_breve').addEventListener('input', function() {
    const maxLength = 150;
    const currentLength = this.value.length;
    const counter = document.getElementById('charCounter') || createCharCounter();
    
    counter.textContent = `${currentLength}/${maxLength} caracteres`;
    
    if (currentLength > maxLength) {
        this.value = this.value.substring(0, maxLength);
        counter.textContent = `${maxLength}/${maxLength} caracteres`;
        counter.style.color = '#e74c3c';
    } else if (currentLength > 130) {
        counter.style.color = '#e67e22';
    } else {
        counter.style.color = '#666';
    }
});

function createCharCounter() {
    const counter = document.createElement('small');
    counter.id = 'charCounter';
    counter.style.cssText = 'color: #666; font-size: 12px; display: block; margin-top: 5px;';
    document.getElementById('descricao_breve').parentNode.appendChild(counter);
    return counter;
}

// Inicializar contador
createCharCounter();

// Fechar modal ao clicar fora
window.onclick = function(event) {
    const modals = document.getElementsByClassName('modal');
    for(let modal of modals) {
        if(event.target == modal) {
            modal.style.display = 'none';
        }
    }
}
</script>