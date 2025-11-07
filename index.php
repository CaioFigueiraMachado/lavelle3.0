<?php
session_start();
include 'conexao.php';

// Verificar se usuário está logado - CORRIGIDO
$usuarioLogado = false;
$usuarioNome = "";

if (isset($_SESSION['id'])) { // Mudado de 'usuario_id' para 'id'
    $usuarioLogado = true;
    $usuarioNome = $_SESSION['nome']; // Mudado de 'usuario_nome' para 'nome'
}

// Verificar se é admin
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];

// Definindo dados para a página
$empresa = "LAVELLE";
$slogan = "O perfume certo transforma a presença em memória.";
$descricao = "Descubra fragrâncias únicas e inesquecíveis";

// Produtos em destaque
$produtos = [
    [
        "nome" => "Lavelle Aureum",
        "preco" => "R$ 299,90",
        "imagem" => "lavelleaureum.jpg"
    ],
    [
        "nome" => "Lavelle Horizon",
        "preco" => "R$ 349,90",
        "imagem" => "horizon.png"
    ],
    [
        "nome" => "Lavelle Rose Sublime",
        "preco" => "R$ 279,90",
        "imagem" => "Lavelle Rose Sublime.jpg"
    ],
 
];

// Categorias - ATUALIZADO COM LINKS CORRETOS
$categorias = [
    [
        "nome" => "Fragrâncias Femininas",
        "imagem" => "femininas.jpg",
        "link" => "paginaprodutos.php?categoria=Feminino"
    ],
    [
        "nome" => "Fragrâncias Masculinas",
        "imagem" => "perfumemasc.jfif",
        "link" => "paginaprodutos.php?categoria=Masculino"
    ],
    [
        "nome" => "Fragrâncias Compartilháveis",
        "imagem" => "lavellegolden.jpg",
        "link" => "paginaprodutos.php?categoria=Compartilhável"
    ]
];

// Destaques
$destaques = [
    [
        "titulo" => "Ingredientes Naturais",
        "descricao" => "Utilizamos apenas os melhores ingredientes naturais em nossas fragrâncias, garantindo pureza e qualidade excepcionais.",
        "icone" => "N"
    ],
    [
        "titulo" => "Tecnologia Avançada",
        "descricao" => "Combinação perfeita entre tradição perfumista e tecnologia moderna para criar experiências olfativas únicas.",
        "icone" => "T"
    ],
    [
        "titulo" => "Sustentabilidade",
        "descricao" => "Comprometidos com práticas sustentáveis e responsáveis em toda nossa cadeiade produção.",
        "icone" => "S"
    ],
   
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAVELLE Perfumes - Fragrâncias Únicas e Inesquecíveis</title>
    <style>
        /* Reset e estilos gerais */
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
            margin-left: 20px;
            position: relative;
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
        
        /* Link ADM - NOVO ESTILO */
        .user-menu a.admin-link {
            background-color: #8b7355;
            color: white;
            font-weight: bold;
        }
        
        .user-menu a.admin-link:hover {
            background-color: #000;
            color: white;
        }
        
        /* Seção de Boas-vindas */
        .user-welcome {
            background: linear-gradient(135deg, #8b7355 0%, #000 100%);
            color: white;
            padding: 40px 0;
        }

        .welcome-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            text-align: center;
        }

        .welcome-content h2 {
            font-size: 28px;
            margin-bottom: 15px;
        }

        .welcome-content p {
            font-size: 16px;
            margin-bottom: 25px;
            opacity: 0.9;
        }

        .user-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .user-actions .btn {
            padding: 12px 25px;
            min-width: 140px;
        }

        .user-actions .btn-outline {
            background: transparent;
            border: 2px solid white;
            color: white;
        }

        .user-actions .btn-outline:hover {
            background: white;
            color: #000;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('lavellehome.png') no-repeat center center/cover;
            background-size: cover;
            background-position: center;
            height: 80vh;
            display: flex;
            align-items: center;
            color: white;
            text-align: center;
        }
        
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            letter-spacing: 3px;
        }
        
        .hero p {
            font-size: 20px;
            margin-bottom: 30px;
        }
        
        .btn {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 10px 20px; /* REDUZIDO de 12px 30px */
            border-radius: 25px; /* REDUZIDO de 30px */
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px; /* ADICIONADO tamanho de fonte menor */
            text-align: center; /* ADICIONADO para centralizar texto */
            display: flex; /* ADICIONADO */
            align-items: center; /* ADICIONADO */
            justify-content: center; /* ADICIONADO */
            min-width: 100px; /* ADICIONADO largura mínima */
        }
           .btn2 {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 10px 20px; /* REDUZIDO de 12px 30px */
            border-radius: 25px; /* REDUZIDO de 30px */
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            font-size: 14px; /* ADICIONADO tamanho de fonte menor */
            text-align: center; /* ADICIONADO para centralizar texto */
           
            align-items: center; /* ADICIONADO */
            justify-content: center; /* ADICIONADO */
            min-width: 100px; /* ADICIONADO largura mínima */
        }
        
        .btn:hover {
            background-color: #333;
        }
        
        .btn-outline {
            background-color: transparent;
            border: 2px solid #000;
            color: #000;
            display: inline-block;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            cursor: pointer;
            font-size: 14px;
            text-align: center;
        }
        
        .btn-outline:hover {
            background-color: #000;
            color: white;
        }
        
        /* Banner Aureum com Vídeo */
        .aureum-banner {
            background-color: #ffffffff;
            padding: 80px 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .video-container {
            position: relative;
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .video-banner {
            width: 100%;
            height: auto;
            display: block;
            border-radius: 15px;
        }
        
        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.5));
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 15px;
        }
        
        .aureum-banner h2 {
            font-size: 42px;
            margin-bottom: 15px;
            color: #fff;
            letter-spacing: 3px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .aureum-banner p {
            font-size: 20px;
            max-width: 600px;
            margin: 0 auto 25px;
            color: #f0f0f0;
            font-weight: 300;
        }
        
        /* Seções */
        .section {
            padding: 80px 0;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
            font-size: 32px;
            color: #000;
        }
        
        /* Produtos - CSS CORRIGIDO */
        .products {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .product-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-10px);
        }
        
        .product-img {
            height: 300px;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }
        
        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        
        .product-info {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* ADICIONADO para melhor distribuição */
        }
        
        .product-name {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .product-price {
            font-weight: bold;
            color: #000;
            font-size: 20px;
            margin-bottom: 15px;
        }
        
        /* Container do botão Comprar - NOVO */
        .product-actions {
            display: flex;
            justify-content: center; /* Centraliza o botão */
            margin-top: auto; /* Empurra o botão para baixo */
            padding-top: 10px;
        }
        
        /* Categorias - CSS CORRIGIDO */
        .categories {
            background-color: white;
        }
        
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .category-card {
            position: relative;
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .category-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .category-card:hover .category-img {
            transform: scale(1.05);
        }
        
        .category-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 30px;
            background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center; /* Centraliza o conteúdo */
            text-align: center;
        }
        
        .category-name {
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        /* Destaques - Cards Elegantes */
        .highlights {
            background-color: #f5f5f5;
        }
        
        .highlights-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .highlight-card {
            background-color: white;
            border-radius: 10px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            border-top: 4px solid #8b7355;
        }
        
        .highlight-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .highlight-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 25px;
            background-color: #f9f5f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #8b7355;
            font-weight: bold;
        }
        
        .highlight-title {
            font-size: 22px;
            margin-bottom: 15px;
            color: #000;
            font-weight: 600;
        }
        
        .highlight-text {
            color: #666;
            line-height: 1.6;
        }
        
        /* Newsletter */
        .newsletter {
            background-color: #000;
            color: white;
            text-align: center;
            padding: 80px 0;
        }
        
        .newsletter h2 {
            font-size: 32px;
            margin-bottom: 20px;
        }
        
        .newsletter p {
            font-size: 18px;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .newsletter-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .newsletter-input {
            flex: 1;
            padding: 15px;
            border: none;
            border-radius: 30px 0 0 30px;
            font-size: 16px;
        }
        
        .newsletter-btn {
            background-color: #8b7355;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 0 30px 30px 0;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .newsletter-btn:hover {
            background-color: #756049;
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
            .header-banner h1 {
                font-size: 12px;
                letter-spacing: 1px;
                padding: 0 10px;
            }
            
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
            
            .hero h1 {
                font-size: 36px;
            }
            
            .about-content {
                grid-template-columns: 1fr;
            }
            
            .aureum-banner h2 {
                font-size: 32px;
            }
            
            .aureum-banner p {
                font-size: 18px;
            }
            
            .newsletter-form {
                flex-direction: column;
            }
            
            .newsletter-input {
                border-radius: 30px;
                margin-bottom: 10px;
            }
            
            .newsletter-btn {
                border-radius: 30px;
            }
            
            .highlight-card {
                padding: 30px 20px;
            }
            
            .user-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .user-actions .btn {
                width: 200px;
            }
            
            /* Ajustes para imagens em mobile */
            .product-img {
                height: 250px;
            }
            
            .category-card {
                height: 300px;
            }
            
            /* Ajustes para botões em mobile */
            .btn {
                padding: 8px 16px;
                font-size: 13px;
                min-width: 90px;
            }
            
            .btn-outline {
                padding: 10px 20px;
                font-size: 13px;
            }
        }
        
        @media (max-width: 480px) {
            .header-banner h1 {
                font-size: 10px;
                letter-spacing: 0.5px;
            }
            
            .aureum-banner h2 {
                font-size: 28px;
            }
            
            .aureum-banner p {
                font-size: 16px;
            }
            
            nav ul {
                flex-direction: column;
                gap: 10px;
            }
            
            .user-menu {
                flex-direction: column;
                gap: 10px;
            }
            
            .product-img {
                height: 200px;
            }
            
            .category-card {
                height: 250px;
            }
            
            .btn {
                padding: 7px 14px;
                font-size: 12px;
                min-width: 80px;
            }
            
            .btn-outline {
                padding: 8px 16px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Banner com a frase estilizada -->
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
                        <li><a href="paginaprodutos.php">PRODUTOS</a></li>
                       
                        <li><a href="sobre.php">SOBRE</a></li>
                        <li><a href="contato.php">CONTATO</a></li>
                        
                        <!-- Menu do Usuário - CORRIGIDO -->
                        <?php if ($usuarioLogado): ?>
                            <div class="user-menu">
                                <span style="color: #8b7355; font-weight: 500;">Olá, <?php echo htmlspecialchars($usuarioNome); ?></span>
                                <li><a href="perfil.php" class="profile-link">MEU PERFIL</a></li>
                                
                                <!-- LINK ADM - APENAS PARA ADMINISTRADOR -->
                                <?php if ($isAdmin): ?>
                                    <li><a href="admin/dashboard.php" class="admin-link">ADM</a></li>
                                <?php endif; ?>
                                
                                <li><a href="logout.php">SAIR</a></li>
                            </div>
                        <?php else: ?>
                            <div class="user-menu">
                                <li><a href="login.php">ENTRAR</a></li>
                              
                            </div>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Seção de Boas-vindas para usuários logados -->
   
    
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1><?php echo $slogan; ?></h1>
                <p><?php echo $descricao; ?></p>
                <a href="paginaprodutos.php" class="btn2">Explorar Coleção</a>
            </div>
        </div>
    </section>
    
    <section class="aureum-banner">
        <div class="container">
            <div class="video-container">
                <video class="video-banner" autoplay muted loop>
                    <source src="vd.mp4" type="video/mp4">
                    Seu navegador não suporta o elemento de vídeo.
                </video>
                <div class="video-overlay">
                   
                </div>
            </div>
        </div>
    </section>
    
    <section class="section">
        <div class="container">
            <h2 class="section-title">Nossas Fragrâncias</h2>
            <div class="products">
                <?php foreach($produtos as $produto): ?>
                <div class="product-card">
                    <div class="product-img">
                        <!-- Imagem do produto - agora preenche todo o espaço -->
                        <img src="<?php echo $produto['imagem']; ?>" alt="<?php echo $produto['nome']; ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-name"><?php echo $produto['nome']; ?></h3>
                        <p class="product-price"><?php echo $produto['preco']; ?></p>
                        <div class="product-actions">
                            <a href="paginaprodutos.php" class="btn">Comprar</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align: center; margin-top: 40px;">
                <a href="paginaprodutos.php" class="btn btn-outline">Ver Todos os Produtos</a>
            </div>
        </div>
    </section>
    
    <section class="section categories">
        <div class="container">
            <h2 class="section-title">Explore por Categoria</h2>
            <div class="categories-grid">
                <?php foreach($categorias as $categoria): ?>
                <div class="category-card">
                    <img src="<?php echo $categoria['imagem']; ?>" alt="<?php echo $categoria['nome']; ?>" class="category-img">
                    <div class="category-overlay">
                        <h3 class="category-name"><?php echo $categoria['nome']; ?></h3>
                        <!-- LINK CORRIGIDO: usando o link específico da categoria -->
                        <a href="<?php echo $categoria['link']; ?>" class="btn">Explorar</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <section class="section highlights">
        <div class="container">
            <h2 class="section-title">Por que Escolher a Lavelle?</h2>
            <div class="highlights-grid">
                <?php foreach($destaques as $destaque): ?>
                <div class="highlight-card">
                    <div class="highlight-icon">
                        <?php echo $destaque['icone']; ?>
                    </div>
                    <h3 class="highlight-title"><?php echo $destaque['titulo']; ?></h3>
                    <p class="highlight-text"><?php echo $destaque['descricao']; ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    
    
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
                    <li><a href="./pdf/politica_privacidade.pdf" download="politica_privacidade.pdf">Política de Privacidade</a></li>
                    <li><a href="./pdf/termos_uso.pdf" download="termos_uso.pdf">Termos de Uso</a></li>
                    <li><a href="./pdf/trocas_devolucoes.pdf" download="trocas_devolucoes.pdf">Trocas e Devoluções</a></li>
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

    <script>
        // Controles do vídeo
        const video = document.querySelector('.video-banner');
        
        // Pausar vídeo quando não estiver visível para economizar recursos
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    video.play();
                } else {
                    video.pause();
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(video);
        
        // Newsletter form
        document.querySelector('.newsletter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('.newsletter-input').value;
            alert('Obrigado por se inscrever com o e-mail: ' + email);
            this.reset();
        });
    </script>
</body>
</html>