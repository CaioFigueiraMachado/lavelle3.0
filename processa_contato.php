<?php
session_start();
include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificar se usuário está logado
    $usuario_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;
    
    $nome = trim($_POST['name']);
    $email = trim($_POST['email']);
    $assunto = trim($_POST['subject']);
    $mensagem = trim($_POST['message']);
    $telefone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    
    // Validações
    if (empty($nome) || empty($email) || empty($assunto) || empty($mensagem)) {
        $_SESSION['contato_erro'] = "Todos os campos são obrigatórios.";
        header('Location: contato.php');
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['contato_erro'] = "E-mail inválido.";
        header('Location: contato.php');
        exit();
    }
    
    try {
        // Verificar se as tabelas existem, se não existir, criar
        $check_table = $con->query("SHOW TABLES LIKE 'contato_mensagens'");
        if ($check_table->rowCount() == 0) {
            // Criar tabela contato_mensagens
            $con->exec("CREATE TABLE contato_mensagens (
                id INT PRIMARY KEY AUTO_INCREMENT,
                usuario_id INT,
                assunto VARCHAR(255) NOT NULL,
                mensagem TEXT NOT NULL,
                status ENUM('aberta', 'respondida', 'fechada') DEFAULT 'aberta',
                prioridade ENUM('baixa', 'media', 'alta') DEFAULT 'media',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )");
        }
        
        $check_table2 = $con->query("SHOW TABLES LIKE 'contato_respostas'");
        if ($check_table2->rowCount() == 0) {
            // Criar tabela contato_respostas
            $con->exec("CREATE TABLE contato_respostas (
                id INT PRIMARY KEY AUTO_INCREMENT,
                mensagem_id INT,
                remetente ENUM('cliente', 'admin') NOT NULL,
                mensagem TEXT NOT NULL,
                lida BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
        }
        
        // Inserir mensagem no banco
        $sql = "INSERT INTO contato_mensagens (usuario_id, assunto, mensagem, status) VALUES (?, ?, ?, 'aberta')";
        $stmt = $con->prepare($sql);
        $stmt->execute([$usuario_id, $assunto, $mensagem]);
        
        $mensagem_id = $con->lastInsertId();
        
        // Se usuário não está logado, salvar também nome, email e telefone na primeira resposta
        if (!$usuario_id) {
            $info_cliente = "Cliente: $nome\nE-mail: $email\nTelefone: " . ($telefone ?: 'Não informado');
            $mensagem_completa = $info_cliente . "\n\nMensagem: " . $mensagem;
            
            $sql_resposta = "INSERT INTO contato_respostas (mensagem_id, remetente, mensagem) VALUES (?, 'cliente', ?)";
            $stmt_resposta = $con->prepare($sql_resposta);
            $stmt_resposta->execute([$mensagem_id, $mensagem_completa]);
        } else {
            // Se usuário está logado, salvar apenas a mensagem
            $sql_resposta = "INSERT INTO contato_respostas (mensagem_id, remetente, mensagem) VALUES (?, 'cliente', ?)";
            $stmt_resposta = $con->prepare($sql_resposta);
            $stmt_resposta->execute([$mensagem_id, $mensagem]);
            
            // Atualizar informações do usuário se for diferente
            $sql_update_user = "UPDATE usuarios SET telefone = ? WHERE id = ? AND (telefone IS NULL OR telefone = '')";
            $stmt_update = $con->prepare($sql_update_user);
            $stmt_update->execute([$telefone, $usuario_id]);
        }
        
        $_SESSION['contato_sucesso'] = "Mensagem enviada com sucesso! Responderemos em breve.";
        header('Location: contato.php');
        exit();
        
    } catch (PDOException $e) {
        error_log("Erro ao processar contato: " . $e->getMessage());
        $_SESSION['contato_erro'] = "Erro ao enviar mensagem. Tente novamente. Erro: " . $e->getMessage();
        header('Location: contato.php');
        exit();
    }
} else {
    header('Location: contato.php');
    exit();
}
?>