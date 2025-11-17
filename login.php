<?php
// login.php (Página de Login)

session_start();

// Incluir e verificar conexão
try {
    include('conexao.php');
    
    // Verificar se a conexão PDO foi estabelecida
    if (!isset($con) || !($con instanceof PDO)) {
        throw new Exception("Conexão com o banco de dados não estabelecida");
    }
} catch (Exception $e) {
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'login') {
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $senha = isset($_POST['senha']) ? $_POST['senha'] : '';

        if (strlen($email) == 0) {
            $login_error = "Preencha seu e-mail";
        } else if (strlen($senha) == 0) {
            $login_error = "Preencha sua senha";
        } else {
            try {
                // Buscar usuário usando PDO
                $sql = "SELECT id, nome, email, senha FROM usuarios WHERE email = ? LIMIT 1";
                $stmt = $con->prepare($sql);
                $stmt->execute([$email]);
                
                if ($stmt->rowCount() === 1) {
                    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                    $hash = $usuario['senha'];

                    $password_ok = false;
                    if (password_get_info($hash)['algo'] !== 0) {
                        if (password_verify($senha, $hash)) $password_ok = true;
                    } else {
                        if ($senha === $hash) $password_ok = true;
                    }

                    if ($password_ok) {
                        $_SESSION['id'] = $usuario['id'];
                        $_SESSION['nome'] = $usuario['nome'];
                        $_SESSION['email'] = $usuario['email'];
                        
                        // VERIFICAÇÃO DO ADMIN - ADICIONADO AQUI
                        if ($email === 'admlavelle@gmail.com') {
                            $_SESSION['is_admin'] = true;
                        } else {
                            $_SESSION['is_admin'] = false;
                        }
                        
                        header("Location: index.php"); // Redireciona para a página inicial após o login
                        exit();
                    } else {
                        $login_error = "Falha ao logar! E-mail ou senha incorretos";
                    }
                } else {
                    $login_error = "Falha ao logar! E-mail ou senha incorretos";
                }
            } catch (PDOException $e) {
                $login_error = "Erro no servidor. Tente novamente mais tarde.";
                // Para debug, você pode mostrar o erro completo:
                // $login_error = "Erro: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LAVELLE Perfumes</title>
    <style>
        /* Reset e estilos gerais */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f5f0;
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            letter-spacing: 2px;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            align-items: center;
        }
        
        nav ul li {
            margin-left: 20px;
            position: relative;
        }
        
        nav ul li a {
            text-decoration: none;
            color: #000;
            font-weight: 500;
            transition: color 0.3s;
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 5px;
        }
        
        nav ul li a:hover {
            color: #8b7355;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 20px;
            padding-left: 20px;
            border-left: 1px solid #eee;
        }
        
        .user-menu a {
            font-size: 13px;
            padding: 6px 12px;
        }
        
        /* Layout de Login */
        .login-page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        
        .login-left {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('img/sobre.jpg') no-repeat center center/cover;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            position: relative;
        }
        
        .login-hero-content {
            max-width: 500px;
            padding: 40px;
        }
        
        .login-hero-content h1 {
            font-size: 42px;
            margin-bottom: 20px;
            letter-spacing: 3px;
        }
        
        .login-hero-content p {
            font-size: 18px;
            margin-bottom: 30px;
        }
        
        .login-right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background-color: white;
        }
        
        .login-card {
            background-color: white;
            border-radius: 15px;
            padding: 50px 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
        
        .login-title {
            text-align: center;
            margin-bottom: 40px;
            font-size: 32px;
            color: #000;
            font-weight: 600;
        }
        
        /* Alertas */
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 14px;
            text-align: center;
        }
        
        .alert.error {
            background: #ffeaea;
            color: #8a1b1b;
            border-left: 4px solid #e74c3c;
        }
        
        /* Formulário */
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #000;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #eee;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s;
            background-color: #f9f9f9;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #8b7355;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(139, 115, 85, 0.1);
        }
        
        .form-input::placeholder {
            color: #999;
        }
        
        /* Botões */
        .btn {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            text-align: center;
            width: 100%;
            font-size: 16px;
        }
        
        .btn:hover {
            background-color: #333;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 2px solid #000;
            color: #000;
            margin-top: 15px;
        }
        
        .btn-outline:hover {
            background-color: #000;
            color: white;
        }
        
        .login-links {
            text-align: center;
            margin-top: 30px;
        }
        
        .login-links a {
            color: #8b7355;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            display: block;
            margin-bottom: 10px;
        }
        
        .login-links a:hover {
            color: #000;
            text-decoration: underline;
        }
        
        /* Footer */
        footer {
            background-color: #000;
            color: white;
            padding: 40px 0 20px;
            margin-top: 0;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 30px;
        }
        
        .footer-column h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #fff;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 10px;
        }
        
        .footer-column ul li a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-column ul li a:hover {
            color: #fff;
        }
        
        .contact-info {
            color: #ccc;
        }
        
        .contact-info p {
            margin-bottom: 10px;
        }
        
        .social-links a {
            color: #ccc;
            margin-right: 15px;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .social-links a:hover {
            color: #fff;
        }
        
        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #444;
            color: #999;
            font-size: 14px;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .login-page {
                grid-template-columns: 1fr;
            }
            
            .login-left {
                min-height: 300px;
            }
            
            .login-hero-content h1 {
                font-size: 32px;
            }
            
            .login-hero-content p {
                font-size: 16px;
            }
            
            .login-card {
                padding: 30px 25px;
                margin: -50px 20px 20px;
                box-shadow: 0 15px 30px rgba(0,0,0,0.15);
            }
            
            .header-top {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 15px;
                justify-content: center;
                flex-wrap: wrap;
            }
            
            nav ul li {
                margin: 5px 8px;
            }
            
            .user-menu {
                margin-left: 0;
                padding-left: 0;
                border-left: none;
                justify-content: center;
                width: 100%;
                margin-top: 10px;
            }
        }
        
        @media (max-width: 480px) {
            .login-hero-content h1 {
                font-size: 28px;
            }
            
            .login-title {
                font-size: 28px;
            }
            
            .login-card {
                padding: 25px 20px;
                margin: -30px 15px 15px;
            }
            
            nav ul {
                flex-direction: column;
                gap: 10px;
            }
            
            .user-menu {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
        
        </div>
    </header>

    <div class="login-page">
        <div class="login-left">
            <div class="login-hero-content">
                <h1>Bem-vindo de volta</h1>
                <p>Entre na sua conta e descubra um mundo de fragrâncias exclusivas</p>
            </div>
        </div>
        
        <div class="login-right">
            <div class="login-card">
                <h2 class="login-title">Login</h2>

                <?php if ($login_error): ?>
                    <div class="alert error"><?= htmlspecialchars($login_error) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-group">
                        <input class="form-input" id="email" name="email" type="email" placeholder="E-mail" required>
                    </div>
                    
                    <div class="form-group">
                        <input class="form-input" id="senha" name="senha" type="password" placeholder="Senha" required>
                    </div>

                    <button type="submit" class="btn">Entrar</button>
                </form>

                <div class="login-links">
                    <a href="cadastro.php">Não tem conta? Cadastre-se</a>
                    <a href="index.php">Voltar para página inicial</a>
                </div>
            </div>
        </div>
    </div>
    


    <script>
        // Efeito de foco nos inputs
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-input');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    if (this.value === '') {
                        this.parentElement.classList.remove('focused');
                    }
                });
            });
        });
    </script>
</body>
</html>