<?php
session_start();
include 'conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// Verificar se existe carrinho e total
if (!isset($_SESSION['total_compra']) || !isset($_SESSION['produtos_carrinho']) || empty($_SESSION['produtos_carrinho'])) {
    header('Location: paginaprodutos.php');
    exit();
}

// Verificar se há endereço de entrega
if (!isset($_SESSION['endereco_entrega'])) {
    header('Location: endereco_entrega.php');
    exit();
}

// Obter o total da compra
$total_compra = $_SESSION['total_compra'];

// Verificar se o total é válido
if ($total_compra <= 0) {
    $total_compra = 0;
    if (isset($_SESSION['produtos_carrinho'])) {
        foreach ($_SESSION['produtos_carrinho'] as $item) {
            $total_compra += $item['subtotal'];
        }
    }
    
    if ($total_compra <= 0) {
        header('Location: paginaprodutos.php');
        exit();
    }
}

// Gerar data de vencimento (2 dias úteis a partir de hoje)
$vencimento = date('d/m/Y', strtotime('+2 weekdays'));

// Gerar código do boleto (simulação)
$codigo_barras = '34191.79001 01043.510047 91020.150008 8 884100000' . str_pad(number_format($total_compra, 2, '', ''), 11, '0', STR_PAD_LEFT);
$linha_digitavel = '34198884100000' . str_pad(number_format($total_compra, 2, '', ''), 11, '0', STR_PAD_LEFT) . '91790010104351004791020150008';

// Processar confirmação de pagamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_pagamento'])) {
    if (processarPedidoAposPagamento($con, 'boleto')) {
        // Limpar dados da sessão relacionados ao pedido atual
        unset($_SESSION['carrinho']);
        unset($_SESSION['produtos_carrinho']);
        unset($_SESSION['endereco_entrega']);
        unset($_SESSION['total_compra']);
        unset($_SESSION['itens_carrinho']);
        unset($_SESSION['metodo_pagamento']);
        
        echo json_encode(['success' => true, 'message' => 'Boleto gerado com sucesso!']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao processar pedido.']);
        exit;
    }
}

function processarPedidoAposPagamento($db, $metodo_pagamento) {
    try {
        // Preparar endereço para salvar no banco
        $endereco_entrega = $_SESSION['endereco_entrega'];
        $endereco_completo = $endereco_entrega['logradouro'] . ', ' . $endereco_entrega['numero'];
        if (!empty($endereco_entrega['complemento'])) {
            $endereco_completo .= ' - ' . $endereco_entrega['complemento'];
        }
        $endereco_completo .= ' - ' . $endereco_entrega['bairro'] . ' - ' . $endereco_entrega['cidade'] . '/' . $endereco_entrega['estado'] . ' - CEP: ' . $endereco_entrega['cep'];
        
        // Inserir pedido no banco de dados - STATUS COMO "pendente"
        $stmt = $db->prepare("
            INSERT INTO pedidos (usuario_id, data_pedido, total, status, metodo_pagamento, endereco_entrega) 
            VALUES (?, NOW(), ?, 'pendente', ?, ?)
        ");
        
        $stmt->execute([
            $_SESSION['id'],
            $_SESSION['total_compra'],
            $metodo_pagamento,
            $endereco_completo
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
        
        return $pedido_id;
        
    } catch(PDOException $e) {
        error_log("Erro ao salvar pedido: " . $e->getMessage());
        return false;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento Boleto - LAVELLE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .boleto-line:hover {
            background: #e9ecef;
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
            text-decoration: none;
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
        
        .btn-download {
            background: #8b7355;
        }
        
        .btn-download:hover {
            background: #7a6347;
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
                
                <div class="boleto-line" onclick="copyToClipboard('<?php echo $codigo_barras; ?>', 'Linha digitável')">
                    <?php echo $codigo_barras; ?>
                </div>
                
                <div class="barcode-section">
                    <div class="barcode-label">Código de barras</div>
                    <div class="boleto-line" onclick="copyToClipboard('<?php echo $linha_digitavel; ?>', 'Código de barras')">
                        <?php echo $linha_digitavel; ?>
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
                <button class="btn btn-download" onclick="downloadBoleto()">
                    <i class="fas fa-download"></i> Baixar Boleto PDF
                </button>
                <button class="btn" onclick="copyToClipboard('<?php echo $codigo_barras; ?>', 'Linha digitável')">
                    <i class="far fa-copy"></i> Copiar Código
                </button>
                <button class="btn" onclick="finalizarCompra()">
                    <i class="fas fa-check"></i> Gerar Boleto
                </button>
            </div>
            
            <div class="instructions">
                <h3><i class="fas fa-info-circle"></i> Como pagar:</h3>
                <ol>
                    <li>Baixe o boleto em PDF ou copie o código</li>
                    <li>Pague em qualquer banco, lotérica ou internet banking</li>
                    <li>Guarde o comprovante de pagamento</li>
                    <li>Seu pedido será processado após a confirmação do pagamento</li>
                </ol>
            </div>
            
            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i> Boleto gerado com sucesso! Aguarde a confirmação do pagamento.
            </div>
            
            <div class="btn-container">
                <a href="paginaprodutos.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Voltar aos Produtos
                </a>
                <a href="index.php" class="btn btn-outline">
                    <i class="fas fa-home"></i> Página Inicial
                </a>
            </div>
        </div>
    </div>

    <div class="footer">
        LAVELLE &copy; 2025 - Todos os direitos reservados
    </div>

    <script>
        // Função para copiar texto para a área de transferência
        function copyToClipboard(text, type) {
            navigator.clipboard.writeText(text).then(() => {
                Swal.fire({
                    title: 'Copiado!',
                    text: `${type} copiado para a área de transferência.`,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }).catch(err => {
                Swal.fire({
                    title: 'Erro',
                    text: 'Erro ao copiar. Tente novamente.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }

        // Função para baixar boleto em PDF
        function downloadBoleto() {
            Swal.fire({
                title: 'Gerar Boleto PDF',
                text: 'Deseja gerar e baixar o boleto em PDF?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, Baixar PDF',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#8b7355'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Gerando PDF...',
                        text: 'Preparando seu boleto para download',
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Simular download do PDF
                    setTimeout(() => {
                        Swal.close();
                        Swal.fire({
                            title: 'PDF Gerado!',
                            text: 'O boleto em PDF foi baixado com sucesso.',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#32b572'
                        });
                    }, 1500);
                }
            });
        }

        // Função para finalizar compra
        function finalizarCompra() {
            Swal.fire({
                title: 'Gerar Boleto',
                html: `
                    <div style="text-align: left;">
                        <p><strong>Resumo do Pedido:</strong></p>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;">
                            <p><strong>Valor:</strong> R$ <?php echo number_format($total_compra, 2, ',', '.'); ?></p>
                            <p><strong>Método:</strong> Boleto Bancário</p>
                            <p><strong>Vencimento:</strong> <?php echo $vencimento; ?></p>
                            <p><strong>Status:</strong> <span style="color: #e67c22;">Pendente</span></p>
                        </div>
                        <p style="color: #666; font-size: 14px;">
                            <i class="fas fa-info-circle"></i> 
                            Seu pedido será registrado com status "Pendente" e será processado após a confirmação do pagamento.
                        </p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Gerar Boleto',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#32b572',
                cancelButtonColor: '#d33',
                width: '500px'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mostrar loading
                    Swal.fire({
                        title: 'Processando...',
                        text: 'Gerando seu boleto e registrando pedido',
                        icon: 'info',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Enviar requisição para o servidor
                    fetch('pagamento_boleto.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'confirmar_pagamento=true'
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();
                        
                        if (data.success) {
                            document.getElementById('successMessage').style.display = 'flex';
                            
                            Swal.fire({
                                title: 'Boleto Gerado!',
                                html: `
                                    <div style="text-align: center;">
                                        <i class="fas fa-check-circle" style="font-size: 48px; color: #32b572; margin-bottom: 20px;"></i>
                                        <p><strong>Seu pedido foi registrado com sucesso!</strong></p>
                                        <p style="color: #666; margin: 10px 0;">
                                            Boleto gerado: <strong><?php echo $codigo_barras; ?></strong>
                                        </p>
                                        <div style="background: #e8f5e8; padding: 15px; border-radius: 8px; margin: 15px 0;">
                                            <p style="margin: 5px 0;">Você receberá um e-mail com os detalhes</p>
                                            <p style="margin: 5px 0;">Seu pedido será processado após a confirmação do pagamento</p>
                                            <p style="margin: 5px 0; color: #e67c22;"><strong>Status atual: Pendente</strong></p>
                                        </div>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonText: 'Você será redirecionado para a homepage',
                                confirmButtonColor: '#8b7355'
                            }).then(() => {
                                window.location.href = 'index.php';
                            });
                        } else {
                            Swal.fire({
                                title: 'Erro ao Gerar Boleto',
                                text: data.message || 'Ocorreu um erro ao processar seu pedido.',
                                icon: 'error',
                                confirmButtonColor: '#8b7355'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        console.error('Erro:', error);
                        Swal.fire({
                            title: 'Erro de Conexão',
                            text: 'Não foi possível processar o pedido. Tente novamente.',
                            icon: 'error',
                            confirmButtonColor: '#8b7355'
                        });
                    });
                }
            });
        }

        // Adicionar evento de clique nas linhas do boleto para copiar
        document.addEventListener('DOMContentLoaded', function() {
            const boletoLines = document.querySelectorAll('.boleto-line');
            boletoLines.forEach(line => {
                line.style.cursor = 'pointer';
                line.title = 'Clique para copiar';
            });
        });
    </script>
</body>
</html>