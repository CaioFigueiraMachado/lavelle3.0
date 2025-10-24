<?php
session_start();
$total_compra = $_SESSION['total_compra'];
$tipo_cartao = isset($_GET['tipo']) ? $_GET['tipo'] : 'credito';
$tipo_nome = $tipo_cartao == 'credito' ? 'Crédito' : 'Débito';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Cartão - LAVELLE</title>
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
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 20px 0;
            border-bottom: 2px solid #8b7355;
        }
        
        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #000;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        
        .payment-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #27ae60;
            margin: 20px 0;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
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
        
        .card-preview {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .card-number {
            font-size: 18px;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }
        
        .card-info {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
        }
        
        .btn {
            background: #27ae60;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #219653;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #8b7355;
            color: #8b7355;
            margin-top: 10px;
        }
        
        .btn-outline:hover {
            background: #8b7355;
            color: white;
        }
        
        .installments {
            margin: 20px 0;
        }
        
        .installment-option {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin: 5px 0;
            cursor: pointer;
        }
        
        .installment-option:hover {
            background: #f8f9fa;
        }
        
        .installment-option.selected {
            border-color: #27ae60;
            background: #f0f9f0;
        }
        
        .security-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">LAVELLE</div>
            <h1>Pagamento com Cartão</h1>
            <p>Cartão de <?php echo $tipo_nome; ?></p>
        </div>
        
        <div class="payment-container">
            <div class="amount">Total: R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></div>
            
            <div class="card-preview">
                <div class="card-number" id="cardPreview">**** **** **** ****</div>
                <div class="card-info">
                    <div id="namePreview">SEU NOME AQUI</div>
                    <div id="expiryPreview">MM/AA</div>
                </div>
            </div>
            
            <form id="paymentForm">
                <div class="form-group">
                    <label class="form-label">Número do Cartão</label>
                    <input type="text" class="form-input" id="cardNumber" 
                           placeholder="1234 5678 9012 3456" maxlength="19"
                           oninput="formatCardNumber(this)">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nome no Cartão</label>
                    <input type="text" class="form-input" id="cardName" 
                           placeholder="Como aparece no cartão"
                           oninput="document.getElementById('namePreview').textContent = this.value || 'SEU NOME AQUI'">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Validade</label>
                        <input type="text" class="form-input" id="cardExpiry" 
                               placeholder="MM/AA" maxlength="5"
                               oninput="formatExpiry(this)">
                    </div>
                    <div class="form-group">
                        <label class="form-label">CVV</label>
                        <input type="text" class="form-input" id="cardCvv" 
                               placeholder="123" maxlength="4">
                    </div>
                </div>
                
                <?php if ($tipo_cartao == 'credito'): ?>
                <div class="form-group installments">
                    <label class="form-label">Parcelamento</label>
                    <div id="installmentsOptions">
                        <!-- Opções de parcelamento serão geradas via JavaScript -->
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="security-info">
                    🔒 Pagamento 100% seguro via criptografia SSL
                </div>
                
                <button type="button" class="btn" onclick="processPayment()">
                    💳 Finalizar Pagamento
                </button>
                
                <button type="button" class="btn btn-outline" onclick="window.location.href='paginaprodutos.php'">
                    ← Voltar aos Produtos
                </button>
            </form>
        </div>
    </div>

    <script>
        function formatCardNumber(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/(\d{4})/g, '$1 ').trim();
            value = value.substring(0, 19);
            input.value = value;
            document.getElementById('cardPreview').textContent = value || '**** **** **** ****';
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
                option.innerHTML = `
                    <div>
                        <input type="radio" name="installment" value="${i}" ${i === 1 ? 'checked' : ''}>
                        <label>${i}x</label>
                    </div>
                    <div>R$ ${installmentValue.toFixed(2).replace('.', ',')}</div>
                `;
                option.onclick = () => {
                    document.querySelectorAll('.installment-option').forEach(el => el.classList.remove('selected'));
                    option.classList.add('selected');
                    option.querySelector('input').checked = true;
                };
                if (i === 1) option.classList.add('selected');
                container.appendChild(option);
            }
        }
        
        function processPayment() {
            const cardNumber = document.getElementById('cardNumber').value.replace(/\D/g, '');
            const cardName = document.getElementById('cardName').value;
            const cardExpiry = document.getElementById('cardExpiry').value;
            const cardCvv = document.getElementById('cardCvv').value;
            
            if (!cardNumber || cardNumber.length < 16) {
                alert('Por favor, insira um número de cartão válido.');
                return;
            }
            
            if (!cardName) {
                alert('Por favor, insira o nome no cartão.');
                return;
            }
            
            if (!cardExpiry || cardExpiry.length < 5) {
                alert('Por favor, insira a validade do cartão.');
                return;
            }
            
            if (!cardCvv || cardCvv.length < 3) {
                alert('Por favor, insira o CVV do cartão.');
                return;
            }
            
            setTimeout(() => {
    alert('✅ Pagamento processado com sucesso! Obrigado pela sua compra.');
    // Limpar carrinho após pagamento bem-sucedido
    window.location.href = 'paginaprodutos.php?pagamento=sucesso';
}, 2000);
        }
        
        <?php if ($tipo_cartao == 'credito'): ?>
        // Gerar opções de parcelamento apenas para crédito
        generateInstallments();
        <?php endif; ?>
    </script>
</body>
</html>