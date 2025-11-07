<?php
session_start();

// Verificar se a variável de sessão existe
if (isset($_SESSION['total_compra'])) {
    $total_compra = $_SESSION['total_compra'];
} else {
    $total_compra = 0;
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
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #f9f5f0 0%, #f0e6d8 100%);
            color: #2c2c2c;
            line-height: 1.6;
            min-height: 100vh;
            font-family: 'Montserrat', sans-serif;
            display: flex;
            flex-direction: column;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 30px 20px;
            flex: 1;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 0;
            position: relative;
        }
        
        .header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 2px;
            background: linear-gradient(90deg, transparent, #8b7355, transparent);
        }
        
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 700;
            color: #000;
            letter-spacing: 4px;
            margin-bottom: 8px;
            text-transform: uppercase;
            position: relative;
            display: inline-block;
        }
        
        .logo::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, #8b7355, transparent);
        }
        
        .tagline {
            font-size: 13px;
            color: #8b7355;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 300;
            margin-bottom: 15px;
        }
        
        .page-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 500;
            color: #2c2c2c;
            margin-bottom: 5px;
        }
        
        .payment-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(139, 115, 85, 0.1);
            margin-bottom: 40px;
            position: relative;
            overflow: hidden;
        }
        
        .payment-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #8b7355, #c4b5a3, #8b7355);
        }
        
        .payment-header {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            padding: 20px 0;
        }
        
        .payment-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #8b7355, #6b5a45);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            box-shadow: 0 8px 20px rgba(139, 115, 85, 0.3);
        }
        
        .payment-icon i {
            font-size: 24px;
            color: white;
        }
        
        .payment-title {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 600;
            color: #2c2c2c;
            letter-spacing: 1px;
        }
        
        .payment-subtitle {
            text-align: center;
            color: #8b7355;
            font-weight: 500;
            font-size: 16px;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }
        
        .amount-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 15px;
            margin: 30px 0;
            border: 1px solid rgba(139, 115, 85, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .amount-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #8b7355, #c4b5a3);
        }
        
        .amount-label {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }
        
        .amount {
            font-family: 'Playfair Display', serif;
            font-size: 42px;
            font-weight: 600;
            color: #2c2c2c;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .card-preview-container {
            margin: 40px 0;
            perspective: 1000px;
        }
        
        .card-preview {
            background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
            color: white;
            padding: 30px;
            border-radius: 16px;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
            transition: transform 0.3s ease;
        }
        
        .card-preview:hover {
            transform: rotateY(5deg) rotateX(5deg);
        }
        
        .card-preview::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            opacity: 0.3;
        }
        
        .card-chip {
            width: 50px;
            height: 35px;
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            border-radius: 8px;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        
        .card-chip::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 40%, rgba(255,255,255,0.3) 50%, transparent 60%);
        }
        
        .card-number {
            font-size: 24px;
            letter-spacing: 3px;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            font-weight: 500;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        
        .card-info {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin-top: 15px;
        }
        
        .card-info div {
            display: flex;
            flex-direction: column;
        }
        
        .card-label {
            font-size: 10px;
            opacity: 0.7;
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .form-section {
            margin: 40px 0;
        }
        
        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 500;
            color: #2c2c2c;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0e6d8;
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 60px;
            height: 2px;
            background: #8b7355;
        }
        
        .form-group {
            margin-bottom: 30px;
            position: relative;
        }
        
        .form-label {
            display: block;
            margin-bottom: 12px;
            font-weight: 600;
            color: #2c2c2c;
            display: flex;
            align-items: center;
            font-size: 15px;
        }
        
        .form-label i {
            margin-right: 12px;
            color: #8b7355;
            width: 20px;
            text-align: center;
        }
        
        .form-input {
            width: 100%;
            padding: 18px 20px;
            border: 2px solid #e8dfd1;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            font-family: 'Montserrat', sans-serif;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #8b7355;
            background: white;
            box-shadow: 0 5px 15px rgba(139, 115, 85, 0.1);
            transform: translateY(-2px);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }
        
        .installments {
            margin: 40px 0;
        }
        
        .installments-title {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-weight: 600;
            color: #2c2c2c;
            font-size: 18px;
        }
        
        .installments-title i {
            margin-right: 15px;
            color: #8b7355;
            font-size: 20px;
        }
        
        .installment-options {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
        }
        
        .installment-option {
            padding: 20px;
            border: 2px solid #e8dfd1;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            background: rgba(255, 255, 255, 0.7);
            position: relative;
            overflow: hidden;
        }
        
        .installment-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(139, 115, 85, 0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .installment-option:hover::before {
            left: 100%;
        }
        
        .installment-option:hover {
            border-color: #8b7355;
            background: white;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(139, 115, 85, 0.15);
        }
        
        .installment-option.selected {
            border-color: #32b572;
            background: linear-gradient(135deg, #f0f9f0, #e8f5e8);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(50, 181, 114, 0.2);
        }
        
        .installment-times {
            font-weight: 700;
            color: #2c2c2c;
            margin-bottom: 8px;
            font-size: 18px;
        }
        
        .installment-value {
            color: #32b572;
            font-weight: 600;
            font-size: 16px;
        }
        
        .security-info {
            background: linear-gradient(135deg, #f0f9f0 0%, #e8f5e8 100%);
            padding: 25px;
            border-radius: 12px;
            margin: 40px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(50, 181, 114, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .security-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #32b572, #28a745);
        }
        
        .security-info i {
            font-size: 24px;
            color: #32b572;
            margin-right: 20px;
        }
        
        .security-text {
            color: #2d5016;
            font-weight: 500;
            font-size: 15px;
        }
        
        .btn-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 50px 0 30px;
        }
        
        .btn {
            background: linear-gradient(135deg, #8b7355, #6b5a45);
            color: white;
            padding: 20px 35px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
            min-width: 220px;
            font-family: 'Montserrat', sans-serif;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn:hover::before {
            left: 100%;
        }
        
        .btn i {
            margin-right: 12px;
            font-size: 18px;
        }
        
        .btn:hover {
            background: linear-gradient(135deg, #6b5a45, #8b7355);
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(139, 115, 85, 0.3);
        }
        
        .btn:active {
            transform: translateY(-1px);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #8b7355;
            color: #8b7355;
            position: relative;
        }
        
        .btn-outline::before {
            display: none;
        }
        
        .btn-outline:hover {
            background: #8b7355;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 12px 25px rgba(139, 115, 85, 0.3);
        }
        
        .footer {
            text-align: center;
            padding: 30px 20px;
            color: #8b7355;
            font-size: 14px;
            border-top: 1px solid rgba(139, 115, 85, 0.2);
            margin-top: auto;
            background: rgba(255, 255, 255, 0.8);
        }
        
        .footer-content {
            max-width: 900px;
            margin: 0 auto;
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
            width: 60px;
            height: 60px;
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
                padding: 20px 15px;
            }
            
            .payment-container {
                padding: 30px 25px;
            }
            
            .payment-header {
                flex-direction: column;
                text-align: center;
            }
            
            .payment-icon {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .installment-options {
                grid-template-columns: 1fr;
            }
            
            .btn-container {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                min-width: auto;
            }
            
            .card-number {
                font-size: 20px;
            }
            
            .amount {
                font-size: 36px;
            }
        }
        
        @media (max-width: 480px) {
            .payment-container {
                padding: 25px 20px;
            }
            
            .logo {
                font-size: 36px;
            }
            
            .payment-title {
                font-size: 28px;
            }
            
            .amount {
                font-size: 32px;
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
            <div class="tagline">ELEGÂNCIA EM FRAGRÂNCIAS</div>
            <h1 class="page-title">Pagamento com Cartão</h1>
            <p class="payment-subtitle">Cartão de <?php echo $tipo_nome; ?></p>
        </div>
        
        <div class="payment-container">
            <div class="payment-header">
                <div class="payment-icon">
                    <i class="far fa-credit-card"></i>
                </div>
                <div class="payment-title">Pagamento Seguro</div>
            </div>
            
            <div class="amount-container">
                <div class="amount-label">Valor total da compra</div>
                <div class="amount">R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></div>
            </div>
            
            <div class="card-preview-container">
                <div class="card-preview">
                    <div class="card-chip"></div>
                    <div class="card-number" id="cardPreview">•••• •••• •••• ••••</div>
                    <div class="card-info">
                        <div>
                            <div class="card-label">Titular do cartão</div>
                            <div id="namePreview">SEU NOME AQUI</div>
                        </div>
                        <div>
                            <div class="card-label">Validade</div>
                            <div id="expiryPreview">MM/AA</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form id="paymentForm">
                <div class="form-section">
                    <h3 class="section-title">Dados do Cartão</h3>
                    
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
                                <i class="fas fa-lock"></i>Código de Segurança
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
                        <i class="fas fa-check-circle"></i> Finalizar Pagamento
                    </button>
                    
                    <button type="button" class="btn btn-outline" onclick="window.location.href='paginaprodutos.php'">
                        <i class="fas fa-arrow-left"></i> Continuar Comprando
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="footer">
        <div class="footer-content">
            LAVELLE &copy; 2025 - Elegância e sofisticação em cada fragrância
        </div>
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
                showValidationError('cardCvv', 'Por favor, insira o código de segurança.');
                return;
            }
            
            // Mostrar confirmação com SweetAlert2
            const result = await Swal.fire({
                title: 'Confirmar Pagamento?',
                html: `
                    <div style="text-align: center; padding: 20px;">
                        <i class="fas fa-credit-card" style="font-size: 48px; color: #8b7355; margin-bottom: 20px;"></i>
                        <p style="margin-bottom: 10px;"><strong>Valor:</strong> R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></p>
                        <p style="margin-bottom: 10px;"><strong>Cartão:</strong> **** **** **** ${cardNumber.slice(-4)}</p>
                        <p style="margin-bottom: 10px;"><strong>Tipo:</strong> <?php echo $tipo_nome; ?></p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Confirmar Pagamento',
                cancelButtonText: 'Revisar Dados',
                confirmButtonColor: '#8b7355',
                cancelButtonColor: '#6c757d',
                customClass: {
                    popup: 'custom-swal'
                }
            });
            
            if (!result.isConfirmed) {
                return;
            }
            
            // Mostrar loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            // Simular processamento
            setTimeout(() => {
                document.getElementById('loadingOverlay').style.display = 'none';
                
                // Mostrar sucesso com SweetAlert2
                Swal.fire({
                    title: 'Pagamento Confirmado!',
                    html: `
                        <div style="text-align: center; padding: 20px;">
                            <i class="fas fa-check-circle" style="font-size: 64px; color: #32b572; margin-bottom: 20px;"></i>
                            <h3 style="color: #32b572; margin-bottom: 15px;">Pagamento Processado com Sucesso!</h3>
                            <p style="margin-bottom: 10px;">Obrigado pela sua compra na LAVELLE.</p>
                            <p style="font-size: 14px; color: #666;">Você receberá um e-mail de confirmação em instantes.</p>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'Continuar',
                    confirmButtonColor: '#8b7355',
                    customClass: {
                        popup: 'custom-swal'
                    }
                }).then(() => {
                    // Redirecionar após confirmação
                    window.location.href = 'paginaprodutos.php?pagamento=sucesso';
                });
            }, 3000);
        }
        
        function showValidationError(fieldId, message) {
            const field = document.getElementById(fieldId);
            field.style.borderColor = '#e74c3c';
            field.focus();
            
            // Criar mensagem de erro temporária
            const errorDiv = document.createElement('div');
            errorDiv.style.color = '#e74c3c';
            errorDiv.style.fontSize = '14px';
            errorDiv.style.marginTop = '8px';
            errorDiv.style.fontWeight = '500';
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
                this.style.borderColor = '#e8dfd1';
                const error = this.parentNode.querySelector('.error-message');
                if (error) error.remove();
            }, { once: true });
        }
        
        <?php if ($tipo_cartao == 'credito'): ?>
        // Gerar opções de parcelamento apenas para crédito
        document.addEventListener('DOMContentLoaded', function() {
            generateInstallments();
        });
        <?php endif; ?>
    </script>
</body>
</html>