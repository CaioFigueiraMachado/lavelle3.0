<?php
session_start();
require_once('conexao.php');
require_once('vendor/autoload.php'); // Se estiver usando Composer
// Inclua manualmente se não usar Composer

use Dompdf\Dompdf;
use Dompdf\Options;

// Verificar se foi solicitado um boleto específico
$pedido_id = isset($_GET['pedido_id']) ? intval($_GET['pedido_id']) : 0;

if ($pedido_id > 0) {
    gerarBoletoPDF($pedido_id, $con);
} else {
    // Gerar boleto para a sessão atual (pré-pedido)
    gerarBoletoSessao($con);
}

function gerarBoletoPDF($pedido_id, $db) {
    try {
        // Buscar dados do pedido
        $stmt = $db->prepare("
            SELECT p.*, u.nome, u.email, u.telefone 
            FROM pedidos p 
            LEFT JOIN usuarios u ON p.usuario_id = u.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$pedido_id]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pedido) {
            die('Pedido não encontrado');
        }
        
        // Buscar itens do pedido
        $stmt = $db->prepare("
            SELECT pi.*, pr.nome as produto_nome 
            FROM pedido_itens pi 
            LEFT JOIN produtos pr ON pi.produto_id = pr.id 
            WHERE pi.pedido_id = ?
        ");
        $stmt->execute([$pedido_id]);
        $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Gerar dados do boleto
        $dados_boleto = gerarDadosBoleto($pedido['total']);
        
        // Gerar HTML do boleto
        $html = gerarHTMLBoleto($pedido, $itens, $dados_boleto);
        
        // Configurar e gerar PDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Output do PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="boleto_lavelle_' . $pedido_id . '.pdf"');
        echo $dompdf->output();
        
    } catch (Exception $e) {
        die('Erro ao gerar boleto: ' . $e->getMessage());
    }
}

function gerarBoletoSessao($db) {
    if (!isset($_SESSION['total_compra']) || $_SESSION['total_compra'] <= 0) {
        die('Dados de pagamento não encontrados');
    }
    
    $total_compra = $_SESSION['total_compra'];
    $dados_boleto = gerarDadosBoleto($total_compra);
    
    // Dados simulados para pré-visualização
    $pedido = [
        'id' => 'PRÉ-VISUALIZAÇÃO',
        'total' => $total_compra,
        'data_pedido' => date('d/m/Y H:i:s'),
        'nome' => $_SESSION['nome'] ?? 'Cliente LAVELLE',
        'endereco_entrega' => isset($_SESSION['endereco_entrega']) ? 
            $_SESSION['endereco_entrega']['logradouro'] . ', ' . 
            $_SESSION['endereco_entrega']['numero'] . ' - ' . 
            $_SESSION['endereco_entrega']['bairro'] . ', ' . 
            $_SESSION['endereco_entrega']['cidade'] . '/' . 
            $_SESSION['endereco_entrega']['estado'] : 'Endereço não informado'
    ];
    
    $html = gerarHTMLBoleto($pedido, [], $dados_boleto);
    
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Arial');
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="boleto_lavelle_preview.pdf"');
    echo $dompdf->output();
}

function gerarDadosBoleto($valor) {
    $vencimento = date('d/m/Y', strtotime('+2 weekdays'));
    
    // Gerar números do boleto (simulação)
    $nosso_numero = str_pad(rand(100000, 999999), 8, '0', STR_PAD_LEFT);
    $codigo_barras_num = '34191.79001 01043.510047 91020.150008 8 ' . str_pad(number_format($valor, 2, '', ''), 11, '0', STR_PAD_LEFT);
    $linha_digitavel = '34191.79001 01043.510047 91020.150008 8 ' . str_pad(number_format($valor, 2, '', ''), 11, '0', STR_PAD_LEFT);
    
    return [
        'vencimento' => $vencimento,
        'valor' => number_format($valor, 2, ',', '.'),
        'valor_extenso' => valorPorExtenso($valor),
        'nosso_numero' => $nosso_numero,
        'codigo_barras' => $codigo_barras_num,
        'linha_digitavel' => $linha_digitavel,
        'cedente' => 'LAVELLE PERFUMES LTDA',
        'cnpj_cedente' => '12.345.678/0001-90',
        'agencia' => '1234',
        'conta' => '56789-0',
        'carteira' => '175',
        'especie' => 'R$',
        'quantidade' => '',
        'documento' => 'PD' . str_pad(rand(1000, 9999), 6, '0', STR_PAD_LEFT),
        'aceite' => 'N',
        'especie_doc' => 'DM'
    ];
}

function gerarHTMLBoleto($pedido, $itens, $dados) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Boleto - LAVELLE</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
            .container { max-width: 800px; margin: 0 auto; border: 2px solid #000; padding: 15px; }
            .header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #000; padding-bottom: 10px; }
            .logo { font-size: 24px; font-weight: bold; color: #8b7355; }
            .boleto-info { margin: 20px 0; }
            .linha-digitavel { 
                font-family: monospace; 
                font-size: 16px; 
                font-weight: bold; 
                text-align: center; 
                padding: 10px; 
                border: 1px solid #000; 
                margin: 10px 0;
                letter-spacing: 2px;
            }
            .detalhes { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 15px 0; }
            .campo { margin-bottom: 8px; }
            .label { font-weight: bold; font-size: 12px; }
            .valor { font-size: 12px; }
            .recibo { border: 1px solid #000; padding: 15px; margin: 20px 0; }
            .assinatura { margin-top: 50px; border-top: 1px solid #000; padding-top: 10px; text-align: center; }
            .codigo-barras { text-align: center; margin: 20px 0; font-family: monospace; }
            .instrucoes { font-size: 10px; margin-top: 20px; }
            table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            th, td { border: 1px solid #000; padding: 5px; font-size: 10px; text-align: left; }
            th { background: #f0f0f0; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="logo">LAVELLE</div>
                <div style="font-size: 12px; color: #666;">ELEGÂNCIA E SOFISTICAÇÃO</div>
            </div>
            
            <div class="recibo">
                <div style="text-align: center; font-weight: bold; margin-bottom: 15px;">RECIBO DO PAGADOR</div>
                
                <div class="linha-digitavel">' . $dados['linha_digitavel'] . '</div>
                
                <div class="detalhes">
                    <div>
                        <div class="campo">
                            <div class="label">Beneficiário</div>
                            <div class="valor">' . $dados['cedente'] . '</div>
                        </div>
                        <div class="campo">
                            <div class="label">CNPJ</div>
                            <div class="valor">' . $dados['cnpj_cedente'] . '</div>
                        </div>
                        <div class="campo">
                            <div class="label">Agência/Código do Beneficiário</div>
                            <div class="valor">' . $dados['agencia'] . ' / ' . $dados['conta'] . '</div>
                        </div>
                    </div>
                    <div>
                        <div class="campo">
                            <div class="label">Data do Documento</div>
                            <div class="valor">' . date('d/m/Y') . '</div>
                        </div>
                        <div class="campo">
                            <div class="label">Nº do Documento</div>
                            <div class="valor">' . $dados['documento'] . '</div>
                        </div>
                        <div class="campo">
                            <div class="label">Vencimento</div>
                            <div class="valor">' . $dados['vencimento'] . '</div>
                        </div>
                    </div>
                </div>
                
                <div class="detalhes">
                    <div>
                        <div class="campo">
                            <div class="label">Pagador</div>
                            <div class="valor">' . $pedido['nome'] . '</div>
                        </div>
                        <div class="campo">
                            <div class="label">Endereço</div>
                            <div class="valor">' . $pedido['endereco_entrega'] . '</div>
                        </div>
                    </div>
                    <div>
                        <div class="campo">
                            <div class="label">Valor do Documento</div>
                            <div class="valor" style="font-weight: bold; font-size: 14px;">R$ ' . $dados['valor'] . '</div>
                        </div>
                        <div class="campo">
                            <div class="label">(=) Valor Cobrado</div>
                            <div class="valor" style="font-weight: bold; font-size: 14px;">R$ ' . $dados['valor'] . '</div>
                        </div>
                    </div>
                </div>
                
                <div class="assinatura">
                    Assinatura do Pagador
                </div>
            </div>
            
            <div style="border-top: 2px dashed #000; margin: 20px 0;"></div>
            
            <div class="linha-digitavel">' . $dados['linha_digitavel'] . '</div>
            
            <div class="detalhes">
                <div>
                    <div class="campo">
                        <div class="label">Local de Pagamento</div>
                        <div class="valor">Pagar preferencialmente no Banco Itaú</div>
                    </div>
                    <div class="campo">
                        <div class="label">Beneficiário</div>
                        <div class="valor">' . $dados['cedente'] . '</div>
                    </div>
                    <div class="campo">
                        <div class="label">Data do Documento</div>
                        <div class="valor">' . date('d/m/Y') . '</div>
                    </div>
                    <div class="campo">
                        <div class="label">Nº do Documento</div>
                        <div class="valor">' . $dados['documento'] . '</div>
                    </div>
                    <div class="campo">
                        <div class="label">Espécie Doc.</div>
                        <div class="valor">' . $dados['especie_doc'] . '</div>
                    </div>
                    <div class="campo">
                        <div class="label">Aceite</div>
                        <div class="valor">' . $dados['aceite'] . '</div>
                    </div>
                </div>
                <div>
                    <div class="campo">
                        <div class="label">Vencimento</div>
                        <div class="valor">' . $dados['vencimento'] . '</div>
                    </div>
                    <div class="campo">
                        <div class="label">Agência/Código Beneficiário</div>
                        <div class="valor">' . $dados['agencia'] . ' / ' . $dados['conta'] . '</div>
                    </div>
                    <div class="campo">
                        <div class="label">Nosso Número</div>
                        <div class="valor">' . $dados['nosso_numero'] . '</div>
                    </div>
                    <div class="campo">
                        <div class="label">Valor do Documento</div>
                        <div class="valor">R$ ' . $dados['valor'] . '</div>
                    </div>
                    <div class="campo">
                        <div class="label">(-) Descontos/Abatimentos</div>
                        <div class="valor">R$ 0,00</div>
                    </div>
                    <div class="campo">
                        <div class="label">(=) Valor Cobrado</div>
                        <div class="valor" style="font-weight: bold;">R$ ' . $dados['valor'] . '</div>
                    </div>
                </div>
            </div>
            
            <div class="codigo-barras">
                <div style="font-weight: bold; margin-bottom: 10px;">Código de Barras</div>
                <div style="font-family: monospace; letter-spacing: 3px; font-size: 14px;">' . $dados['codigo_barras'] . '</div>
            </div>
            
            <div class="instrucoes">
                <p><strong>Instruções:</strong></p>
                <p>1. Pagável em qualquer banco até a data do vencimento</p>
                <p>2. Após o vencimento, pagar somente no Banco Itaú</p>
                <p>3. Não receber após 30 dias do vencimento</p>
                <p>4. Multa de 2% após o vencimento</p>
                <p>5. Mora de 1% ao mês</p>
            </div>
            
            <div style="margin-top: 20px; font-size: 10px; text-align: center;">
                Boleto gerado automaticamente por LAVELLE PERFUMES<br>
                Em caso de dúvidas, contate: sac@lavelle.com.br
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}

function valorPorExtenso($valor) {
    // Função simplificada para converter valor em extenso
    $valor = number_format($valor, 2, ',', '.');
    return $valor . ' (' . escreverValorPorExtenso($valor) . ')';
}

function escreverValorPorExtenso($valor) {
    // Implementação básica - em produção use uma biblioteca mais robusta
    return "valor por extenso";
}
?>