<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante do Pedido #019</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }
        .comprovante {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #8b7355;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #8b7355;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .numero-pedido {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .info-section {
            margin-bottom: 25px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .info-section h2 {
            color: #8b7355;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item {
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 13px;
        }
        .info-value {
            color: #333;
            font-size: 14px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .table th {
            background: #8b7355;
            color: white;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        .table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }
        .table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px solid #8b7355;
        }
        .total-line {
            font-size: 16px;
            margin-bottom: 8px;
        }
        .total-final {
            font-size: 20px;
            font-weight: bold;
            color: #8b7355;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pendente { background: #fff3cd; color: #856404; }
        .status-confirmado { background: #d1ecf1; color: #0c5460; }
        .status-enviado { background: #d4edda; color: #155724; }
        .status-entregue { background: #e2e3e5; color: #383d41; }
        .status-cancelado { background: #f8d7da; color: #721c24; }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
        }
        @media print {
            body { background: white; padding: 0; }
            .comprovante { box-shadow: none; padding: 0; }
            .no-print { display: none; }
        }
        .actions {
            text-align: center;
            margin: 20px 0;
        }
        .btn-print {
            background: #8b7355;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
        .btn-voltar {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="comprovante">
        <div class="header">
            <h1> COMPROVANTE DE PEDIDO</h1>
            <div class="numero-pedido">Pedido #019</div>
            <div class="status-badge status-confirmado">
                Confirmado            </div>
        </div>

        <div class="info-grid">
            <div class="info-section">
                <h2> Informações do Pedido</h2>
                <div class="info-item">
                    <div class="info-label">Data do Pedido:</div>
                    <div class="info-value">24/11/2025 15:52</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Método de Pagamento:</div>
                    <div class="info-value">Pix</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status:</div>
                    <div class="info-value status-badge status-confirmado">
                        Confirmado                    </div>
                </div>
            </div>

            <div class="info-section">
                <h2> Dados do Cliente</h2>
                <div class="info-item">
                    <div class="info-label">Nome:</div>
                    <div class="info-value">ADM LAVELLE PERFUMES</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email:</div>
                    <div class="info-value">admlavelle@gmail.com</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Telefone:</div>
                    <div class="info-value">(12) 99733-3349</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Endereço:</div>
                    <div class="info-value">Rua Essio Lanfredi, 1 - Parque Residencial Maria Elmira</div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h2> Itens do Pedido</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Quantidade</th>
                        <th>Preço Unitário</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                                        <tr>
                        <td>Lavelle Espírito Livre</td>
                        <td>1</td>
                        <td>R$ 299,99</td>
                        <td>R$ 299,99</td>
                    </tr>
                                    </tbody>
            </table>
        </div>

        <div class="total-section">
            <div class="total-line">
                <strong>Total do Pedido: R$ 299,99</strong>
            </div>
        </div>

        <div class="actions no-print">
            <button class="btn-print" onclick="window.print()"> Imprimir Comprovante</button>
            
        </div>

        <div class="footer">
            <p>Comprovante gerado automaticamente em 28/11/2025 às 20:06</p>
            <p>Obrigado pela preferência! </p>
        </div>
    </div>

    <script>
        // Auto-print se necessário
        // window.print();
    </script>
</body>
</html>
        