<?php
session_start();


$total_compra = $_SESSION['total_compra'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento PIX - LAVELLE</title>
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
            text-align: center;
        }
        
        .pix-code {
            background: #f8f9fa;
            border: 2px dashed #8b7355;
            border-radius: 10px;
            padding: 30px;
            margin: 30px 0;
            font-family: monospace;
            font-size: 18px;
            word-break: break-all;
        }
        
        .qr-code {
            width: 250px;
            height: 250px;
            background: #f5f5f5;
            border: 2px solid #8b7355;
            border-radius: 10px;
            margin: 20px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: #666;
        }
        
        .amount {
            font-size: 28px;
            font-weight: bold;
            color: #27ae60;
            margin: 20px 0;
        }
        
        .instructions {
            text-align: left;
            margin: 30px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .instructions ol {
            margin-left: 20px;
        }
        
        .instructions li {
            margin-bottom: 10px;
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
            margin: 10px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #219653;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid #8b7355;
            color: #8b7355;
        }
        
        .btn-outline:hover {
            background: #8b7355;
            color: white;
        }
        
        .timer {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;
            margin: 20px 0;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">LAVELLE</div>
            <h1>Pagamento via PIX</h1>
        </div>
        
        <div class="payment-container">
            <h2>💎 Pagamento PIX</h2>
            <p>Escaneie o QR Code ou copie o código para pagar</p>
            
            <div class="amount">Total: R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></div>
            
            
            
            <div class="pix-code">
                00020126580014br.gov.bcb.pix0136c8b7a1e2-d3f4-5g6h-7i8j-9k0l1m2n3o4p520400005303986540<?php echo number_format($total_compra, 2, '', ''); ?>5802BR5913LAVELLE STORE6008SAO PAULO62070503***6304A1B2
            </div>
            
            <button class="btn" onclick="copyPixCode()">📋 Copiar Código PIX</button>
            
            <div class="timer" id="timer">
                ⏰ Tempo restante: 29:59
            </div>
            
            <div class="instructions">
                <h3>Como pagar:</h3>
                <ol>
                    <li>Abra o app do seu banco</li>
                    <li>Selecione a opção PIX</li>
                    <li>Escaneie o QR Code ou cole o código</li>
                    <li>Confirme o pagamento</li>
                    <li>Aguarde a confirmação automática</li>
                </ol>
            </div>
            
            <div class="success-message" id="successMessage">
                ✅ Pagamento confirmado! Obrigado pela sua compra.
            </div>
            
            <div style="margin-top: 30px;">
                <button class="btn" onclick="simulatePayment()">Confirmar Pagamento</button>
                <button class="btn btn-outline" onclick="window.location.href='paginaprodutos.php'">← Voltar aos Produtos</button>
            </div>
        </div>
    </div>

    <script>
        let timeLeft = 30 * 60; // 30 minutos em segundos
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('timer').textContent = 
                `⏰ Tempo restante: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft > 0) {
                timeLeft--;
                setTimeout(updateTimer, 1000);
            } else {
                document.getElementById('timer').innerHTML = '❌ Tempo esgotado!<br>Por favor, inicie um novo pagamento.';
            }
        }
        
        function copyPixCode() {
            const pixCode = document.querySelector('.pix-code').textContent;
            navigator.clipboard.writeText(pixCode).then(() => {
                alert('Código PIX copiado! Cole no seu app bancário.');
            });
        }
        
        function simulatePayment() {
            document.getElementById('successMessage').style.display = 'block';
            document.getElementById('timer').innerHTML = '✅ Pagamento confirmado!';
            alert('Pagamento feito com sucesso! Obrigado pela preferencia.');
        }
        
        // Iniciar timer
        updateTimer();
    </script>
</body>
</html>