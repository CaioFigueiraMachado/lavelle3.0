<?php
session_start();
if(isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit;
}

if($_POST) {
    // Credenciais simples - você pode implementar verificação no banco
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if($username == 'admin' && $password == 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_name'] = 'Administrador';
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Credenciais inválidas!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PaineI Administrative</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-header">
            <h1>PaineI Administrative</h1>
            <p>BEM-WIND, ADM</p>
        </div>
        
        <?php if(isset($error)): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" class="login-form">
            <div class="form-group">
                <input type="text" name="username" placeholder="Usuário" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" placeholder="Senha" required>
            </div>
            <button type="submit" class="btn-login">Entrar</button>
        </form>
    </div>
</body>
</html>