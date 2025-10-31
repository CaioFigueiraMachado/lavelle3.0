<?php
session_start();
include 'conexao.php';

// Inicializar carrinho se não existir
if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}

// CORREÇÃO: Verificar se usuário está logado - mesma verificação da index.php
$usuarioLogado = false;
$usuarioNome = "";

if (isset($_SESSION['id'])) {
    $usuarioLogado = true;
    $usuarioNome = $_SESSION['nome'];
}

// Buscar produtos do banco de dados
$produtos = [];
try {
    $database = new PDO("mysql:host=localhost;dbname=lavelle_perfumes", "root", "");
    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // ATUALIZADO: Buscar descricao_breve e descricao_longa
    $query = "SELECT id, nome, descricao_breve, descricao_longa, preco, imagem, categoria, created_at FROM produtos ORDER BY created_at DESC";
    $stmt = $database->prepare($query);
    $stmt->execute();
    $produtos_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Converter para o formato esperado pelo código existente
    foreach($produtos_db as $produto_db) {
        $produtos[] = [
            "id" => $produto_db['id'],
            "nome" => $produto_db['nome'],
            "categoria" => $produto_db['categoria'] ?? 'Compartilhável',
            "preco" => floatval($produto_db['preco']),
            "preco_formatado" => "R$ " . number_format($produto_db['preco'], 2, ',', '.'),
            // ATUALIZADO: Usar descricao_breve para o card e descricao_longa para o modal
            "descricao" => $produto_db['descricao_breve'] ?? $produto_db['descricao_longa'] ?? 'Descrição não disponível',
            "descricao_longa" => $produto_db['descricao_longa'] ?? $produto_db['descricao_breve'] ?? 'Descrição detalhada não disponível',
            "notas" => [
                "Notas de Saída: Bergamota, Laranja",
                "Notas de Coração: Jasmim, Rosa, Íris", 
                "Notas de Fundo: Baunilha, Âmbar, Musk"
            ],
            "badge" => "",
            "badge_class" => "",
            "imagem" => $produto_db['imagem']
        ];
    }
    
} catch(PDOException $e) {
    // Se houver erro, usar produtos padrão como fallback
    $produtos = [
        [
            "id" => 1,
            "nome" => "Lavelle Aureum",
            "categoria" => "Feminino",
            "preco" => 299.90,
            "preco_formatado" => "R$ 299,90",
            "descricao" => "Fragrância floral intensa com notas de jasmim e baunilha. Perfeita para ocasiões especiais.",
            "descricao_longa" => "A fragrância Lavelle Aureum é uma fragrância sofisticada que combina notas florais intensas com um toque sensual de baunilha. Desenvolvida para mulheres que buscam elegância e sofisticação, esta fragrância possui excelente fixação e projeção moderada, ideal para uso noturno e ocasiões especiais. Com uma duração de até 8 horas na pele, é a escolha perfeita para eventos formais e encontros românticos.",
            "notas" => [
                "Notas de Saída: Bergamota, Laranja",
                "Notas de Coração: Jasmim, Rosa, Íris",
                "Notas de Fundo: Baunilha, Âmbar, Musk"
            ],
            "badge" => "Novo",
            "badge_class" => "new",
            "imagem" => "lavelleaureum.jpg"
        ],
        [
            "id" => 2,
            "nome" => "Lavelle Intense Noir",
            "categoria" => "Masculino",
            "preco" => 349.90,
            "preco_formatado" => "R$ 349,90",
            "descricao" => "Perfume amadeirado com notas de sândalo e âmbar para o homem moderno.",
            "descricao_longa" => "Intense Noir é uma fragrância masculina que transmite confiança e sofisticação. Com notas amadeiradas e especiarias, é perfeita para o homem contemporâneo que valoriza qualidade e personalidade. Desenvolvido com ingredientes selecionados, oferece duração excepcional e um aroma marcante que evolui ao longo do dia.",
            "notas" => [
                "Notas de Saída: Cardamomo, Pimenta Preta",
                "Notas de Coração: Cedro, Sândalo",
                "Notas de Fundo: Âmbar, Couro, Musk"
            ],
            "badge" => "",
            "badge_class" => "",
            "imagem" => "intense.png"
        ],
        [
            "id" => 3,
            "nome" => "Lavelle Rose Sublime",
            "categoria" => "Feminino",
            "preco" => 279.90,
            "preco_formatado" => "R$ 279,90",
            "descricao" => "Fragrância cítrica e fresca para o dia a dia, com notas vibrantes e energizantes.",
            "descricao_longa" => "Lavelle Rose Sublime é a escolha perfeita para o dia a dia. Suas notas cítricas e frescas proporcionam uma sensação de limpeza e energia, ideal para mulheres ativas e modernas. Com uma combinação equilibrada de frutas cítricas e florais suaves, esta fragrância é versátil e adequada para qualquer ocasião.",
            "notas" => [
                "Notas de Saída: Limão Siciliano, Bergamota",
                "Notas de Coração: Neroli, Lírio do Vale",
                "Notas de Fundo: Musk Branco, Almíscar"
            ],
            "badge" => "Mais Vendido",
            "badge_class" => "bestseller",
            "imagem" => "Lavelle Rose Sublime.jpg"
        ]
    ];
}

// Processar adição ao carrinho
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['adicionar_carrinho'])) {
    // Verificar se usuário está logado
    if (!$usuarioLogado) {
        $_SESSION['redirect_to'] = $_SERVER['PHP_SELF'];
        header('Location: login.php');
        exit();
    }
    
    $produto_id = $_POST['produto_id'];
    $quantidade = $_POST['quantidade'] ?? 1;
    
    // Adicionar ou atualizar item no carrinho
    if (isset($_SESSION['carrinho'][$produto_id])) {
        $_SESSION['carrinho'][$produto_id] += $quantidade;
    } else {
        $_SESSION['carrinho'][$produto_id] = $quantidade;
    }
    
    $mensagem_carrinho = "Produto adicionado ao carrinho!";
}

// Processar remoção do carrinho
if (isset($_GET['remover'])) {
    // Verificar se usuário está logado
    if (!$usuarioLogado) {
        $_SESSION['redirect_to'] = $_SERVER['PHP_SELF'];
        header('Location: login.php');
        exit();
    }
    
    $produto_id = $_GET['remover'];
    if (isset($_SESSION['carrinho'][$produto_id])) {
        unset($_SESSION['carrinho'][$produto_id]);
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Processar atualização de quantidade
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['atualizar_carrinho'])) {
    // Verificar se usuário está logado
    if (!$usuarioLogado) {
        $_SESSION['redirect_to'] = $_SERVER['PHP_SELF'];
        header('Location: login.php');
        exit();
    }
    
    foreach ($_POST['quantidade'] as $produto_id => $quantidade) {
        if ($quantidade <= 0) {
            unset($_SESSION['carrinho'][$produto_id]);
        } else {
            $_SESSION['carrinho'][$produto_id] = $quantidade;
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Definindo dados para a página
$empresa = "LAVELLE";

// Categorias de produtos
$categorias = [
    "Todos",
    "Feminino",
    "Masculino",
    "Compartilhável",
];

// *** PAGINAÇÃO - DEVE VIR ANTES DO CÁLCULO DO CARRINHO ***

// Configuração da paginação
$produtos_por_pagina = 9;
$total_produtos = count($produtos);
$total_paginas = ceil($total_produtos / $produtos_por_pagina);

// Obter página atual
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina_atual < 1) $pagina_atual = 1;
if ($pagina_atual > $total_paginas) $pagina_atual = $total_paginas;

// Calcular produtos para a página atual
$inicio = ($pagina_atual - 1) * $produtos_por_pagina;
$produtos_pagina = array_slice($produtos, $inicio, $produtos_por_pagina);

// *** AGORA CALCULAR O TOTAL DO CARRINHO ***

// Calcular total do carrinho
$total_carrinho = 0;
$itens_carrinho = 0;

if (!empty($_SESSION['carrinho'])) {
    foreach ($_SESSION['carrinho'] as $produto_id => $quantidade) {
        $produto_carrinho = null;
        foreach ($produtos as $produto) {
            if ($produto['id'] == $produto_id) {
                $produto_carrinho = $produto;
                break;
            }
        }
        
        if ($produto_carrinho) {
            $subtotal = $produto_carrinho['preco'] * $quantidade;
            $total_carrinho += $subtotal;
            $itens_carrinho += $quantidade;
        }
    }
}

// *** PROCESSAR FINALIZAÇÃO DA COMPRA - ATUALIZADO ***

// Processar finalização de compra
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar_compra'])) {
    // Verificar se usuário está logado
    if (!$usuarioLogado) {
        $_SESSION['redirect_to'] = $_SERVER['PHP_SELF'];
        header('Location: login.php');
        exit();
    }
    
    // Verificar se o carrinho não está vazio
    if (empty($_SESSION['carrinho'])) {
        // Adicionar mensagem de erro na sessão
        $_SESSION['erro_carrinho'] = "Seu carrinho está vazio! Adicione produtos antes de finalizar a compra.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
    
    $metodo_pagamento = $_POST['metodo_pagamento'];
    
    // *** CORREÇÃO: Salvar dados completos do carrinho na sessão ***
    $_SESSION['total_compra'] = $total_carrinho;
    $_SESSION['itens_carrinho'] = $_SESSION['carrinho'];
    $_SESSION['produtos_carrinho'] = []; // Array para armazenar informações completas dos produtos
    
    // Preencher informações completas dos produtos no carrinho
    foreach ($_SESSION['carrinho'] as $produto_id => $quantidade) {
        foreach ($produtos as $produto) {
            if ($produto['id'] == $produto_id) {
                $_SESSION['produtos_carrinho'][$produto_id] = [
                    'produto' => $produto,
                    'quantidade' => $quantidade,
                    'subtotal' => $produto['preco'] * $quantidade
                ];
                break;
            }
        }
    }
    
    // Redirecionar para página de pagamento específica
    switch($metodo_pagamento) {
        case 'credit':
            header('Location: pagamento_cartao.php');
            exit();
        case 'pix':
            header('Location: pagamento_pix.php');
            exit();
        case 'boleto':
            header('Location: pagamento_boleto.php');
            exit();
        default:
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
    }
}

// Função para obter a imagem do produto (usa placeholder se a imagem não existir)
function getProdutoImagem($produto) {
    if (isset($produto['imagem']) && file_exists($produto['imagem'])) {
        return $produto['imagem'];
    }
    // Fallback para placeholder
    return "https://via.placeholder.com/250x300/f5f5f5/333?text=" . urlencode($produto['nome']);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - LAVELLE Perfumes</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }
        
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            letter-spacing: 2px;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            align-items: center;
        }
        
        nav ul li {
            margin-left: 25px;
        }
        
        nav ul li a {
            text-decoration: none;
            color: #000;
            font-weight: 500;
            transition: color 0.3s;
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 5px;
        }
        
        nav ul li a:hover {
            color: #8b7355;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 20px;
            padding-left: 20px;
            border-left: 1px solid #eee;
        }
        
        .user-menu a {
            font-size: 13px;
            padding: 6px 12px;
        }
        
        .user-menu a.profile-link {
            background-color: #f5f5f5;
            color: #8b7355;
        }
        
        .user-menu a.profile-link:hover {
            background-color: #8b7355;
            color: white;
        }
        
        .cart-icon {
            position: relative;
            background: #000;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
            border: none;
            font-weight: 500;
        }

        .cart-icon:hover {
            background-color: #333;
        }

        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            padding: 20px 0;
            background-color: #f5f5f5;
            margin-bottom: 40px;
        }
        
        .breadcrumb a {
            color: #666;
            text-decoration: none;
        }
        
        .breadcrumb a:hover {
            color: #000;
        }
        
        /* Filtros e Categorias */
        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .categories {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .category-btn {
            padding: 10px 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }
        
        .category-btn.active, .category-btn:hover {
            background-color: #000;
            color: white;
            border-color: #000;
        }
        
        .filters {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .filter-select {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: white;
            font-size: 14px;
        }
        
        /* Produtos */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }
        
        .product-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #000;
            color: white;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            z-index: 2;
            font-weight: bold;
        }
        
        .product-badge.new {
            background-color: #8b7355;
        }
        
        .product-badge.bestseller {
            background-color: #d4af37;
            color: #000;
        }
        
        .product-img {
            height: 300px;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .product-img img {
            max-width: 80%;
            max-height: 80%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .product-card:hover .product-img img {
            transform: scale(1.1);
        }
        
        .product-info {
            padding: 25px;
        }
        
        .product-category {
            color: #8b7355;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 8px;
            font-weight: 600;
            letter-spacing: 1px;
        }
        
        .product-name {
            font-size: 18px;
            margin-bottom: 12px;
            color: #000;
            font-weight: 600;
        }
        
        .product-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.5;
            height: 42px;
            overflow: hidden;
        }
        
        .product-price {
            font-weight: bold;
            color: #000;
            font-size: 22px;
            margin-bottom: 20px;
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            text-align: center;
            flex: 1;
            font-size: 14px;
        }
        
        .btn:hover {
            background-color: #333;
            transform: translateY(-2px);
        }
        
        .btn-outline {
            background-color: transparent;
            border: 2px solid #000;
            color: #000;
        }
        
        .btn-outline:hover {
            background-color: #000;
            color: white;
        }

        .btn-success {
            background-color: #27ae60;
        }

        .btn-success:hover {
            background-color: #219653;
        }
        
        /* Modal de Detalhes */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            z-index: 1000;
            overflow-y: auto;
        }
        
        .modal-content {
            background-color: white;
            margin: 50px auto;
            border-radius: 15px;
            width: 90%;
            max-width: 1000px;
            position: relative;
            animation: modalFade 0.3s;
        }
        
        @keyframes modalFade {
            from {opacity: 0; transform: translateY(-50px);}
            to {opacity: 1; transform: translateY(0);}
        }
        
        .close-modal {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 30px;
            color: #000;
            cursor: pointer;
            z-index: 1001;
            background: rgba(255,255,255,0.8);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .product-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            padding: 40px;
        }
        
        .product-detail-image {
            text-align: center;
        }
        
        .product-detail-image img {
            max-width: 100%;
            max-height: 500px;
            border-radius: 10px;
            object-fit: cover;
        }
        
        .product-detail-info h2 {
            font-size: 28px;
            margin-bottom: 15px;
            color: #000;
        }
        
        .product-detail-category {
            color: #8b7355;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 10px;
            font-weight: 600;
        }
        
        .product-detail-price {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #000;
        }
        
        .product-detail-description {
            margin-bottom: 25px;
            line-height: 1.7;
            color: #666;
        }
        
        .product-detail-features {
            margin-bottom: 30px;
        }
        
        .product-detail-features h3 {
            margin-bottom: 15px;
            font-size: 18px;
            color: #000;
        }
        
        .product-detail-features ul {
            list-style: none;
            padding-left: 0;
        }
        
        .product-detail-features li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }
        
        .product-detail-features li:before {
            content: "•";
            color: #8b7355;
            position: absolute;
            left: 0;
        }
        
        .product-detail-actions {
            display: flex;
            gap: 15px;
        }
        
        .quantity-selector {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .quantity-btn {
            width: 35px;
            height: 35px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        
        .quantity-input {
            width: 50px;
            height: 35px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        /* Modal do Carrinho */
        .cart-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            z-index: 1000;
            overflow-y: auto;
        }

        .cart-modal-content {
            background-color = white;
            margin: 50px auto;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            position: relative;
            animation: modalFade 0.3s;
        }

        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px;
            border-bottom: 1px solid #eee;
            background-color: #ffffff;
        }

        .cart-header h2 {
            font-size: 24px;
            color: #000;
        }

        .close-cart {
            font-size: 30px;
            color: #000;
            cursor: pointer;
            background: none;
            border: none;
        }

        .cart-items {
            padding: 30px;
            max-height: 400px;
            overflow-y: auto;
            background-color: #f5f5f5;
        }

        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .cart-item-image {
            width: 80px;
            height: 80px;
            background-color: #f5f5f5;
            border-radius: 8px;
            margin-right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .cart-item-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-name {
            font-weight: bold;
            margin-bottom: 5px;
            color: #000;
        }

        .cart-item-price {
            color: #8b7355;
            font-weight: bold;
        }

        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }

        .cart-item-remove {
            color: #e74c3c;
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .cart-item-remove:hover {
            background-color: #fdf2f2;
        }

        .cart-footer {
            padding: 30px;
            border-top: 1px solid #eee;
            background-color: #f9f9f9;
            border-radius: 0 0 15px 15px;
        }

        .cart-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: bold;
        }

        .cart-actions {
            display: flex;
            gap: 15px;
        }

        .cart-actions .btn {
            flex: 1;
        }

        .empty-cart {
            text-align: center;
            padding: 60px 30px;
            color: #666;
        }

        .empty-cart-icon {
            font-size: 48px;
            margin-bottom: 20px;
            color: #ddd;
        }

        /* Modal de Pagamento */
        .payment-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            z-index: 1000;
            overflow-y: auto;
        }

        .payment-modal-content {
            background-color: white;
            margin: 50px auto;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            position: relative;
            animation: modalFade 0.3s;
        }

        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px;
            border-bottom: 1px solid #eee;
        }

        .payment-header h2 {
            font-size: 24px;
            color: #000;
        }

        .payment-methods {
            padding: 30px;
        }

        .payment-method {
            display: flex;
            align-items: center;
            padding: 20px;
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .payment-method:hover {
            border-color: #8b7355;
        }

        .payment-method.selected {
            border-color: #8b7355;
            background-color: #f9f5f0;
        }

        .payment-method input {
            margin-right: 15px;
        }

        .payment-method-icon {
            font-size: 24px;
            margin-right: 15px;
            width: 40px;
            text-align: center;
        }

        .payment-method-info {
            flex: 1;
        }

        .payment-method-name {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .payment-method-desc {
            color: #666;
            font-size: 14px;
        }

        .payment-form {
            padding: 0 30px 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #000;
            font-weight: 500;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
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

        /* Notificação */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #27ae60;
            color: white;
            padding: 15px 25px;
            border-radius: 8px;
            z-index: 1000;
            animation: slideInRight 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        /* Paginação */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 60px;
            flex-wrap: wrap;
        }
        
        .pagination a, .pagination span {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s;
            font-weight: 500;
        }
        
        .pagination a:hover {
            background-color: #8b7355;
            color: white;
            border-color: #8b7355;
        }
        
        .pagination .current {
            background-color: #000;
            color: white;
            border-color: #000;
        }
        
        .pagination .disabled {
            color: #ccc;
            cursor: not-allowed;
        }
        
        .pagination .disabled:hover {
            background-color: transparent;
            color: #ccc;
            border-color: #ddd;
        }
        
        .page-info {
            text-align: center;
            margin-bottom: 20px;
            color: #666;
            font-size: 14px;
        }
        
        /* Footer */
        footer {
            background-color: #000;
            color: white;
            padding: 60px 0 30px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .footer-column h3 {
            font-size: 18px;
            margin-bottom: 20px;
            color: #fff;
            text-transform: uppercase;
            font-weight: bold;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 10px;
        }
        
        .footer-column ul li a {
            color: #ccc;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-column ul li a:hover {
            color: #fff;
        }
        
        .contact-info {
            color: #ccc;
        }
        
        .contact-info p {
            margin-bottom: 10px;
        }
        
        .social-links a {
            color: #ccc;
            margin-right: 15px;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .social-links a:hover {
            color: #fff;
        }
        
        .copyright {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid #444;
            color: #999;
            font-size: 14px;
        }
        
        /* Responsividade */
        @media (max-width: 768px) {
            .header-top {
                flex-direction: column;
                text-align: center;
            }
            
            nav ul {
                margin-top: 15px;
                justify-content: center;
                flex-wrap: wrap;
            }
            
            nav ul li {
                margin: 5px 8px;
            }
            
            .user-menu {
                margin-left: 0;
                padding-left: 0;
                border-left: none;
                justify-content: center;
                width: 100%;
                margin-top: 10px;
            }
            
            .products-header {
                flex-direction: column;
                gap: 20px;
                align-items: flex-start;
            }
            
            .categories {
                justify-content: center;
            }
            
            .filters {
                width: 100%;
                justify-content: space-between;
            }
            
            .product-detail {
                grid-template-columns: 1fr;
                gap: 30px;
                padding: 30px;
            }
            
            .product-detail-actions {
                flex-direction: column;
            }

            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .cart-item-image {
                margin-bottom: 15px;
            }

            .cart-actions {
                flex-direction: column;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
            
            .pagination {
                gap: 5px;
            }
            
            .pagination a, .pagination span {
                padding: 8px 12px;
                font-size: 14px;
            }
        }
        
        @media (max-width: 480px) {
            .product-card {
                margin: 0 auto;
                max-width: 300px;
            }
            
            .categories {
                justify-content: center;
            }
            
            .category-btn {
                padding: 8px 15px;
                font-size: 12px;
            }
            
            .pagination {
                flex-wrap: wrap;
            }
            
            .pagination a, .pagination span {
                padding: 6px 10px;
                font-size: 12px;
            }
        }
        
        /* Adicionando estilo para atualização em tempo real */
        .cart-update-info {
            background: #e8f5e8;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            text-align: center;
            font-size: 14px;
            color: #27ae60;
            display: none;
        }
        
        .quantity-update {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .quantity-update-btn {
            width: 25px;
            height: 25px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }
        
        .quantity-update-btn:hover {
            background: #f5f5f5;
        }
        /* Header Banner */
        .header-banner {
            background-color: #000;
            color: #ffffff;
            text-align: center;
            padding: 8px 0;
            font-size: 14px;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
            border-bottom: 1px solid #333;
        }
        
        .header-banner h1 {
            font-size: 14px;
            font-weight: 300;
            margin: 0;
            padding: 0;
            letter-spacing: 3px;
            color: #f5f5f5;
        }
        
        
    </style>
</head>
<body>
     <div class="header-banner">
        <h1>O perfume certo transforma a presença em memória.</h1>
    </div>
     
    <header>
        <div class="container">
            <div class="header-top">
                <div class="logo"><?php echo $empresa; ?></div>
                <nav>
                    <ul>
                        <li><a href="index.php">INÍCIO</a></li>
                        <li><a href="paginaprodutos.php" style="color: #8b7355;">PRODUTOS</a></li>
                        <li><a href="sobre.php">SOBRE</a></li>
                        <li><a href="contato.php">CONTATO</a></li>
                        
                        <!-- Menu do Usuário -->
                        <?php if ($usuarioLogado): ?>
                            <div class="user-menu">
                                <span style="color: #8b7355; font-weight: 500;">Olá, <?php echo htmlspecialchars($usuarioNome); ?></span>
                                <li><a href="perfil.php" class="profile-link">MEU PERFIL</a></li>
                                <li>
                                    <button class="cart-icon" onclick="openCartModal()">
                                        CARRINHO
                                        <?php if (count($_SESSION['carrinho']) > 0): ?>
                                            <span class="cart-badge"><?php echo array_sum($_SESSION['carrinho']); ?></span>
                                        <?php endif; ?>
                                    </button>
                                </li>
                            </div>
                        <?php else: ?>
                            <div class="user-menu">
                                <li><a href="login.php">ENTRAR</a></li>
                                <li>
                                    <button class="cart-icon" onclick="openCartModal()">
                                        CARRINHO
                                        <?php if (count($_SESSION['carrinho']) > 0): ?>
                                            <span class="cart-badge"><?php echo array_sum($_SESSION['carrinho']); ?></span>
                                        <?php endif; ?>
                                    </button>
                                </li>
                            </div>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    
    <div class="breadcrumb">
        <div class="container">
            <a href="index.php">Início</a> > <span>Produtos</span>
        </div>
    </div>
    
    <div class="container">
        <div class="products-header">
            <h1>Nossos Produtos</h1>
            <div class="filters">
                <select class="filter-select" id="sort-select">
                    <option value="recent">Ordenar por: Mais Recentes</option>
                    <option value="price-low">Ordenar por: Preço (Menor para Maior)</option>
                    <option value="price-high">Ordenar por: Preço (Maior para Menor)</option>
                    <option value="popular">Ordenar por: Mais Vendidos</option>
                </select>
                <select class="filter-select" id="show-select">
                    <option value="9">Mostrar: 9 produtos</option>
                    <option value="12">Mostrar: 12 produtos</option>
                    <option value="24">Mostrar: 24 produtos</option>
                </select>
            </div>
        </div>
        
        <!-- Informação da página -->
        <div class="page-info">
            Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?> 
            | Mostrando <?php echo count($produtos_pagina); ?> de <?php echo $total_produtos; ?> produtos
        </div>
        
        <div class="categories">
            <?php foreach($categorias as $categoria): ?>
                <button class="category-btn <?php echo $categoria == 'Todos' ? 'active' : ''; ?>" data-category="<?php echo $categoria; ?>">
                    <?php echo $categoria; ?>
                </button>
            <?php endforeach; ?>
        </div>
        
        <div class="products-grid" id="products-container">
            <?php foreach($produtos_pagina as $produto): ?>
            <div class="product-card" data-category="<?php echo $produto['categoria']; ?>" data-price="<?php echo $produto['preco']; ?>">
                <?php if(!empty($produto['badge'])): ?>
                    <div class="product-badge <?php echo $produto['badge_class']; ?>"><?php echo $produto['badge']; ?></div>
                <?php endif; ?>
                <div class="product-img">
                    <img src="<?php echo getProdutoImagem($produto); ?>" alt="<?php echo $produto['nome']; ?>">
                </div>
                <div class="product-info">
                    <div class="product-category"><?php echo $produto['categoria']; ?></div>
                    <h3 class="product-name"><?php echo $produto['nome']; ?></h3>
                    <!-- AQUI: descricao breve no card -->
                    <p class="product-description"><?php echo $produto['descricao']; ?></p>
                    <div class="product-price"><?php echo $produto['preco_formatado']; ?></div>
                    <div class="product-actions">
                        <form method="POST" style="display: inline; margin: 0;">
                            <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                            <input type="hidden" name="quantidade" value="1">
                            <button type="submit" name="adicionar_carrinho" class="btn">
                                Adicionar
                            </button>
                        </form>
                        <button class="btn btn-outline" onclick="openProductModal(<?php echo $produto['id']; ?>)">Detalhes</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Paginação -->
        <div class="pagination">
            <?php if ($pagina_atual > 1): ?>
                <a href="?pagina=1">&laquo; Primeira</a>
                <a href="?pagina=<?php echo $pagina_atual - 1; ?>">&lsaquo; Anterior</a>
            <?php else: ?>
             
            <?php endif; ?>

            <?php
            // Mostrar até 5 páginas ao redor da atual
            $inicio_paginas = max(1, $pagina_atual - 2);
            $fim_paginas = min($total_paginas, $pagina_atual + 2);
            
            for ($i = $inicio_paginas; $i <= $fim_paginas; $i++):
                if ($i == $pagina_atual): ?>
                    <span class="current"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif;
            endfor; ?>

            <?php if ($pagina_atual < $total_paginas): ?>
              
            <?php else: ?>
                <span class="disabled">Próxima &rsaquo;</span>
                <span class="disabled">Última &raquo;</span>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Modal de Detalhes do Produto -->
    <div id="productModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeProductModal()">&times;</span>
            <div class="product-detail" id="product-detail-content">
                <!-- Conteúdo será preenchido via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Modal do Carrinho -->
    <div id="cartModal" class="cart-modal">
        <div class="cart-modal-content">
            <div class="cart-header">
                <h2>Meu Carrinho</h2>
                <button class="close-cart" onclick="closeCartModal()">&times;</button>
            </div>
            
            <div class="cart-items">
                <?php if (empty($_SESSION['carrinho'])): ?>
                    <div class="empty-cart">
                        <div class="empty-cart-icon">🛒</div>
                        <h3>Seu carrinho está vazio</h3>
                        <p>Adicione alguns produtos para continuar</p>
                    </div>
                <?php else: ?>
                    <div class="cart-update-info" id="cartUpdateInfo">
                        Carrinho atualizado! O total será recalculado.
                    </div>
                    
                    <form method="POST" id="cartForm">
                        <?php 
                        $total_carrinho_calc = 0;
                        $itens_carrinho_calc = 0;
                        foreach ($_SESSION['carrinho'] as $produto_id => $quantidade): 
                            // Encontrar o produto no array
                            $produto_carrinho = null;
                            foreach ($produtos as $produto) {
                                if ($produto['id'] == $produto_id) {
                                    $produto_carrinho = $produto;
                                    break;
                                }
                            }
                            
                            if ($produto_carrinho):
                                $subtotal = $produto_carrinho['preco'] * $quantidade;
                                $total_carrinho_calc += $subtotal;
                                $itens_carrinho_calc += $quantidade;
                        ?>
                            <div class="cart-item" data-product-id="<?php echo $produto_id; ?>" data-price="<?php echo $produto_carrinho['preco']; ?>">
                                <div class="cart-item-image">
                                    <img src="<?php echo getProdutoImagem($produto_carrinho); ?>" alt="<?php echo $produto_carrinho['nome']; ?>">
                                </div>
                                <div class="cart-item-info">
                                    <div class="cart-item-name"><?php echo $produto_carrinho['nome']; ?></div>
                                    <div class="cart-item-price"><?php echo $produto_carrinho['preco_formatado']; ?></div>
                                    <div class="cart-item-quantity">
                                        <label>Quantidade:</label>
                                        <div class="quantity-update">
                                            <button type="button" class="quantity-update-btn" onclick="updateQuantity(<?php echo $produto_id; ?>, -1)">-</button>
                                            <input type="number" name="quantidade[<?php echo $produto_id; ?>]" 
                                                   value="<?php echo $quantidade; ?>" min="1" class="quantity-input" 
                                                   onchange="updateCartTotal()" id="quantity_<?php echo $produto_id; ?>">
                                            <button type="button" class="quantity-update-btn" onclick="updateQuantity(<?php echo $produto_id; ?>, 1)">+</button>
                                        </div>
                                        <span class="item-subtotal" id="subtotal_<?php echo $produto_id; ?>">
                                            Subtotal: R$ <?php echo number_format($subtotal, 2, ',', '.'); ?>
                                        </span>
                                    </div>
                                </div>
                                <a href="?remover=<?php echo $produto_id; ?>" class="cart-item-remove" onclick="return confirmRemoveItem(event)">
                                    Remover
                                </a>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </form>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($_SESSION['carrinho'])): ?>
                <div class="cart-footer">
                    <div class="cart-total">
                        <span>Total:</span>
                        <span id="cartTotal">R$ <?php echo number_format($total_carrinho_calc, 2, ',', '.'); ?></span>
                    </div>
                    <div class="cart-actions">
                        <button type="submit" form="cartForm" name="atualizar_carrinho" class="btn btn-outline">
                            Atualizar Carrinho
                        </button>
                        <?php if ($usuarioLogado): ?>
                            <button class="btn" onclick="openPaymentModal()">
                                Finalizar Compra
                            </button>
                        <?php else: ?>
                            <button class="btn" onclick="showLoginAlert()">
                                Fazer Login para Comprar
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal de Pagamento -->
    <div id="paymentModal" class="payment-modal">
        <div class="payment-modal-content">
            <div class="payment-header">
                <h2>Finalizar Compra</h2>
                <button class="close-cart" onclick="closePaymentModal()">&times;</button>
            </div>
            
            <form method="POST" id="paymentForm" class="payment-form">
                <div class="payment-methods">
                    <div class="payment-method" onclick="selectPaymentMethod('credit')">
                        <input type="radio" name="metodo_pagamento" value="credit" id="credit" required>
                        <div class="payment-method-icon"></div>
                        <div class="payment-method-info">
                            <div class="payment-method-name">Cartão</div>
                            <div class="payment-method-desc">Parcelamento em até 12x</div>
                        </div>
                    </div>
                    
                    <div class="payment-method" onclick="selectPaymentMethod('pix')">
                        <input type="radio" name="metodo_pagamento" value="pix" id="pix">
                        <div class="payment-method-icon"></div>
                        <div class="payment-method-info">
                            <div class="payment-method-name">PIX</div>
                            <div class="payment-method-desc">15% de desconto</div>
                        </div>
                    </div>

                    <div class="payment-method" onclick="selectPaymentMethod('boleto')">
                        <input type="radio" name="metodo_pagamento" value="boleto" id="boleto">
                        <div class="payment-method-icon"></div>
                        <div class="payment-method-info">
                            <div class="payment-method-name">Boleto Bancário</div>
                            <div class="payment-method-desc">Pagamento em 1x</div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="finalizar_compra" class="btn" style="width: 100%; margin-top: 20px;">
                    Confirmar Pedido - R$ <?php echo number_format($total_carrinho, 2, ',', '.'); ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Notificação -->
    <?php if (isset($mensagem_carrinho)): ?>
        <div class="notification" id="notification">
            <?php echo $mensagem_carrinho; ?>
        </div>
        <script>
            setTimeout(() => {
                const notification = document.getElementById('notification');
                if (notification) {
                    notification.remove();
                }
            }, 3000);
        </script>
    <?php endif; ?>

    <!-- SweetAlert para carrinho vazio -->
    <?php if (isset($_SESSION['erro_carrinho'])): ?>
        <script>
            Swal.fire({
                title: 'Carrinho Vazio',
                text: '<?php echo $_SESSION['erro_carrinho']; ?>',
                icon: 'warning',
                confirmButtonColor: '#8b7355'
            });
        </script>
        <?php unset($_SESSION['erro_carrinho']); ?>
    <?php endif; ?>
    
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>CONTATO</h3>
                    <div class="contact-info">
                        <p>E-mail: contatolavelle@gmail.com</p>
                        <p>Endereço: Rua das Fragrâncias, 123 - Jardim Perfumado</p>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>REDES SOCIAIS</h3>
                    <div class="social-links">
                        <a href="#">Facebook</a><br>
                        <a href="#">Instagram</a><br>
                        <a href="#">Twitter</a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>POLÍTICAS</h3>
                    <ul>
                        <li><a href="#">Política de Privacidade</a></li>
                        <li><a href="#">Termos de Uso</a></li>
                        <li><a href="#">Trocas e Devoluções</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>INFORMAÇÕES</h3>
                    <ul>
                        <li><a href="sobre.php">Sobre Nós</a></li>
                        <li><a href="#">Nossa História</a></li>
                        <li><a href="#">Trabalhe Conosco</a></li>
                        <li><a href="#">FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; <?php echo date('Y'); ?> LAVELLE Perfumes. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- SweetAlert Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Dados dos produtos para o modal (todos os produtos)
        const productsData = <?php echo json_encode($produtos); ?>;
        
        // Função para atualizar quantidade
        function updateQuantity(productId, change) {
            const input = document.getElementById('quantity_' + productId);
            let newValue = parseInt(input.value) + change;
            
            if (newValue < 1) newValue = 1;
            if (newValue > 99) newValue = 99;
            
            input.value = newValue;
            updateCartTotal();
            showUpdateMessage();
        }
        
        // Função para atualizar o total do carrinho em tempo real
        function updateCartTotal() {
            let total = 0;
            const cartItems = document.querySelectorAll('.cart-item');
            
            cartItems.forEach(item => {
                const productId = item.getAttribute('data-product-id');
                const price = parseFloat(item.getAttribute('data-price'));
                const quantity = parseInt(document.getElementById('quantity_' + productId).value);
                const subtotal = price * quantity;
                
                // Atualizar subtotal do item
                document.getElementById('subtotal_' + productId).textContent = 
                    'Subtotal: R$ ' + subtotal.toFixed(2).replace('.', ',');
                
                total += subtotal;
            });
            
            // Atualizar total do carrinho
            document.getElementById('cartTotal').textContent = 'R$ ' + total.toFixed(2).replace('.', ',');
            
            // Atualizar botão de finalizar compra no modal de pagamento
            const finalizeButton = document.querySelector('#paymentForm button[type="submit"]');
            if (finalizeButton) {
                finalizeButton.textContent = 'Confirmar Pedido - R$ ' + total.toFixed(2).replace('.', ',');
            }
        }
        
        // Função para mostrar mensagem de atualização
        function showUpdateMessage() {
            const updateInfo = document.getElementById('cartUpdateInfo');
            if (updateInfo) {
                updateInfo.style.display = 'block';
                setTimeout(() => {
                    updateInfo.style.display = 'none';
                }, 2000);
            }
        }
        
        // Funções do Carrinho
        function openCartModal() {
            // Verificar se o usuário está logado (usando a variável PHP convertida para JS)
            const usuarioLogado = <?php echo $usuarioLogado ? 'true' : 'false'; ?>;
            
            if (usuarioLogado) {
                document.getElementById('cartModal').style.display = 'block';
                // Atualizar total quando o modal abrir
                setTimeout(updateCartTotal, 100);
            } else {
                Swal.fire({
                    title: 'Login Necessário',
                    text: 'Por favor, faça login para acessar o carrinho!',
                    icon: 'warning',
                    confirmButtonText: 'Fazer Login',
                    confirmButtonColor: '#8b7355'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'login.php';
                    }
                });
            }
        }
        
        function closeCartModal() {
            document.getElementById('cartModal').style.display = 'none';
        }

        // Função para mostrar alerta de login
        function showLoginAlert() {
            Swal.fire({
                title: 'Login Necessário',
                text: 'Por favor, faça login para finalizar a compra!',
                icon: 'warning',
                confirmButtonText: 'Fazer Login',
                confirmButtonColor: '#8b7355',
                showCancelButton: true,
                cancelButtonText: 'Continuar Comprando',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        }

        // Função para confirmar remoção de item
        function confirmRemoveItem(event) {
            event.preventDefault();
            const href = event.currentTarget.href;
            
            Swal.fire({
                title: 'Remover Item',
                text: 'Tem certeza que deseja remover este item do carrinho?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, Remover',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
            
            return false;
        }
        
        // Filtro por categoria
        document.addEventListener('DOMContentLoaded', function() {
            const categoryButtons = document.querySelectorAll('.category-btn');
            const productCards = document.querySelectorAll('.product-card');
            
            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class de todos os botões
                    categoryButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Adiciona active class ao botão clicado
                    this.classList.add('active');
                    
                    const category = this.getAttribute('data-category');
                    
                    // Filtra os produtos
                    productCards.forEach(card => {
                        if (category === 'Todos' || card.getAttribute('data-category') === category) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
            
            // Ordenação de produtos
            document.getElementById('sort-select').addEventListener('change', function() {
                sortProducts(this.value);
            });
            
            // Atualizar total quando o modal do carrinho abrir
            document.querySelector('.cart-icon').addEventListener('click', function() {
                setTimeout(updateCartTotal, 100);
            });
        });
        
        // Função para abrir modal de detalhes do produto
        function openProductModal(productId) {
            const product = productsData.find(p => p.id === productId);
            if (!product) return;
            
            const modalContent = document.getElementById('product-detail-content');
            modalContent.innerHTML = `
                <div class="product-detail-image">
                    <img src="${product.imagem || 'https://via.placeholder.com/400x500/f5f5f5/333?text=' + encodeURIComponent(product.nome)}" alt="${product.nome}">
                </div>
                <div class="product-detail-info">
                    <div class="product-detail-category">${product.categoria}</div>
                    <h2>${product.nome}</h2>
                    <div class="product-detail-price">${product.preco_formatado}</div>
                    <!-- AQUI: descricao longa no modal -->
                    <p class="product-detail-description">${product.descricao_longa}</p>
                    
                    <div class="product-detail-features">
                        <h3>Notas Olfativas</h3>
                        <ul>
                            ${product.notas.map(nota => `<li>${nota}</li>`).join('')}
                        </ul>
                    </div>
                    
                    <div class="quantity-selector">
                        <span>Quantidade:</span>
                        <button type="button" class="quantity-btn" onclick="decreaseQuantity()">-</button>
                        <input type="number" class="quantity-input" id="quantity" value="1" min="1" max="10">
                        <button type="button" class="quantity-btn" onclick="increaseQuantity()">+</button>
                    </div>
                    
                    <div class="product-detail-actions">
                        <form method="POST" style="flex: 1;">
                            <input type="hidden" name="produto_id" value="${product.id}">
                            <input type="hidden" name="quantidade" value="1" id="modalQuantity">
                            <button type="submit" name="adicionar_carrinho" class="btn" style="width: 100%;">
                                Adicionar ao Carrinho
                            </button>
                        </form>
                        <button class="btn btn-outline" onclick="closeProductModal()">Continuar Comprando</button>
                    </div>
                </div>
            `;
            
            document.getElementById('productModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        
        // Função para fechar modal
        function closeProductModal() {
            document.getElementById('productModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        
        // Funções para quantidade no modal de detalhes
        function increaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            const modalInput = document.getElementById('modalQuantity');
            if (quantityInput) {
                const newValue = parseInt(quantityInput.value) + 1;
                if (newValue <= 10) {
                    quantityInput.value = newValue;
                    if (modalInput) modalInput.value = newValue;
                }
            }
        }
        
        function decreaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            const modalInput = document.getElementById('modalQuantity');
            if (quantityInput && parseInt(quantityInput.value) > 1) {
                quantityInput.value = parseInt(quantityInput.value) - 1;
                if (modalInput) modalInput.value = quantityInput.value;
            }
        }
        
        // Funções de Pagamento
        function openPaymentModal() {
            closeCartModal();
            document.getElementById('paymentModal').style.display = 'block';
        }
        
        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }
        
        function selectPaymentMethod(method) {
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            const selectedElement = document.querySelector(`.payment-method input[value="${method}"]`).parentElement;
            selectedElement.classList.add('selected');
            document.getElementById(method).checked = true;
        }
        
        // Função para ordenar produtos
        function sortProducts(criteria) {
            const container = document.getElementById('products-container');
            const products = Array.from(container.getElementsByClassName('product-card'));
            
            products.sort((a, b) => {
                switch(criteria) {
                    case 'price-low':
                        return parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price'));
                    case 'price-high':
                        return parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price'));
                    case 'popular':
                        // Simulação - produtos com badge "Mais Vendido" primeiro
                        const aBadge = a.querySelector('.product-badge.bestseller') ? 1 : 0;
                        const bBadge = b.querySelector('.product-badge.bestseller') ? 1 : 0;
                        return bBadge - aBadge;
                    default: // recent
                        return 0; // Mantém ordem original
                }
            });
            
            // Reorganiza os produtos no container
            products.forEach(product => container.appendChild(product));
        }
        
        // Fechar modal ao clicar fora
        window.onclick = function(event) {
            const modal = document.getElementById('productModal');
            const cartModal = document.getElementById('cartModal');
            const paymentModal = document.getElementById('paymentModal');
            
            if (event.target === modal) {
                closeProductModal();
            }
            if (event.target === cartModal) {
                closeCartModal();
            }
            if (event.target === paymentModal) {
                closePaymentModal();
            }
        }
    </script>
</body>
</html>