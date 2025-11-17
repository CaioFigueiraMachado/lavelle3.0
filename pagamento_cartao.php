<?php
session_start();

// Verificar se a variável de sessão existe
if (isset($_SESSION['total_compra'])) {
    $total_compra = $_SESSION['total_compra'];
} else {
    $total_compra = 0;
}

$tipo_cartao = isset($_GET['tipo']) ? $_GET['tipo'] : 'credito';
$tipo_nome = $tipo_cartao == 'credito' ? 'CRÉDITO' : 'Débito';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Cartão - LAVELLE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: #f9f5f0;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
            font-family: 'Montserrat', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            flex: 1;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px 0;
            border-bottom: 1px solid #e0d5c3;
        }
        
        .logo {
            font-size: 36px;
            font-weight: 700;
            color: #000;
            letter-spacing: 3px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .tagline {
            font-size: 14px;
            color: #8b7355;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }
        
        .payment-container {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .payment-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .payment-icon {
            font-size: 28px;
            margin-right: 15px;
            color: #8b7355;
        }
        
        .payment-title {
            font-size: 24px;
            font-weight: 600;
            color: #000;
        }
        
        .amount-container {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 25px 0;
            border-left: 4px solid #8b7355;
            text-align: center;
        }
        
        .amount-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .amount {
            font-size: 32px;
            font-weight: 700;
            color: #32b572;
        }
        
        .card-preview {
            border: 1px solid #e0d5c3;
            border-radius: 10px;
            padding: 30px;
            margin: 25px 0;
            background: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .card-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #e0d5c3;
        }
        
        .card-icon {
            font-size: 48px;
            color: #8b7355;
            margin-bottom: 10px;
        }
        
        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: #000;
        }
        
        .card-number {
            font-family: monospace;
            font-size: 18px;
            letter-spacing: 2px;
            margin: 15px 0;
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .card-info {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-weight: 600;
            color: #333;
        }
        
        .form-section {
            margin: 30px 0;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #000;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0e6d8;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e8dfd1;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #8b7355;
            box-shadow: 0 0 0 3px rgba(139, 115, 85, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .installments {
            margin: 30px 0;
        }
        
        .installment-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }
        
        .installment-option {
            padding: 15px;
            border: 2px solid #e8dfd1;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
        }
        
        .installment-option:hover {
            border-color: #8b7355;
        }
        
        .installment-option.selected {
            border-color: #32b572;
            background: #f0f9f0;
        }
        
        .installment-times {
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .installment-value {
            color: #32b572;
            font-weight: 600;
        }
        
        .security-info {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin: 25px 0;
            display: flex;
            align-items: center;
            border-left: 4px solid #32b572;
        }
        
        .security-info i {
            font-size: 24px;
            color: #32b572;
            margin-right: 15px;
        }
        
        .security-text {
            color: #2d5016;
            font-weight: 500;
        }
        
        .btn-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin: 30px 0;
        }
        
        .btn {
            background: #32b572;
            color: white;
            padding: 15px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            min-width: 180px;
        }
        
        .btn i {
            margin-right: 10px;
        }
        
        .btn:hover {
            background: #2a9d62;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #8b7355;
            color: #8b7355;
        }
        
        .btn-outline:hover {
            background: #8b7355;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(139, 115, 85, 0.2);
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: #8b7355;
            font-size: 14px;
            border-top: 1px solid #e0d5c3;
            margin-top: auto;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #8b7355;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .payment-container {
                padding: 25px 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .installment-options {
                grid-template-columns: 1fr;
            }
            
            .btn-container {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <div class="container">
        <div class="header">
            <div class="logo">LAVELLE</div>
            <div class="tagline">ELEGÂNCIA E SOFISTICAÇÃO</div>
            <h1>Pagamento com Cartão</h1>
            <p style="color: #8b7355; margin-top: 10px;">Cartão de <?php echo $tipo_nome; ?></p>
        </div>
        
        <div class="payment-container">
            <div class="payment-header">
                <div class="payment-icon">
                    <i class="far fa-credit-card"></i>
                </div>
                <div class="payment-title">Pagamento com Cartão</div>
            </div>
            
            <div class="amount-container">
                <div class="amount-label">Valor total do pagamento:</div>
                <div class="amount">R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></div>
            </div>
            
            <div class="card-preview">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="far fa-credit-card"></i>
                    </div>
                    <div class="card-title">CARTÃO <?php echo strtoupper($tipo_nome); ?></div>
                </div>
                
                <div class="card-number" id="cardPreview">•••• •••• •••• ••••</div>
                
                <div class="card-info">
                    <div class="info-item">
                        <span class="info-label">Titular do cartão</span>
                        <span class="info-value" id="namePreview">SEU NOME AQUI</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Validade</span>
                        <span class="info-value" id="expiryPreview">MM/AA</span>
                    </div>
                </div>
            </div>
            
            <form id="paymentForm">
                <div class="form-section">
                    <h3 class="section-title">Dados do Cartão</h3>
                    
                    <div class="form-group">
                        <label class="form-label" for="cardNumber">
                            <i class="fas fa-credit-card"></i> Número do Cartão
                        </label>
                        <input type="text" class="form-input" id="cardNumber" 
                               placeholder="1234 5678 9012 3456" maxlength="19"
                               oninput="formatCardNumber(this)">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="cardName">
                            <i class="fas fa-user"></i> Nome no Cartão
                        </label>
                        <input type="text" class="form-input" id="cardName" 
                               placeholder="Como aparece no cartão"
                               oninput="document.getElementById('namePreview').textContent = this.value || 'SEU NOME AQUI'">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="cardExpiry">
                                <i class="fas fa-calendar-alt"></i> Validade
                            </label>
                            <input type="text" class="form-input" id="cardExpiry" 
                                   placeholder="MM/AA" maxlength="5"
                                   oninput="formatExpiry(this)">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="cardCvv">
                                <i class="fas fa-lock"></i> Código de Segurança
                            </label>
                            <input type="text" class="form-input" id="cardCvv" 
                                   placeholder="123" maxlength="4">
                        </div>
                    </div>
                </div>
                
                <?php if ($tipo_cartao == 'credito'): ?>
                <div class="installments">
                    <h3 class="section-title">Opções de Parcelamento</h3>
                    <div class="installment-options" id="installmentsOptions">
                        <!-- Opções de parcelamento serão geradas via JavaScript -->
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="security-info">
                    <i class="fas fa-shield-alt"></i>
                    <div class="security-text">Seus dados estão protegidos com criptografia de última geração</div>
                </div>
                
                <div class="btn-container">
                    <button type="button" class="btn" onclick="processPayment()">
                        <i class="fas fa-check"></i> Confirmar Pagamento
                    </button>
                    
                    <button type="button" class="btn btn-outline" onclick="window.location.href='paginaprodutos.php'">
                        <i class="fas fa-arrow-left"></i> Voltar aos Produtos
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="footer">
        LAVELLE &copy; 2025 - Todos os direitos reservados
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        function formatCardNumber(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})/g, '$1 ').trim();
            value = value.substring(0, 19);
            input.value = value;
            
            // Atualizar preview do cartão
            const preview = value || '•••• •••• •••• ••••';
            document.getElementById('cardPreview').textContent = preview;
        }
        
        function formatExpiry(input) {
            let value = input.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            input.value = value;
            document.getElementById('expiryPreview').textContent = value || 'MM/AA';
        }
        
        function generateInstallments() {
            const total = <?php echo $total_compra; ?>;
            const container = document.getElementById('installmentsOptions');
            container.innerHTML = '';
            
            for (let i = 1; i <= 12; i++) {
                const installmentValue = total / i;
                const option = document.createElement('div');
                option.className = 'installment-option';
                if (i === 1) option.classList.add('selected');
                
                option.innerHTML = `
                    <div class="installment-times">${i}x</div>
                    <div class="installment-value">R$ ${installmentValue.toFixed(2).replace('.', ',')}</div>
                    <input type="radio" name="installment" value="${i}" ${i === 1 ? 'checked' : ''} style="display: none;">
                `;
                
                option.onclick = () => {
                    document.querySelectorAll('.installment-option').forEach(el => {
                        el.classList.remove('selected');
                    });
                    option.classList.add('selected');
                    option.querySelector('input').checked = true;
                };
                
                container.appendChild(option);
            }
        }
        
        function processPayment() {
            const cardNumber = document.getElementById('cardNumber').value.replace(/\D/g, '');
            const cardName = document.getElementById('cardName').value;
            const cardExpiry = document.getElementById('cardExpiry').value;
            const cardCvv = document.getElementById('cardCvv').value;
            
            // Validações básicas
            if (!cardNumber || cardNumber.length < 16) {
                Swal.fire({
                    title: 'Campo Inválido',
                    text: 'Por favor, insira um número de cartão válido.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#32b572'
                });
                return;
            }
            
            if (!cardName) {
                Swal.fire({
                    title: 'Campo Inválido',
                    text: 'Por favor, insira o nome no cartão.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#32b572'
                });
                return;
            }
            
            if (!cardExpiry || cardExpiry.length < 5) {
                Swal.fire({
                    title: 'Campo Inválido',
                    text: 'Por favor, insira a validade do cartão.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#32b572'
                });
                return;
            }
            
            if (!cardCvv || cardCvv.length < 3) {
                Swal.fire({
                    title: 'Campo Inválido',
                    text: 'Por favor, insira o código de segurança.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#32b572'
                });
                return;
            }
            
            // SweetAlert2 para confirmação de pagamento
            Swal.fire({
                title: 'Confirmar Pagamento?',
                html: `
                    <div style="text-align: center; padding: 10px;">
                        <p><strong>Valor:</strong> R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></p>
                        <p><strong>Cartão:</strong> **** **** **** ${cardNumber.slice(-4)}</p>
                        <p><strong>Tipo:</strong> <?php echo $tipo_nome; ?></p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, Confirmar Pagamento',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#32b572',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    document.getElementById('loadingOverlay').style.display = 'flex';
                    
                    // Simular processamento
                    setTimeout(() => {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        
                        // SweetAlert2 de sucesso
                        Swal.fire({
                            title: 'Pagamento Confirmado!',
                            text: 'Obrigado pela preferência. Você será redirecionado para a página inicial.',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#32b572',
                            timer: 3000,
                            timerProgressBar: true
                        }).then(() => {
                            // Redirecionar para index.php
                            window.location.href = 'index.php';
                        });
                        
                        // Redirecionar automaticamente após 3 segundos
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 3000);
                    }, 2000);
                }
            });
        }
        
        <?php if ($tipo_cartao == 'credito'): ?>
        // Gerar opções de parcelamento apenas para crédito
        document.addEventListener('DOMContentLoaded', function() {
            generateInstallments();
        });
        // Após o pagamento ser confirmado, adicione:
function processarPedidoAposPagamento($db, $metodo_pagamento) {
    try {
        // Inserir pedido no banco de dados
        $stmt = $db->prepare("
            INSERT INTO pedidos (usuario_id, data_pedido, total, status, metodo_pagamento, endereco_entrega) 
            VALUES (?, NOW(), ?, 'pendente', ?, ?)
        ");
        
        $stmt->execute([
            $_SESSION['id'],
            $_SESSION['total_compra'],
            $metodo_pagamento,
            $_SESSION['endereco_entrega']
        ]);
        
        $pedido_id = $db->lastInsertId();
        
        // Inserir itens do pedido
        foreach ($_SESSION['produtos_carrinho'] as $produto_id => $item) {
            $stmt = $db->prepare("
                INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario, subtotal) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $pedido_id,
                $produto_id,
                $item['quantidade'],
                $item['produto']['preco'],
                $item['subtotal']
            ]);
        }
        
        // Gerar comprovante automaticamente
        require_once 'receipt_generator.php';
        $receiptGenerator = new ReceiptGenerator();
        $filename = $receiptGenerator->generateReceipt($pedido_id, $db);
        
        // Limpar carrinho
        unset($_SESSION['carrinho']);
        unset($_SESSION['produtos_carrinho']);
        unset($_SESSION['endereco_entrega']);
        unset($_SESSION['total_compra']);
        unset($_SESSION['itens_carrinho']);
        unset($_SESSION['metodo_pagamento']);
        
        return $pedido_id;
        
    } catch(PDOException $e) {
        error_log("Erro ao salvar pedido: " . $e->getMessage());
        return false;
    }
}
        <?php endif; ?>
    </script>
</body>
</html>