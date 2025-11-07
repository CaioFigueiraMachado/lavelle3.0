<?php
session_start();
include 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    $_SESSION['redirect_to'] = 'endereco_entrega.php';
    header('Location: login.php');
    exit();
}

// Verificar se há uma compra em andamento
if (!isset($_SESSION['metodo_pagamento']) || empty($_SESSION['carrinho'])) {
    header('Location: paginaprodutos.php');
    exit();
}

// *** NOVO: Processar envio do formulário de endereço ***
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirmar_endereco'])) {
    // Verificar se usuário está logado
    if (!isset($_SESSION['id'])) {
        $_SESSION['redirect_to'] = 'endereco_entrega.php';
        header('Location: login.php');
        exit();
    }
    
    // Validar dados obrigatórios
    $required_fields = ['cep', 'logradouro', 'numero', 'bairro', 'cidade', 'estado'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['erro_endereco'] = "Por favor, preencha todos os campos obrigatórios.";
            header('Location: endereco_entrega.php');
            exit();
        }
    }
    
    // Coletar dados do endereço
    $endereco = [
        'cep' => $_POST['cep'],
        'logradouro' => $_POST['logradouro'],
        'numero' => $_POST['numero'],
        'complemento' => $_POST['complemento'] ?? '',
        'bairro' => $_POST['bairro'],
        'cidade' => $_POST['cidade'],
        'estado' => $_POST['estado'],
        'ponto_referencia' => $_POST['ponto_referencia'] ?? ''
    ];
    
    // Salvar endereço na sessão
    $_SESSION['endereco_entrega'] = $endereco;
    
    // Configurar redirecionamento para o método de pagamento escolhido
    $metodo_pagamento = $_SESSION['metodo_pagamento'];
    $_SESSION['show_payment_redirect'] = true;
    $_SESSION['payment_method'] = $metodo_pagamento;
    
    // Definir URL de redirecionamento baseada no método de pagamento
    switch($metodo_pagamento) {
        case 'credit':
            $_SESSION['redirect_url'] = 'pagamento_cartao.php';
            break;
        case 'pix':
            $_SESSION['redirect_url'] = 'pagamento_pix.php';
            break;
        case 'boleto':
            $_SESSION['redirect_url'] = 'pagamento_boleto.php';
            break;
        default:
            $_SESSION['redirect_url'] = 'paginaprodutos.php';
            break;
    }
    
    // Redirecionar de volta para mostrar o SweetAlert de confirmação
    header('Location: paginaprodutos.php?show_redirect=true');
    exit();
}

// Buscar endereço do usuário do banco de dados (se existir)
$endereco_usuario = [];
try {
    $database = new PDO("mysql:host=localhost;dbname=lavelle_perfumes", "root", "");
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $query = "SELECT cep, logradouro, numero, complemento, bairro, cidade, estado 
              FROM usuarios WHERE id = :usuario_id";
    $stmt = $database->prepare($query);
    $stmt->bindParam(':usuario_id', $_SESSION['id']);
    $stmt->execute();
    
    $endereco_usuario = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // Endereço não encontrado ou erro na consulta
    $endereco_usuario = [];
}

$empresa = "LAVELLE";
$usuarioNome = $_SESSION['nome'];
$total_compra = $_SESSION['total_compra'];
$metodo_pagamento = $_SESSION['metodo_pagamento'];

// Definir nome do método de pagamento para exibição
$nomes_pagamento = [
    'credit' => 'Cartão de Crédito',
    'pix' => 'PIX',
    'boleto' => 'Boleto Bancário'
];
$metodo_pagamento_nome = $nomes_pagamento[$metodo_pagamento] ?? 'Método de Pagamento';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endereço de Entrega - LAVELLE Perfumes</title>
    <style>
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
        
        /* Header Banner */
        .header-banner {
            background-color: #000;
            color: #ffffff;
            text-align: center;
            padding: 8px 0;
            font-size: 14px;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
        }
        
        .header-banner h1 {
            font-size: 14px;
            font-weight: 300;
            margin: 0;
            padding: 0;
            letter-spacing: 3px;
            color: #f5f5f5;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            padding: 20px 0;
            background-color: #f5f5f5;
            margin-bottom: 40px;
        }
        
        .breadcrumb a {
            color: #666;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            color: #000;
        }
        
        /* Conteúdo Principal */
        .checkout-container {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
            margin-bottom: 60px;
        }
        
        .address-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .address-form h2 {
            margin-bottom: 25px;
            color: #000;
            font-size: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #000;
            font-weight: 500;
            font-size: 14px;
        }
        
        .required::after {
            content: " *";
            color: #e74c3c;
        }
        
        .form-input {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #8b7355;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .order-summary {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: fit-content;
            position: sticky;
            top: 100px;
        }
        
        .order-summary h3 {
            margin-bottom: 20px;
            color: #000;
            font-size: 20px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .summary-total {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 18px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #000;
        }
        
        .payment-method-info {
            background: #f9f5f0;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            text-align: center;
        }
        
        .btn {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 15px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            text-align: center;
            width: 100%;
            font-size: 16px;
            margin-top: 20px;
        }
        
        .btn:hover {
            background-color: #333;
        }
        
        .btn-back {
            background-color: #6c757d;
            margin-top: 10px;
        }
        
        .btn-back:hover {
            background-color: #5a6268;
        }
        
        /* Footer */
        footer {
            background-color: #000;
            color: white;
            padding: 60px 0 30px;
            margin-top: 60px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
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
            padding-top: 30px;
            border-top: 1px solid #444;
            color: #999;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .checkout-container {
                grid-template-columns: 1fr;
            }
            
            .order-summary {
                position: static;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header-banner">
        <h1>O perfume certo transforma a presença em memória.</h1>
    </div>
     
    <header>
        <div class="container">
            <div class="header-top">
                <div class="logo"><?php echo $empresa; ?></div>
                <nav>
                    <span style="color: #8b7355; font-weight: 500;">Olá, <?php echo htmlspecialchars($usuarioNome); ?></span>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="breadcrumb">
        <div class="container">
            <a href="index.php">Início</a> > <a href="paginaprodutos.php">Produtos</a> > <span>Endereço de Entrega</span>
        </div>
    </div>
    
    <div class="container">
        <div class="checkout-container">
            <div class="address-form">
                <h2>Endereço de Entrega</h2>
                
                <form method="POST" id="addressForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="cep">CEP</label>
                            <input type="text" class="form-input" id="cep" name="cep" 
                                   value="<?php echo $endereco_usuario['cep'] ?? ''; ?>" 
                                   required maxlength="9" onblur="buscarCep()" placeholder="00000-000">
                        </div>
                        <div class="form-group">
                            <label class="form-label required" for="numero">Número</label>
                            <input type="text" class="form-input" id="numero" name="numero" 
                                   value="<?php echo $endereco_usuario['numero'] ?? ''; ?>" required placeholder="123">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label required" for="logradouro">Logradouro</label>
                        <input type="text" class="form-input" id="logradouro" name="logradouro" 
                               value="<?php echo $endereco_usuario['logradouro'] ?? ''; ?>" required placeholder="Rua das Flores">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="complemento">Complemento</label>
                        <input type="text" class="form-input" id="complemento" name="complemento" 
                               value="<?php echo $endereco_usuario['complemento'] ?? ''; ?>" placeholder="Apartamento 101">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="bairro">Bairro</label>
                            <input type="text" class="form-input" id="bairro" name="bairro" 
                                   value="<?php echo $endereco_usuario['bairro'] ?? ''; ?>" required placeholder="Centro">
                        </div>
                        <div class="form-group">
                            <label class="form-label required" for="cidade">Cidade</label>
                            <input type="text" class="form-input" id="cidade" name="cidade" 
                                   value="<?php echo $endereco_usuario['cidade'] ?? ''; ?>" required placeholder="São Paulo">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="estado">Estado</label>
                            <select class="form-input" id="estado" name="estado" required>
                                <option value="">Selecione</option>
                                <option value="AC" <?php echo ($endereco_usuario['estado'] ?? '') == 'AC' ? 'selected' : ''; ?>>Acre</option>
                                <option value="AL" <?php echo ($endereco_usuario['estado'] ?? '') == 'AL' ? 'selected' : ''; ?>>Alagoas</option>
                                <option value="AP" <?php echo ($endereco_usuario['estado'] ?? '') == 'AP' ? 'selected' : ''; ?>>Amapá</option>
                                <option value="AM" <?php echo ($endereco_usuario['estado'] ?? '') == 'AM' ? 'selected' : ''; ?>>Amazonas</option>
                                <option value="BA" <?php echo ($endereco_usuario['estado'] ?? '') == 'BA' ? 'selected' : ''; ?>>Bahia</option>
                                <option value="CE" <?php echo ($endereco_usuario['estado'] ?? '') == 'CE' ? 'selected' : ''; ?>>Ceará</option>
                                <option value="DF" <?php echo ($endereco_usuario['estado'] ?? '') == 'DF' ? 'selected' : ''; ?>>Distrito Federal</option>
                                <option value="ES" <?php echo ($endereco_usuario['estado'] ?? '') == 'ES' ? 'selected' : ''; ?>>Espírito Santo</option>
                                <option value="GO" <?php echo ($endereco_usuario['estado'] ?? '') == 'GO' ? 'selected' : ''; ?>>Goiás</option>
                                <option value="MA" <?php echo ($endereco_usuario['estado'] ?? '') == 'MA' ? 'selected' : ''; ?>>Maranhão</option>
                                <option value="MT" <?php echo ($endereco_usuario['estado'] ?? '') == 'MT' ? 'selected' : ''; ?>>Mato Grosso</option>
                                <option value="MS" <?php echo ($endereco_usuario['estado'] ?? '') == 'MS' ? 'selected' : ''; ?>>Mato Grosso do Sul</option>
                                <option value="MG" <?php echo ($endereco_usuario['estado'] ?? '') == 'MG' ? 'selected' : ''; ?>>Minas Gerais</option>
                                <option value="PA" <?php echo ($endereco_usuario['estado'] ?? '') == 'PA' ? 'selected' : ''; ?>>Pará</option>
                                <option value="PB" <?php echo ($endereco_usuario['estado'] ?? '') == 'PB' ? 'selected' : ''; ?>>Paraíba</option>
                                <option value="PR" <?php echo ($endereco_usuario['estado'] ?? '') == 'PR' ? 'selected' : ''; ?>>Paraná</option>
                                <option value="PE" <?php echo ($endereco_usuario['estado'] ?? '') == 'PE' ? 'selected' : ''; ?>>Pernambuco</option>
                                <option value="PI" <?php echo ($endereco_usuario['estado'] ?? '') == 'PI' ? 'selected' : ''; ?>>Piauí</option>
                                <option value="RJ" <?php echo ($endereco_usuario['estado'] ?? '') == 'RJ' ? 'selected' : ''; ?>>Rio de Janeiro</option>
                                <option value="RN" <?php echo ($endereco_usuario['estado'] ?? '') == 'RN' ? 'selected' : ''; ?>>Rio Grande do Norte</option>
                                <option value="RS" <?php echo ($endereco_usuario['estado'] ?? '') == 'RS' ? 'selected' : ''; ?>>Rio Grande do Sul</option>
                                <option value="RO" <?php echo ($endereco_usuario['estado'] ?? '') == 'RO' ? 'selected' : ''; ?>>Rondônia</option>
                                <option value="RR" <?php echo ($endereco_usuario['estado'] ?? '') == 'RR' ? 'selected' : ''; ?>>Roraima</option>
                                <option value="SC" <?php echo ($endereco_usuario['estado'] ?? '') == 'SC' ? 'selected' : ''; ?>>Santa Catarina</option>
                                <option value="SP" <?php echo ($endereco_usuario['estado'] ?? '') == 'SP' ? 'selected' : ''; ?>>São Paulo</option>
                                <option value="SE" <?php echo ($endereco_usuario['estado'] ?? '') == 'SE' ? 'selected' : ''; ?>>Sergipe</option>
                                <option value="TO" <?php echo ($endereco_usuario['estado'] ?? '') == 'TO' ? 'selected' : ''; ?>>Tocantins</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="ponto_referencia">Ponto de Referência</label>
                            <input type="text" class="form-input" id="ponto_referencia" name="ponto_referencia" placeholder="Próximo ao mercado">
                        </div>
                    </div>
                    
                    <button type="submit" name="confirmar_endereco" class="btn">
                        Confirmar Endereço e Continuar
                    </button>
                    
                    <a href="paginaprodutos.php" class="btn btn-back" style="text-decoration: none;">
                        Voltar para Produtos
                    </a>
                </form>
            </div>
            
            <div class="order-summary">
                <h3>Resumo do Pedido</h3>
                
                <div class="summary-item">
                    <span>Subtotal:</span>
                    <span>R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></span>
                </div>
                
                <div class="summary-item">
                    <span>Frete:</span>
                    <span>Grátis</span>
                </div>
                
                <div class="summary-total">
                    <span>Total:</span>
                    <span>R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></span>
                </div>
                
                <div class="payment-method-info">
                    <strong>Método de Pagamento:</strong><br>
                    <?php echo $metodo_pagamento_nome; ?>
                </div>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>CONTATO</h3>
                    <div class="contact-info">
                        <p>E-mail: contatolavelle@gmail.com</p>
                        <p>Endereço: Rua das Fragrâncias, 123 - Jardim Perfumado</p>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>REDES SOCIAIS</h3>
                    <div class="social-links">
                        <a href="#">Facebook</a><br>
                        <a href="#">Instagram</a><br>
                        <a href="#">Twitter</a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>POLÍTICAS</h3>
                    <ul>
                        <li><a href="#">Política de Privacidade</a></li>
                        <li><a href="#">Termos de Uso</a></li>
                        <li><a href="#">Trocas e Devoluções</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>INFORMAÇÕES</h3>
                    <ul>
                        <li><a href="sobre.php">Sobre Nós</a></li>
                        <li><a href="#">Nossa História</a></li>
                        <li><a href="#">Trabalhe Conosco</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> LAVELLE Perfumes. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- SweetAlert Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert para erros de endereço -->
    <?php if (isset($_SESSION['erro_endereco'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Erro no Endereço',
                text: '<?php echo $_SESSION['erro_endereco']; ?>',
                icon: 'error',
                confirmButtonColor: '#8b7355'
            });
        });
    </script>
    <?php 
        unset($_SESSION['erro_endereco']);
    ?>
    <?php endif; ?>

    <script>
        // Função para buscar CEP via API
        function buscarCep() {
            const cep = document.getElementById('cep').value.replace(/\D/g, '');
            
            if (cep.length !== 8) {
                return;
            }
            
            // Mostrar loading
            Swal.fire({
                title: 'Buscando CEP...',
                text: 'Aguarde enquanto consultamos o endereço',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(response => response.json())
                .then(data => {
                    Swal.close();
                    
                    if (!data.erro) {
                        document.getElementById('logradouro').value = data.logradouro;
                        document.getElementById('bairro').value = data.bairro;
                        document.getElementById('cidade').value = data.localidade;
                        document.getElementById('estado').value = data.uf;
                        document.getElementById('numero').focus();
                        
                        // Mostrar confirmação
                        Swal.fire({
                            title: 'CEP Encontrado!',
                            text: 'Endereço preenchido automaticamente',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: 'CEP não encontrado',
                            text: 'Verifique o CEP e tente novamente',
                            icon: 'warning',
                            confirmButtonColor: '#8b7355'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    console.error('Erro ao buscar CEP:', error);
                    Swal.fire({
                        title: 'Erro na consulta',
                        text: 'Não foi possível buscar o CEP. Tente novamente.',
                        icon: 'error',
                        confirmButtonColor: '#8b7355'
                    });
                });
        }
        
        // Formatação do CEP
        document.getElementById('cep').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5, 8);
            }
            e.target.value = value;
        });
        
        // Validação do formulário antes do envio
        document.getElementById('addressForm').addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            let firstInvalidField = null;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                    field.style.borderColor = '#e74c3c';
                } else {
                    field.style.borderColor = '#ddd';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    title: 'Campos Obrigatórios',
                    text: 'Por favor, preencha todos os campos marcados com *',
                    icon: 'warning',
                    confirmButtonColor: '#8b7355'
                });
                
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
            }
        });
    </script>
</body>
</html>