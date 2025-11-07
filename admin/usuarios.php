<?php
// admin/usuarios.php
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

// DEBUG DETALHADO
error_log("=== DEBUG USUARIOS.PHP ===");
error_log("POST DATA: " . print_r($_POST, true));
error_log("GET DATA: " . print_r($_GET, true));

// Processar ações
if($_POST && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    error_log("ACTION: $action");
    
    if($action === 'create') {
        // DEBUG DETALHADO DOS CAMPOS
        error_log("CAMPO nome: '" . ($_POST['nome'] ?? 'NULL') . "'");
        error_log("CAMPO email: '" . ($_POST['email'] ?? 'NULL') . "'");
        error_log("CAMPO senha EXISTS: " . (isset($_POST['senha']) ? 'YES' : 'NO'));
        error_log("CAMPO senha VALUE: '" . ($_POST['senha'] ?? 'NULL') . "'");
        error_log("CAMPO senha LENGTH: " . (isset($_POST['senha']) ? strlen($_POST['senha']) : '0'));
        
        // Criar usuário
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        
        // DEBUG DA VALIDAÇÃO
        error_log("VALIDATION - nome empty: " . (empty($nome) ? 'YES' : 'NO'));
        error_log("VALIDATION - email empty: " . (empty($email) ? 'YES' : 'NO'));
        error_log("VALIDATION - senha empty: " . (empty($senha) ? 'YES' : 'NO'));
        
        if(empty($nome) || empty($email) || empty($senha)) {
            $mensagem = "Nome, email e senha são obrigatórios!";
            $tipo_mensagem = "error";
            error_log("VALIDATION FAILED");
        } else {
            try {
                // Verificar se email já existe
                $checkQuery = "SELECT id FROM usuarios WHERE email = ?";
                $checkStmt = $db->prepare($checkQuery);
                $checkStmt->execute([$email]);
                
                if($checkStmt->fetch()) {
                    $mensagem = "Este email já está cadastrado!";
                    $tipo_mensagem = "error";
                } else {
                    // Criar hash da senha
                    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                    
                    $query = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$nome, $email, $senha_hash]);
                    
                    $mensagem = "Usuário criado com sucesso!";
                    $tipo_mensagem = "success";
                    error_log("USER CREATED SUCCESSFULLY");
                }
            } catch(PDOException $e) {
                $mensagem = "Erro ao criar usuário: " . $e->getMessage();
                $tipo_mensagem = "error";
                error_log("ERROR: " . $e->getMessage());
            }
        }
    }
    // ... resto do código para update e delete
}

// Buscar todos os usuários
$usuarios = [];
try {
    $query = "SELECT id, nome, email, telefone, cidade, estado, created_at FROM usuarios ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $mensagem = "Erro ao carregar usuários: " . $e->getMessage();
    $tipo_mensagem = "error";
}

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<div class="main-content">
    <div class="page-header">
        <h1>Gerenciar Usuários</h1>
        <button class="btn-primary" onclick="openModal('create')">Novo Usuário</button>
    </div>

    <?php if($mensagem): ?>
        <div class="alert <?php echo $tipo_mensagem; ?>"><?php echo $mensagem; ?></div>
    <?php endif; ?>

    <div class="content-card">
        <?php if(empty($usuarios)): ?>
            <div style="text-align: center; padding: 3rem; color: #666;">
                <p>Nenhum usuário cadastrado.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                          
                            <th>Data Cadastro</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
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
                            <td class="actions">
                                <button class="btn-edit" onclick="editUsuario(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['nome']); ?>', '<?php echo htmlspecialchars($usuario['email']); ?>')">Editar</button>
                                <?php if($usuario['id'] != $_SESSION['id']): ?>
                                    <button class="btn-delete" onclick="deleteUsuario(<?php echo $usuario['id']; ?>)">Excluir</button>
                                <?php else: ?>
                                    <button class="btn-delete" disabled title="Você não pode excluir seu próprio usuário">Excluir</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Usuário - VERSÃO SIMPLIFICADA SEM JAVASCRIPT -->
<div id="usuarioModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Novo Usuário</h2>
        
        <!-- FORMULÁRIO SIMPLES SEM JAVASCRIPT -->
        <form id="usuarioForm" method="POST" action="usuarios.php">
            <input type="hidden" name="action" value="create">
            
            <div class="form-group">
                <label for="nome">Nome:*</label>
                <input type="text" id="nome" name="nome" required class="form-input" placeholder="Digite o nome completo">
            </div>

            <div class="form-group">
                <label for="email">Email:*</label>
                <input type="email" id="email" name="email" required class="form-input" placeholder="Digite o email">
            </div>

            <div class="form-group">
                <label for="senha">Senha:*</label>
                <input type="password" id="senha" name="senha" required class="form-input" placeholder="Digite a senha (mínimo 6 caracteres)">
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
        <p>Tem certeza que deseja excluir este usuário?</p>
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
// JavaScript MÍNIMO - apenas para abrir/fechar modais
function openModal(action) {
    document.getElementById('usuarioModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('usuarioModal').style.display = 'none';
}

function editUsuario(id, nome, email) {
    // Implementação básica para editar
    alert('Funcionalidade de editar será implementada após resolver o problema de criação');
}

function deleteUsuario(id) {
    document.getElementById('confirmModal').style.display = 'block';
    document.getElementById('deleteId').value = id;
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}

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