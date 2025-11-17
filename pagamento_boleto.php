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

// Gerar data de vencimento (2 dias úteis a partir de hoje)
$vencimento = date('d/m/Y', strtotime('+2 weekdays'));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Boleto - LAVELLE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
        
        .warning {
            background: #fff9e6;
            border: 1px solid #ffd166;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            display: flex;
            align-items: center;
        }
        
        .warning i {
            font-size: 24px;
            color: #e67c22;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .warning-text {
            font-size: 15px;
            color: #8a6d3b;
        }
        
        .boleto-preview {
            border: 1px solid #e0d5c3;
            border-radius: 10px;
            padding: 30px;
            margin: 25px 0;
            background: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .boleto-header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #e0d5c3;
        }
        
        .boleto-icon {
            font-size: 48px;
            color: #8b7355;
            margin-bottom: 10px;
        }
        
        .boleto-title {
            font-size: 20px;
            font-weight: 600;
            color: #000;
        }
        
        .boleto-line {
            font-family: monospace;
            font-size: 16px;
            letter-spacing: 1px;
            margin: 12px 0;
            word-break: break-all;
            text-align: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
        }
        
        .barcode-section {
            text-align: center;
            margin: 25px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .barcode-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .boleto-info {
            background: #e8f5e8;
            padding: 25px;
            border-radius: 8px;
            margin: 25px 0;
            border-left: 4px solid #32b572;
        }
        
        .info-item {
            display: flex;
            justify-content: space-between;
            margin: 12px 0;
            padding: 10px 0;
            border-bottom: 1px solid #d4edda;
        }
        
        .info-label {
            font-weight: 600;
            color: #2d5016;
        }
        
        .info-value {
            color: #155724;
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
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            display: none;
            border-left: 4px solid #28a745;
            align-items: center;
        }
        
        .success-message i {
            margin-right: 10px;
            font-size: 20px;
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
            
            .boleto-preview {
                padding: 20px;
            }
            
            .boleto-line {
                font-size: 14px;
                letter-spacing: 0.5px;
            }
            
            .info-item {
                flex-direction: column;
                gap: 5px;
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
            <h1>Pagamento via Boleto</h1>
        </div>
        
        <div class="payment-container">
            <div class="payment-header">
                <div class="payment-icon">
                    <i class="fas fa-barcode"></i>
                </div>
                <div class="payment-title">Pagamento com Boleto</div>
            </div>
            
            <div class="amount-container">
                <div class="amount-label">Valor total do boleto:</div>
                <div class="amount">R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></div>
            </div>
            
            <div class="warning">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="warning-text">
                    O boleto pode levar até 3 dias úteis para ser compensado após o pagamento.
                </div>
            </div>
            
            <div class="boleto-preview">
                <div class="boleto-header">
                    <div class="boleto-icon">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="boleto-title">BOLETO BANCÁRIO</div>
                </div>
                
                <div class="boleto-line">
                    34191.79001 01043.510047 91020.150008 8 884100000<?php echo number_format($total_compra, 2, '', ''); ?>
                </div>
                
                <div class="barcode-section">
                    <div class="barcode-label">Código de barras</div>
                    <div class="boleto-line" style="font-size: 14px; background: white;">
                        34198884100000<?php echo number_format($total_compra, 2, '', ''); ?>91790010104351004791020150008
                    </div>
                </div>
            </div>
            
            <div class="boleto-info">
                <div class="info-item">
                    <span class="info-label">Beneficiário:</span>
                    <span class="info-value">LAVELLE PERFUMES LTDA</span>
                </div>
                <div class="info-item">
                    <span class="info-label">CNPJ:</span>
                    <span class="info-value">12.345.678/0001-90</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Vencimento:</span>
                    <span class="info-value"><?php echo $vencimento; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Valor:</span>
                    <span class="info-value">R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></span>
                </div>
            </div>
            
            <div class="btn-container">
                <button class="btn" onclick="printBoleto()">
                    <i class="fas fa-print"></i> Imprimir Boleto
                </button>
                <button class="btn" onclick="copyBarcode()">
                    <i class="far fa-copy"></i> Copiar Código
                </button>
                <button class="btn" onclick="simulatePayment()">
                    <i class="fas fa-check"></i> Confirmar Pagamento
                </button>
            </div>
            
            <div class="instructions">
                <h3><i class="fas fa-info-circle"></i> Como pagar:</h3>
                <ol>
                    <li>Imprima o boleto ou copie o código de barras</li>
                    <li>Pague em qualquer banco, lotérica ou internet banking</li>
                    <li>Guarde o comprovante de pagamento</li>
                    <li>Seu pedido será processado após a confirmação do pagamento</li>
                </ol>
            </div>
            
            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i> Boleto gerado com sucesso! Aguarde a confirmação do pagamento.
            </div>
            
            <div class="btn-container">
                <button class="btn btn-outline" onclick="window.location.href='paginaprodutos.php'">
                    <i class="fas fa-arrow-left"></i> Voltar aos Produtos
                </button>
            </div>
        </div>
    </div>

    <div class="footer">
        LAVELLE &copy; 2025 - Todos os direitos reservados
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script>
        function printBoleto() {
            // Feedback visual
            const printBtn = document.querySelector('.btn:nth-child(1)');
            const originalText = printBtn.innerHTML;
            printBtn.innerHTML = '<i class="fas fa-check"></i> Boleto Impresso!';
            printBtn.style.background = '#32b572';
            
            // SweetAlert2 para confirmação de impressão
            Swal.fire({
                title: 'Boleto Impresso!',
                text: 'Em um ambiente real, esta função abriria o boleto em PDF.',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#32b572'
            }).then(() => {
                printBtn.innerHTML = originalText;
                printBtn.style.background = '';
            });
        }
        
        function copyBarcode() {
            const barcode = '34198884100000<?php echo number_format($total_compra, 2, '', ''); ?>91790010104351004791020150008';
            navigator.clipboard.writeText(barcode).then(() => {
                // Feedback visual
                const copyBtn = document.querySelector('.btn:nth-child(2)');
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fas fa-check"></i> Código Copiado!';
                copyBtn.style.background = '#32b572';
                
                // SweetAlert2 para confirmação de cópia
                Swal.fire({
                    title: 'Código Copiado!',
                    text: 'O código de barras foi copiado para a área de transferência.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    copyBtn.innerHTML = originalText;
                    copyBtn.style.background = '';
                });
                
                setTimeout(() => {
                    copyBtn.innerHTML = originalText;
                    copyBtn.style.background = '';
                }, 2000);
            }).catch(err => {
                Swal.fire({
                    title: 'Erro',
                    text: 'Erro ao copiar o código. Tente novamente.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }
        
        function simulatePayment() {
            // SweetAlert2 para confirmação de pagamento
            Swal.fire({
                title: 'Confirmar Pagamento?',
                text: `Você está prestes a confirmar o pagamento de R$ <?php echo number_format($total_compra, 2, ',', '.'); ?>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, Confirmar Pagamento',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#32b572',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar mensagem de sucesso
                    document.getElementById('successMessage').style.display = 'flex';
                    
                    // Desabilitar botões após pagamento
                    document.querySelectorAll('.btn').forEach(btn => {
                        if (!btn.classList.contains('btn-outline')) {
                            btn.disabled = true;
                            btn.style.opacity = '0.6';
                            btn.style.cursor = 'not-allowed';
                        }
                    });
                    
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
                }
            });
        }
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
    </script>
</body>
</html>