<?php
session_start();



$total_compra = $_SESSION['total_compra'];
// Gerar data de vencimento (2 dias úteis a partir de hoje)
$vencimento = date('d/m/Y', strtotime('+2 weekdays'));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Boleto - LAVELLE</title>
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
        
        .boleto-preview {
            border: 2px dashed #8b7355;
            border-radius: 10px;
            padding: 30px;
            margin: 20px 0;
            background: #f8f9fa;
        }
        
        .boleto-line {
            font-family: monospace;
            font-size: 16px;
            letter-spacing: 1px;
            margin: 10px 0;
            word-break: break-all;
        }
        
        .amount {
            font-size: 28px;
            font-weight: bold;
            color: #27ae60;
            margin: 20px 0;
            text-align: center;
        }
        
        .boleto-info {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
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
            margin: 10px 5px;
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
        
        .instructions {
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
        
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
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
            <h1>Pagamento via Boleto</h1>
        </div>
        
        <div class="payment-container">
            <div class="amount">Total: R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></div>
            
            <div class="warning">
                ⚠️ O boleto pode levar até 3 dias úteis para ser compensado após o pagamento.
            </div>
            
            <div class="boleto-preview">
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="font-size: 48px;">📄</div>
                    <h3>BOLETO BANCÁRIO</h3>
                </div>
                
                <div class="boleto-line">
                    34191.79001 01043.510047 91020.150008 8 884100000<?php echo number_format($total_compra, 2, '', ''); ?>
                </div>
                
                <div style="text-align: center; margin: 20px 0;">
                    <div style="font-size: 12px; color: #666;">Código de barras</div>
                    <div class="boleto-line" style="font-size: 14px;">
                        34198884100000<?php echo number_format($total_compra, 2, '', ''); ?>91790010104351004791020150008
                    </div>
                </div>
            </div>
            
            <div class="boleto-info">
                <div class="info-item">
                    <span>Beneficiário:</span>
                    <span>LAVELLE PERFUMES LTDA</span>
                </div>
                <div class="info-item">
                    <span>CNPJ:</span>
                    <span>12.345.678/0001-90</span>
                </div>
                <div class="info-item">
                    <span>Vencimento:</span>
                    <span><?php echo $vencimento; ?></span>
                </div>
                <div class="info-item">
                    <span>Valor:</span>
                    <span>R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></span>
                </div>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                
                <button class="btn" onclick="copyBarcode()">📋 Copiar Código</button>
                <button class="btn" onclick="simulatePayment()">Confirmara Pagamento</button>
            </div>
            
            <div class="instructions">
                <h3>Como pagar:</h3>
                <ol>
                    <li>Imprima o boleto ou copie o código de barras</li>
                    <li>Pague em qualquer banco, lotérica ou internet banking</li>
                    <li>Guarde o comprovante de pagamento</li>
                    <li>Seu pedido será processado após a confirmação do pagamento</li>
                </ol>
            </div>
            
            <div class="success-message" id="successMessage">
                ✅ Boleto gerado com sucesso! Aguarde a confirmação do pagamento.
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <button class="btn btn-outline" onclick="window.location.href='paginaprodutos.php'">
                    ← Voltar aos Produtos
                </button>
            </div>
        </div>
    </div>

    <script>
        function printBoleto() {
            alert('Boleto impresso com sucesso! Em um ambiente real, esta função abriria o boleto em PDF.');
        }
        
        function copyBarcode() {
            const barcode = '34198884100000<?php echo number_format($total_compra, 2, '', ''); ?>91790010104351004791020150008';
            navigator.clipboard.writeText(barcode).then(() => {
                alert('Código de barras copiado! Cole no seu internet banking.');
            });
        }
        
        function simulatePayment() {
            document.getElementById('successMessage').style.display = 'block';
            alert('Pagamento feito com sucesso! Obrigado pela preferencia.');
            
            setTimeout(() => {
                window.location.href = 'produtos.php?pagamento=sucesso';
            }, 2000);
        }
    </script>
</body>
</html>