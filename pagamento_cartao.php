<?php
session_start();

// Verificar se a variável de sessão existe
if (isset($_SESSION['total_compra'])) {
    $total_compra = $_SESSION['total_compra'];
} else {
    // Se não existir, definir um valor padrão ou redirecionar
    $total_compra = 0;
    // Ou redirecionar de volta para o carrinho
    // header('Location: carrinho.php');
    // exit;
}

$tipo_cartao = isset($_GET['tipo']) ? $_GET['tipo'] : 'credito';
$tipo_nome = $tipo_cartao == 'credito' ? 'Crédito' : 'Débito';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Cartão - LAVELLE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Incluir SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f5f0;
            color: #333;
            line-height: 1.6;
            min-height: 100vh;
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
        
        .payment-subtitle {
            text-align: center;
            color: #8b7355;
            font-weight: 500;
            margin-bottom: 10px;
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
        
        .card-preview-container {
            margin: 30px 0;
        }
        
        .card-preview {
            background: linear-gradient(135deg, #8b7355 0%, #6b5a45 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 8px 20px rgba(139, 115, 85, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .card-preview::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        }
        
        .card-chip {
            width: 40px;
            height: 30px;
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .card-number {
            font-size: 20px;
            letter-spacing: 2px;
            margin: 15px 0;
            font-family: 'Courier New', monospace;
        }
        
        .card-info {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .card-info div {
            display: flex;
            flex-direction: column;
        }
        
        .card-label {
            font-size: 10px;
            opacity: 0.8;
            margin-bottom: 3px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
        }
        
        .form-label i {
            margin-right: 8px;
            color: #8b7355;
        }
        
        .form-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0d5c3;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
            background: white;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #8b7355;
            box-shadow: 0 0 0 3px rgba(139, 115, 85, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .installments {
            margin: 25px 0;
        }
        
        .installments-title {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-weight: 600;
            color: #333;
        }
        
        .installments-title i {
            margin-right: 10px;
            color: #8b7355;
        }
        
        .installment-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }
        
        .installment-option {
            padding: 15px;
            border: 2px solid #e0d5c3;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        
        .installment-option:hover {
            border-color: #8b7355;
            background: #f9f5f0;
        }
        
        .installment-option.selected {
            border-color: #32b572;
            background: #f0f9f0;
        }
        
        .installment-times {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }
        
        .installment-value {
            color: #32b572;
            font-weight: 600;
        }
        
        .security-info {
            background: #f0f9f0;
            padding: 20px;
            border-radius: 8px;
            margin: 30px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-left: 4px solid #32b572;
        }
        
        .security-info i {
            font-size: 20px;
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
            gap: 15px;
            margin: 30px 0;
        }
        
        .btn {
            background: #32b572;
            color: white;
            padding: 18px 30px;
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
            min-width: 200px;
        }
        
        .btn i {
            margin-right: 10px;
        }
        
        .btn:hover {
            background: #2a9d62;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(50, 181, 114, 0.3);
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
            box-shadow: 0 4px 12px rgba(139, 115, 85, 0.3);
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: #8b7355;
            font-size: 14px;
            border-top: 1px solid #e0d5c3;
            margin-top: auto;
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
                gap: 15px;
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
            
            .card-number {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">LAVELLE</div>
            <div class="tagline">ELEGÂNCIA E SOFISTICAÇÃO</div>
            <h1>Pagamento com Cartão</h1>
            <p class="payment-subtitle">Cartão de <?php echo $tipo_nome; ?></p>
        </div>
        
        <div class="payment-container">
            <div class="payment-header">
                <div class="payment-icon">
                    <i class="far fa-credit-card"></i>
                </div>
                <div class="payment-title">Pagamento com Cartão</div>
            </div>
            
            <div class="amount-container">
                <div class="amount-label">Valor total da compra:</div>
                <div class="amount">R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></div>
            </div>
            
            <div class="card-preview-container">
                <div class="card-preview">
                    <div class="card-chip"></div>
                    <div class="card-number" id="cardPreview">•••• •••• •••• ••••</div>
                    <div class="card-info">
                        <div>
                            <div class="card-label">TITULAR DO CARTÃO</div>
                            <div id="namePreview">SEU NOME AQUI</div>
                        </div>
                        <div>
                            <div class="card-label">VALIDADE</div>
                            <div id="expiryPreview">MM/AA</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="paymentForm">
                <div class="form-group">
                    <label class="form-label" for="cardNumber">
                        <i class="fas fa-credit-card"></i>Número do Cartão
                    </label>
                    <input type="text" class="form-input" id="cardNumber" 
                           placeholder="1234 5678 9012 3456" maxlength="19"
                           oninput="formatCardNumber(this)">
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="cardName">
                        <i class="fas fa-user"></i>Nome no Cartão
                    </label>
                    <input type="text" class="form-input" id="cardName" 
                           placeholder="Como aparece no cartão"
                           oninput="document.getElementById('namePreview').textContent = this.value || 'SEU NOME AQUI'">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="cardExpiry">
                            <i class="fas fa-calendar-alt"></i>Validade
                        </label>
                        <input type="text" class="form-input" id="cardExpiry" 
                               placeholder="MM/AA" maxlength="5"
                               oninput="formatExpiry(this)">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cardCvv">
                            <i class="fas fa-lock"></i>CVV
                        </label>
                        <input type="text" class="form-input" id="cardCvv" 
                               placeholder="123" maxlength="4">
                    </div>
                </div>
                
                <?php if ($tipo_cartao == 'credito'): ?>
                <div class="installments">
                    <div class="installments-title">
                        <i class="fas fa-chart-pie"></i>Parcelamento
                    </div>
                    <div class="installment-options" id="installmentsOptions">
                        <!-- Opções de parcelamento serão geradas via JavaScript -->
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="security-info">
                    <i class="fas fa-shield-alt"></i>
                    <div class="security-text">Pagamento 100% seguro via criptografia SSL</div>
                </div>
                
                <div class="btn-container">
                    <button type="button" class="btn" onclick="processPayment()">
                        <i class="fas fa-check-circle"></i> Finalizar Pagamento
                    </button>
                    
                    <button type="button" class="btn btn-outline" onclick="window.location.href='paginaprodutos.php'">
                        <i class="fas fa-arrow-left"></i> Voltar aos Produtos
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="footer">
        LAVELLE &copy; 2023 - Todos os direitos reservados
    </div>

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
        
        async function processPayment() {
            const cardNumber = document.getElementById('cardNumber').value.replace(/\D/g, '');
            const cardName = document.getElementById('cardName').value;
            const cardExpiry = document.getElementById('cardExpiry').value;
            const cardCvv = document.getElementById('cardCvv').value;
            
            // Validações
            if (!cardNumber || cardNumber.length < 16) {
                showValidationError('cardNumber', 'Por favor, insira um número de cartão válido.');
                return;
            }
            
            if (!cardName) {
                showValidationError('cardName', 'Por favor, insira o nome no cartão.');
                return;
            }
            
            if (!cardExpiry || cardExpiry.length < 5) {
                showValidationError('cardExpiry', 'Por favor, insira a validade do cartão.');
                return;
            }
            
            if (!cardCvv || cardCvv.length < 3) {
                showValidationError('cardCvv', 'Por favor, insira o CVV do cartão.');
                return;
            }
            
            // Mostrar confirmação com SweetAlert2
            const result = await Swal.fire({
                title: 'Confirmar Pagamento?',
                html: `
                    <div style="text-align: left;">
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
            });
            
            if (!result.isConfirmed) {
                return;
            }
            
            // Simular processamento
            const btn = document.querySelector('.btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
            btn.disabled = true;
            
            // Mostrar loading com SweetAlert2
            Swal.fire({
                title: 'Processando Pagamento',
                text: 'Aguarde enquanto processamos seu pagamento...',
                icon: 'info',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            }).then(() => {
                // Atualizar botão
                btn.innerHTML = '<i class="fas fa-check"></i> Pagamento Confirmado!';
                btn.style.background = '#28a745';
                
                // Mostrar sucesso com SweetAlert2
                Swal.fire({
                    title: 'Pagamento Confirmado!',
                    html: `
                        <div style="text-align: center;">
                            <i class="fas fa-check-circle" style="font-size: 48px; color: #32b572; margin-bottom: 20px;"></i>
                            <p>Pagamento processado com sucesso!</p>
                            <p><strong>Obrigado pela sua compra.</strong></p>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'Continuar',
                    confirmButtonColor: '#32b572'
                }).then(() => {
                    // Redirecionar após confirmação
                    window.location.href = 'paginaprodutos.php?pagamento=sucesso';
                });
            });
        }
        
        function showValidationError(fieldId, message) {
            const field = document.getElementById(fieldId);
            field.style.borderColor = '#e74c3c';
            field.focus();
            
            // Criar mensagem de erro temporária
            const errorDiv = document.createElement('div');
            errorDiv.style.color = '#e74c3c';
            errorDiv.style.fontSize = '14px';
            errorDiv.style.marginTop = '5px';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
            
            // Remover mensagens anteriores
            const existingError = field.parentNode.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
            
            errorDiv.className = 'error-message';
            field.parentNode.appendChild(errorDiv);
            
            // Remover estilização após correção
            field.addEventListener('input', function() {
                this.style.borderColor = '#e0d5c3';
                const error = this.parentNode.querySelector('.error-message');
                if (error) error.remove();
            }, { once: true });
        }
        
        <?php if ($tipo_cartao == 'credito'): ?>
        // Gerar opções de parcelamento apenas para crédito
        generateInstallments();
        <?php endif; ?>
    </script>
</body>
</html>