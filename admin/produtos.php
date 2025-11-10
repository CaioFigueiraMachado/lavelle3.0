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

// Verificar se as colunas de notas existem
$colunas_existem = true;
try {
    $stmt = $db->query("SHOW COLUMNS FROM produtos LIKE 'notas_saida'");
    $colunas_existem = $stmt->rowCount() > 0;
} catch(PDOException $e) {
    $colunas_existem = false;
}

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
        $notas_saida = $_POST['notas_saida'] ?? '';
        $notas_coracao = $_POST['notas_coracao'] ?? '';
        $notas_fundo = $_POST['notas_fundo'] ?? '';
        
        try {
            if ($colunas_existem) {
                $query = "INSERT INTO produtos (nome, descricao_breve, descricao_longa, preco, imagem, categoria, notas_saida, notas_coracao, notas_fundo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$nome, $descricao_breve, $descricao_longa, $preco, $imagem, $categoria, $notas_saida, $notas_coracao, $notas_fundo]);
            } else {
                $query = "INSERT INTO produtos (nome, descricao_breve, descricao_longa, preco, imagem, categoria) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$nome, $descricao_breve, $descricao_longa, $preco, $imagem, $categoria]);
            }
            
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
        $notas_saida = $_POST['notas_saida'] ?? '';
        $notas_coracao = $_POST['notas_coracao'] ?? '';
        $notas_fundo = $_POST['notas_fundo'] ?? '';
        
        try {
            if ($colunas_existem) {
                $query = "UPDATE produtos SET nome = ?, descricao_breve = ?, descricao_longa = ?, preco = ?, imagem = ?, categoria = ?, notas_saida = ?, notas_coracao = ?, notas_fundo = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$nome, $descricao_breve, $descricao_longa, $preco, $imagem, $categoria, $notas_saida, $notas_coracao, $notas_fundo, $id]);
            } else {
                $query = "UPDATE produtos SET nome = ?, descricao_breve = ?, descricao_longa = ?, preco = ?, imagem = ?, categoria = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$nome, $descricao_breve, $descricao_longa, $preco, $imagem, $categoria, $id]);
            }
            
            $mensagem = "Produto atualizado com sucesso!";
            $tipo_mensagem = "success";
        } catch(PDOException $e) {
            $mensagem = "Erro ao atualizar produto: " . $e->getMessage();
            $tipo_mensagem = "error";
        }
    } elseif($action === 'delete') {
        // Excluir produto
        $id = $_POST['id'];
        
        try {
            $query = "DELETE FROM produtos WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$id]);
            
            $mensagem = "Produto excluído com sucesso!";
            $tipo_mensagem = "success";
        } catch(PDOException $e) {
            $mensagem = "Erro ao excluir produto: " . $e->getMessage();
            $tipo_mensagem = "error";
        }
    }
}

// Buscar produtos com filtro de pesquisa
$search = $_GET['search'] ?? '';
$categoria_filter = $_GET['categoria'] ?? '';

try {
    $query = "SELECT id, nome, descricao_breve, descricao_longa, preco, imagem, categoria, created_at";
    if ($colunas_existem) {
        $query .= ", notas_saida, notas_coracao, notas_fundo";
    }
    $query .= " FROM produtos WHERE 1=1";
    
    $params = array();
    
    // Aplicar filtro de pesquisa
    if (!empty($search)) {
        $query .= " AND (nome LIKE ? OR descricao_breve LIKE ? OR descricao_longa LIKE ?";
        if ($colunas_existem) {
            $query .= " OR notas_saida LIKE ? OR notas_coracao LIKE ? OR notas_fundo LIKE ?";
        }
        $query .= ")";
        
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        if ($colunas_existem) {
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
    }
    
    // Aplicar filtro de categoria
    if (!empty($categoria_filter)) {
        $query .= " AND categoria = ?";
        $params[] = $categoria_filter;
    }
    
    $query .= " ORDER BY created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $mensagem = "Erro ao buscar produtos: " . $e->getMessage();
    $tipo_mensagem = "error";
    $produtos = array();
}

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

    <?php if(!$colunas_existem): ?>
        <div class="alert warning">
            <strong>Atenção:</strong> As colunas para notas dos perfumes não foram encontradas no banco de dados. 
            <a href="criar_colunas_notas.php" style="color: #8b7355; text-decoration: underline;">Clique aqui para criar as colunas automaticamente.</a>
        </div>
    <?php endif; ?>

    <!-- Formulário de Pesquisa -->
    <div class="content-card">
        <h3>Pesquisar Perfumes</h3>
        <form method="GET" class="search-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="search">Pesquisar:</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           class="form-input" placeholder="Pesquisar por nome, descrição ou notas...">
                </div>
                
                <div class="form-group">
                    <label for="categoria">Filtrar por Categoria:</label>
                    <select id="categoria_filter" name="categoria" class="form-input">
                        <option value="">Todas as categorias</option>
                        <option value="Feminino" <?php echo $categoria_filter === 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
                        <option value="Masculino" <?php echo $categoria_filter === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="Compartilhável" <?php echo $categoria_filter === 'Compartilhável' ? 'selected' : ''; ?>>Compartilhável</option>
                    </select>
                </div>
                
                <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" class="btn-primary">Pesquisar</button>
                    <?php if(!empty($search) || !empty($categoria_filter)): ?>
                        <a href="produtos.php" class="btn-outline" style="margin-left: 10px;">Limpar</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
        
        <?php if(!empty($search) || !empty($categoria_filter)): ?>
            <div class="search-results-info">
                <p>
                    <?php echo count($produtos); ?> produto(s) encontrado(s)
                    <?php if(!empty($search)): ?>
                        para "<?php echo htmlspecialchars($search); ?>"
                    <?php endif; ?>
                    <?php if(!empty($categoria_filter)): ?>
                        na categoria <?php echo $categoria_filter; ?>
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
    </div>

    <br><br><div class="content-card">
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
                    <?php if(empty($produtos)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 20px;">
                                <?php if(!empty($search) || !empty($categoria_filter)): ?>
                                    Nenhum produto encontrado com os critérios de pesquisa.
                                <?php else: ?>
                                    Nenhum produto cadastrado.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($produtos as $produto): ?>
                        <tr>
                            <td><?php echo $produto['id']; ?></td>
                            <td>
                                <?php if($produto['imagem']): ?>
                                    <img src="../<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>" class="product-thumb">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                            <td><?php echo htmlspecialchars($produto['descricao_breve']); ?></td>
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
                                    '<?php echo htmlspecialchars($produto['descricao_breve']); ?>', 
                                    '<?php echo htmlspecialchars($produto['descricao_longa']); ?>', 
                                    '<?php echo $produto['preco']; ?>', 
                                    '<?php echo $produto['imagem']; ?>',
                                    '<?php echo $produto['categoria']; ?>',
                                    '<?php echo isset($produto['notas_saida']) ? htmlspecialchars($produto['notas_saida']) : ''; ?>',
                                    '<?php echo isset($produto['notas_coracao']) ? htmlspecialchars($produto['notas_coracao']) : ''; ?>',
                                    '<?php echo isset($produto['notas_fundo']) ? htmlspecialchars($produto['notas_fundo']) : ''; ?>'
                                )">Editar</button>
                                <button class="btn-delete" onclick="deleteProduto(<?php echo $produto['id']; ?>)">Excluir</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
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

            <!-- Campos para as notas do perfume -->
            <?php if($colunas_existem): ?>
            <div class="form-group">
                <label for="notas_saida">Notas de Saída:</label>
                <input type="text" id="notas_saida" name="notas_saida" class="form-input" placeholder="Ex: Bergamota, Laranja, Limão Siciliano">
                <small style="color: #666; font-size: 12px;">Notas que são percebidas imediatamente após a aplicação</small>
            </div>

            <div class="form-group">
                <label for="notas_coracao">Notas de Coração:</label>
                <input type="text" id="notas_coracao" name="notas_coracao" class="form-input" placeholder="Ex: Jasmim, Rosa, Lírio do Vale">
                <small style="color: #666; font-size: 12px;">Notas que aparecem após as notas de saída evaporarem</small>
            </div>

            <div class="form-group">
                <label for="notas_fundo">Notas de Fundo:</label>
                <input type="text" id="notas_fundo" name="notas_fundo" class="form-input" placeholder="Ex: Baunilha, Âmbar, Musk, Sândalo">
                <small style="color: #666; font-size: 12px;">Notas que permanecem na pele por mais tempo</small>
            </div>
            <?php endif; ?>

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

function editProduto(id, nome, descricaoBreve, descricaoLonga, preco, imagem, categoria, notasSaida, notasCoracao, notasFundo) {
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
    
    // Só preenche os campos de notas se eles existirem
    const notasSaidaField = document.getElementById('notas_saida');
    const notasCoracaoField = document.getElementById('notas_coracao');
    const notasFundoField = document.getElementById('notas_fundo');
    
    if (notasSaidaField) notasSaidaField.value = notasSaida;
    if (notasCoracaoField) notasCoracaoField.value = notasCoracao;
    if (notasFundoField) notasFundoField.value = notasFundo;
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
    
    counter.textContent = currentLength + '/' + maxLength + ' caracteres';
    
    if (currentLength > maxLength) {
        this.value = this.value.substring(0, maxLength);
        counter.textContent = maxLength + '/' + maxLength + ' caracteres';
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
    for(let i = 0; i < modals.length; i++) {
        if(event.target == modals[i]) {
            modals[i].style.display = 'none';
        }
    }
}
</script>