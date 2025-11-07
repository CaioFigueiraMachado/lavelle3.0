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
        // Criar usuário
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        
        if(empty($nome) || empty($email) || empty($senha)) {
            $mensagem = "Nome, email e senha são obrigatórios!";
            $tipo_mensagem = "error";
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
                }
            } catch(PDOException $e) {
                $mensagem = "Erro ao criar usuário: " . $e->getMessage();
                $tipo_mensagem = "error";
            }
        }
    }
    elseif($action === 'update') {
        // Atualizar usuário
        $id = $_POST['id'] ?? '';
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';
        
        if(empty($id) || empty($nome) || empty($email)) {
            $mensagem = "ID, nome e email são obrigatórios!";
            $tipo_mensagem = "error";
        } else {
            try {
                // Verificar se email já existe em outro usuário
                $checkQuery = "SELECT id FROM usuarios WHERE email = ? AND id != ?";
                $checkStmt = $db->prepare($checkQuery);
                $checkStmt->execute([$email, $id]);
                
                if($checkStmt->fetch()) {
                    $mensagem = "Este email já está cadastrado em outro usuário!";
                    $tipo_mensagem = "error";
                } else {
                    if(!empty($senha)) {
                        // Atualizar com senha
                        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                        $query = "UPDATE usuarios SET nome = ?, email = ?, senha = ? WHERE id = ?";
                        $stmt = $db->prepare($query);
                        $stmt->execute([$nome, $email, $senha_hash, $id]);
                    } else {
                        // Atualizar sem alterar senha
                        $query = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
                        $stmt = $db->prepare($query);
                        $stmt->execute([$nome, $email, $id]);
                    }
                    
                    $mensagem = "Usuário atualizado com sucesso!";
                    $tipo_mensagem = "success";
                }
            } catch(PDOException $e) {
                $mensagem = "Erro ao atualizar usuário: " . $e->getMessage();
                $tipo_mensagem = "error";
            }
        }
    }
    elseif($action === 'delete') {
        // Excluir usuário
        $id = $_POST['id'] ?? '';
        
        if(empty($id)) {
            $mensagem = "ID do usuário é obrigatório!";
            $tipo_mensagem = "error";
        } else {
            try {
                // Verificar se é o próprio usuário
                if($id == $_SESSION['id']) {
                    $mensagem = "Você não pode excluir seu próprio usuário!";
                    $tipo_mensagem = "error";
                } else {
                    $query = "DELETE FROM usuarios WHERE id = ?";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$id]);
                    
                    if($stmt->rowCount() > 0) {
                        $mensagem = "Usuário excluído com sucesso!";
                        $tipo_mensagem = "success";
                    } else {
                        $mensagem = "Usuário não encontrado!";
                        $tipo_mensagem = "error";
                    }
                }
            } catch(PDOException $e) {
                $mensagem = "Erro ao excluir usuário: " . $e->getMessage();
                $tipo_mensagem = "error";
            }
        }
    }
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

<!-- Modal Usuário -->
<div id="usuarioModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Novo Usuário</h2>
        
        <form id="usuarioForm" method="POST" action="usuarios.php">
            <input type="hidden" name="action" id="formAction" value="create">
            <input type="hidden" name="id" id="userId">
            
            <div class="form-group">
                <label for="nome">Nome:*</label>
                <input type="text" id="nome" name="nome" required class="form-input" placeholder="Digite o nome completo">
            </div>

            <div class="form-group">
                <label for="email">Email:*</label>
                <input type="email" id="email" name="email" required class="form-input" placeholder="Digite o email">
            </div>

            <div class="form-group">
                <label for="senha">Senha:<?php echo '<span id="senhaObrigatoria">*</span><span id="senhaOpcional" style="display:none"> (deixe em branco para manter a atual)</span>'; ?></label>
                <input type="password" id="senha" name="senha" class="form-input" placeholder="Digite a senha">
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
        <form id="deleteForm" method="POST" action="usuarios.php">
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
// Funções JavaScript
function openModal(action) {
    const modal = document.getElementById('usuarioModal');
    const title = document.getElementById('modalTitle');
    const formAction = document.getElementById('formAction');
    const senhaObrigatoria = document.getElementById('senhaObrigatoria');
    const senhaOpcional = document.getElementById('senhaOpcional');
    const senhaInput = document.getElementById('senha');
    
    if(action === 'create') {
        title.textContent = 'Novo Usuário';
        formAction.value = 'create';
        document.getElementById('userId').value = '';
        document.getElementById('usuarioForm').reset();
        senhaObrigatoria.style.display = 'inline';
        senhaOpcional.style.display = 'none';
        senhaInput.required = true;
    }
    
    modal.style.display = 'block';
}

function closeModal() {
    document.getElementById('usuarioModal').style.display = 'none';
}

function editUsuario(id, nome, email) {
    const modal = document.getElementById('usuarioModal');
    const title = document.getElementById('modalTitle');
    const formAction = document.getElementById('formAction');
    const userId = document.getElementById('userId');
    const nomeInput = document.getElementById('nome');
    const emailInput = document.getElementById('email');
    const senhaObrigatoria = document.getElementById('senhaObrigatoria');
    const senhaOpcional = document.getElementById('senhaOpcional');
    const senhaInput = document.getElementById('senha');
    
    title.textContent = 'Editar Usuário';
    formAction.value = 'update';
    userId.value = id;
    nomeInput.value = nome;
    emailInput.value = email;
    senhaInput.value = '';
    senhaObrigatoria.style.display = 'none';
    senhaOpcional.style.display = 'inline';
    senhaInput.required = false;
    
    modal.style.display = 'block';
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