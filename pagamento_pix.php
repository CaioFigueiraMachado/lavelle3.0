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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            text-align: center;
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
            color: #32b572;
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
            border-left: 4px solid #32b572;
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
        
        .qr-section {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin: 30px 0;
        }
        
        .qr-container {
            flex: 1;
            min-width: 250px;
            max-width: 300px;
        }
        
        .qr-code {
            width: 100%;
            height: auto;
            border: 1px solid #e0d5c3;
            border-radius: 8px;
            padding: 15px;
            background: white;
        }
        
        .code-container {
            flex: 1;
            min-width: 300px;
        }
        
        .pix-code {
            background: #f8f9fa;
            border: 1px solid #e0d5c3;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            font-family: monospace;
            font-size: 16px;
            word-break: break-all;
            text-align: center;
            position: relative;
        }
        
        .copy-btn {
            background: #8b7355;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }
        
        .copy-btn i {
            margin-right: 8px;
        }
        
        .copy-btn:hover {
            background: #7a6347;
        }
        
        .timer-container {
            background: #fff9e6;
            border: 1px solid #ffd166;
            border-radius: 8px;
            padding: 15px;
            margin: 25px 0;
        }
        
        .timer {
            font-size: 18px;
            font-weight: 600;
            color: #e67c22;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .timer i {
            margin-right: 10px;
            font-size: 20px;
        }
        
        .instructions {
            text-align: left;
            margin: 30px 0;
            padding: 25px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 4px solid #8b7355;
        }
        
        .instructions h3 {
            color: #000;
            margin-bottom: 15px;
            font-size: 18px;
            display: flex;
            align-items: center;
        }
        
        .instructions h3 i {
            margin-right: 10px;
            color: #8b7355;
        }
        
        .instructions ol {
            margin-left: 20px;
        }
        
        .instructions li {
            margin-bottom: 12px;
            padding-left: 5px;
        }
        
        .btn-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn {
            background: #32b572;
            color: white;
            padding: 15px 30px;
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
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            display: none;
            border-left: 4px solid #28a745;
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
            
            .qr-section {
                flex-direction: column;
            }
            
            .qr-container, .code-container {
                max-width: 100%;
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
    <div class="container">
        <div class="header">
            <div class="logo">LAVELLE</div>
            <div class="tagline">ELEGÂNCIA E SOFISTICAÇÃO</div>
            <h1>Pagamento via PIX</h1>
        </div>
        
        <div class="payment-container">
            <div class="payment-header">
                <div class="payment-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div class="payment-title">Pagamento PIX</div>
            </div>
            
            <p>Escaneie o QR Code ou copie o código para pagar</p>
            
            <div class="amount-container">
                <div class="amount-label">Valor total:</div>
                <div class="amount">R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></div>
            </div>
            
            <div class="qr-section">
                <div class="qr-container">
                    <div class="qr-title">QR Code para pagamento</div>
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=00020126580014br.gov.bcb.pix0136c8b7a1e2-d3f4-5g6h-7i8j-9k0l1m2n3o4p520400005303986540<?php echo number_format($total_compra, 2, '', ''); ?>5802BR5913LAVELLE STORE6008SAO PAULO62070503***6304A1B2" alt="QR Code PIX" class="qr-code">
                </div>
                
                <div class="code-container">
                    <div class="code-title">Código PIX (copiar e colar)</div>
                    <div class="pix-code">
                        00020126580014br.gov.bcb.pix0136c8b7a1e2-d3f4-5g6h-7i8j-9k0l1m2n3o4p520400005303986540<?php echo number_format($total_compra, 2, '', ''); ?>5802BR5913LAVELLE STORE6008SAO PAULO62070503***6304A1B2
                    </div>
                    <button class="copy-btn" onclick="copyPixCode()">
                        <i class="far fa-copy"></i> Copiar Código PIX
                    </button>
                </div>
            </div>
            
            <div class="timer-container">
                <div class="timer" id="timer">
                    <i class="fas fa-clock"></i> Tempo restante: 29:59
                </div>
            </div>
            
            <div class="instructions">
                <h3><i class="fas fa-info-circle"></i> Como pagar:</h3>
                <ol>
                    <li>Abra o app do seu banco ou instituição financeira</li>
                    <li>Selecione a opção PIX</li>
                    <li>Escaneie o QR Code ou cole o código copiado</li>
                    <li>Confirme os dados e realize o pagamento</li>
                    <li>Aguarde a confirmação automática</li>
                </ol>
            </div>
            
            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i> Pagamento confirmado! Obrigado pela sua compra.
            </div>
            
            <div class="btn-container">
                <button class="btn" onclick="simulatePayment()">
                    <i class="fas fa-check"></i> Simular Pagamento
                </button>
                <button class="btn btn-outline" onclick="window.location.href='paginaprodutos.php'">
                    <i class="fas fa-arrow-left"></i> Voltar aos Produtos
                </button>
            </div>
        </div>
    </div>

    <div class="footer">
        LAVELLE &copy; 2023 - Todos os direitos reservados
    </div>

    <script>
        let timeLeft = 30 * 60; // 30 minutos em segundos
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            document.getElementById('timer').innerHTML = 
                `<i class="fas fa-clock"></i> Tempo restante: ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft > 0) {
                timeLeft--;
                setTimeout(updateTimer, 1000);
            } else {
                document.getElementById('timer').innerHTML = '<i class="fas fa-exclamation-triangle"></i> Tempo esgotado! Por favor, inicie um novo pagamento.';
                document.getElementById('timer').style.color = '#e74c3c';
            }
        }
        
        function copyPixCode() {
            const pixCode = document.querySelector('.pix-code').textContent;
            navigator.clipboard.writeText(pixCode).then(() => {
                // Feedback visual
                const copyBtn = document.querySelector('.copy-btn');
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fas fa-check"></i> Código Copiado!';
                copyBtn.style.background = '#32b572';
                
                setTimeout(() => {
                    copyBtn.innerHTML = originalText;
                    copyBtn.style.background = '#8b7355';
                }, 2000);
            }).catch(err => {
                alert('Erro ao copiar o código. Tente novamente.');
            });
        }
        
        function simulatePayment() {
            document.getElementById('successMessage').style.display = 'block';
            document.getElementById('timer').innerHTML = '<i class="fas fa-check-circle"></i> Pagamento confirmado!';
            document.getElementById('timer').style.color = '#32b572';
            
            // Desabilitar botões após pagamento
            document.querySelectorAll('.btn').forEach(btn => {
                btn.disabled = true;
                btn.style.opacity = '0.6';
                btn.style.cursor = 'not-allowed';
            });
            
            setTimeout(() => {
                alert('Pagamento confirmado! Obrigado pela preferência.');
                // Redirecionar para index.php após o alerta
                window.location.href = 'index.php';
            }, 500);
        }
        
        // Iniciar timer
        updateTimer();
    </script>
</body>
</html>