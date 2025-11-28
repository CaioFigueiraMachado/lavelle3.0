<!DOCTYPE html>
<html lang="pt-BR">
<head>
<?php
session_start();
include 'conexao.php';

// Verificar se usuário está logado
$usuarioLogado = false;
$usuarioNome = "";
$empresa = "LAVELLE";

// Suportar múltiplos padrões de nomes de sessão
if (isset($_SESSION['usuario_id']) || isset($_SESSION['id'])) {
    $usuarioLogado = true;
    $usuarioNome = $_SESSION['usuario_nome'] ?? $_SESSION['nome'] ?? 'Usuário';
}
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre - LAVELLE Perfumes</title>
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
        
        .user-menu a.profile-link {
            background-color: #f5f5f5;
            color: #8b7355;
        }
        
        .user-menu a.profile-link:hover {
            background-color: #8b7355;
            color: white;
        }
        
        /* Hero Section */
        .page-hero {
            position: relative;
            height: 60vh;
            display: flex;
            align-items: center;
            color: white;
            text-align: center;
            overflow: hidden;
        }
        
        .hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
        }
        
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: -1;
        }
        
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        
        .page-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            letter-spacing: 3px;
        }
        
        .page-hero p {
            font-size: 20px;
            margin-bottom: 30px;
        }
        
        .btn {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background-color: #333;
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
        
        /* Story Section */
        .story-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }
        
        .story-text h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: #000;
        }
        
        .story-text p {
            margin-bottom: 20px;
            color: #666;
            line-height: 1.8;
            font-size: 16px;
        }
        
        .story-image {
            border-radius: 15px;
            overflow: hidden;
            height: 400px;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .story-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        /* Timeline Section */
        .timeline-section {
            background-color: #f5f5f5;
        }
        
        .timeline {
            position: relative;
            max-width: 800px;
            margin: 0 auto;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #8b7355;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 40px;
            padding-left: 30px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -9px;
            top: 0;
            width: 16px;
            height: 16px;
            background: #8b7355;
            border-radius: 50%;
            border: 3px solid #fff;
        }
        
        .timeline-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .timeline-content h3 {
            color: #000;
            font-size: 20px;
            margin-bottom: 10px;
        }
        
        .timeline-year {
            color: #8b7355;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        /* Values Section */
        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .value-card {
            background-color: white;
            border-radius: 10px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            border-top: 4px solid #8b7355;
        }
        
        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .value-icon {
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
        
        .value-card h3 {
            font-size: 22px;
            margin-bottom: 15px;
            color: #000;
            font-weight: 600;
        }
        
        .value-card p {
            color: #666;
            line-height: 1.6;
        }
        
        /* Team Section */
        .team-section {
            background-color: #f5f5f5;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .team-member {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        
        .team-member:hover {
            transform: translateY(-10px);
        }
        
        .member-photo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0 auto 20px;
            overflow: hidden;
            border: 3px solid #8b7355;
        }
        
        .member-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .team-member h3 {
            font-size: 20px;
            margin-bottom: 5px;
            color: #000;
        }
        
        .team-member .role {
            color: #8b7355;
            font-style: italic;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .team-member p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }
        
        /* Process Section - CARDS ESTILIZADOS */
        .process-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .process-step {
            background-color: white;
            border-radius: 15px;
            padding: 40px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
        }
        
        .process-step::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #8b7355, #d4b896);
        }
        
        .process-step:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .step-number {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #8b7355, #d4b896);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto 25px;
            box-shadow: 0 5px 15px rgba(139, 115, 85, 0.3);
            transition: all 0.3s ease;
        }
        
        .process-step:hover .step-number {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(139, 115, 85, 0.4);
        }
        
        .process-step h3 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #000;
            font-weight: 600;
            position: relative;
            padding-bottom: 15px;
        }
        
        .process-step h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 2px;
            background: #8b7355;
        }
        
        .process-step p {
            color: #666;
            line-height: 1.7;
            font-size: 15px;
            margin-top: auto;
        }
        
        /* Testimonials Section */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        
        .testimonial {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            position: relative;
        }
        
        .testimonial::before {
            content: '"';
            font-size: 60px;
            color: #8b7355;
            position: absolute;
            top: 10px;
            left: 20px;
            line-height: 1;
        }
        
        .testimonial-text {
            color: #666;
            font-style: italic;
            margin-bottom: 20px;
            padding-top: 10px;
            line-height: 1.6;
        }
        
        .testimonial-author {
            color: #000;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .testimonial-rating {
            color: #ffd700;
            font-size: 16px;
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
            
            .page-hero h1 {
                font-size: 36px;
            }
            
            .story-content {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .story-image {
                height: 300px;
                order: -1;
            }
            
            .timeline {
                padding-left: 20px;
            }
            
            .timeline-item {
                padding-left: 20px;
            }
            
            .process-step {
                padding: 30px 20px;
            }
        }
        
        @media (max-width: 480px) {
            .page-hero h1 {
                font-size: 28px;
            }
            
            .page-hero p {
                font-size: 16px;
            }
            
            .section-title {
                font-size: 28px;
            }
            
            .value-card, .team-member, .testimonial {
                padding: 20px;
            }
            
            .process-step {
                padding: 25px 15px;
            }
            
            .step-number {
                width: 60px;
                height: 60px;
                font-size: 20px;
            }
        }
        
        /* Animações */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in {
            animation: fadeInUp 0.8s ease-out;
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
        <?php include 'header.php'; ?>
    </header>

    <!-- Hero Section -->
    <section class="page-hero">
        <video class="hero-video" autoplay muted loop playsinline>
            <source src="vdblack.mp4" type="video/mp4">
            Seu navegador não suporta vídeos HTML5.
        </video>
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content fade-in">
                <h1>Nossa História</h1>
                <p>Conheça a jornada da Lavelle Perfumes e nossa paixão por criar experiências olfativas únicas e memoráveis</p>
                <a href="paginaprodutos.php" class="btn">Explorar Produtos</a>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="section">
        <div class="container">
            <div class="story-content">
                <div class="story-text fade-in">
                    <h2>Uma Paixão que Começou</h2>
                    <p>A Lavelle Perfumes nasceu do sonho de criar fragrâncias que contassem histórias. Fundada por especialistas em perfumaria com experiência, nossa marca representa a união perfeita entre tradição e inovação.</p>
                    <p>Desde o início, nossa missão tem sido democratizar o acesso a perfumes de alta qualidade, oferecendo fragrâncias premium com preços justos e atendimento personalizado.</p>
                    <p>Hoje, somos reconhecidos como uma das principais perfumarias do país, com milhares de clientes satisfeitos e uma reputação construída sobre confiança, qualidade e excelência.</p>
                </div>
                <div class="story-image fade-in">
                    <img src="img/sobre.jpg" alt="Nossa História">
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline Section -->
    <section class="section timeline-section">
        <div class="container">
            <h2 class="section-title">Nossa Jornada</h2>
            <div class="timeline">
                <div class="timeline-item fade-in">
                    <div class="timeline-content">
                        <div class="timeline-year">2025</div>
                        <h3>Fundação</h3>
                        <p>Abertura do primeiro site, com foco em perfumes importados de alta qualidade.</p>
                    </div>
                </div>
                <div class="timeline-item fade-in">
                    <div class="timeline-content">
                        <div class="timeline-year">2025</div>
                        <h3>Expansão Digital</h3>
                        <p>Lançamento da loja online, permitindo atender clientes em todo o Brasil com entrega rápida e segura.</p>
                    </div>
                </div>
                <div class="timeline-item fade-in">
                    <div class="timeline-content">
                        <div class="timeline-year">2025</div>
                        <h3>Linha Própria</h3>
                        <p>Desenvolvimento da primeira linha de perfumes exclusivos Lavelle, criada por perfumistas renomados.</p>
                    </div>
                </div>
                
                   
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Nossos Valores</h2>
            <div class="values-grid">
                <div class="value-card fade-in">
                    <div class="value-icon">M</div>
                    <h3>Missão</h3>
                    <p>Proporcionar experiências olfativas únicas e memoráveis, oferecendo perfumes de alta qualidade que expressem a personalidade e estilo de cada cliente.</p>
                </div>
                <div class="value-card fade-in">
                    <div class="value-icon">V</div>
                    <h3>Visão</h3>
                    <p>Ser reconhecida como a principal referência em perfumaria no Brasil, inovando constantemente e mantendo a excelência em produtos e atendimento.</p>
                </div>
                <div class="value-card fade-in">
                    <div class="value-icon">Q</div>
                    <h3>Qualidade</h3>
                    <p>Compromisso inabalável com a qualidade em todos os aspectos: desde a seleção de fornecedores até o atendimento ao cliente final.</p>
                </div>
                
        </div>
    </section>

    <!-- Team Section -->
    <section class="section team-section">
        <div class="container">
            <h2 class="section-title">Nossa Equipe</h2>
            <div class="team-grid">
                <div class="team-member fade-in">
                    <div class="member-photo">
                        <img src="img/caii.png" alt="Caio Machado">
                    </div>
                    <h3>Caio Machado</h3>
                    <div class="role">DEV TEAM</div>
                </div>
                <div class="team-member fade-in">
                    <div class="member-photo">
                        <img src="img/lucas (1).png" alt="Lucas Henrique">
                    </div>
                    <h3>Lucas Henrique</h3>
                    <div class="role">DEV TEAM</div>
                </div>
                <div class="team-member fade-in">
                    <div class="member-photo">
                        <img src="img/sophiaruiz.png" alt="Sophia Ruiz">
                    </div>
                    <h3>Sophia Ruiz</h3>
                    <div class="role">SCRUM MASTER</div>
                </div>
                <div class="team-member fade-in">
                    <div class="member-photo">
                        <img src="img/ana.png" alt="Ana Victoria">
                    </div>
                    <h3>Ana Victória</h3>
                    <div class="role">DESIGNER DE PRODUTOS</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section - CARDS ESTILIZADOS -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Nosso Processo de Criação</h2>
            <div class="process-steps">
                <div class="process-step fade-in">
                    <div class="step-number">1</div>
                    <h3>Pesquisa & Inspiração</h3>
                    <p>Estudamos tendências globais, comportamento do consumidor e buscamos inspiração em arte, natureza e cultura para criar conceitos únicos.</p>
                </div>
                <div class="process-step fade-in">
                    <div class="step-number">2</div>
                    <h3>Desenvolvimento</h3>
                    <p>Nossa equipe de perfumistas trabalha na criação das fórmulas, testando diferentes combinações de notas até encontrar a harmonia perfeita.</p>
                </div>
                <div class="process-step fade-in">
                    <div class="step-number">3</div>
                    <h3>Testes de Qualidade</h3>
                    <p>Realizamos rigorosos testes de qualidade, durabilidade e segurança, garantindo que cada fragrância atenda aos mais altos padrões.</p>
                </div>
               
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">O Que Nossos Clientes Dizem</h2>
            <div class="testimonials-grid">
                <div class="testimonial fade-in">
                    <div class="testimonial-text">
                        A Lavelle mudou completamente minha relação com perfumes. A qualidade é excepcional e o atendimento é sempre impecável. Recomendo de olhos fechados!
                    </div>
                    <div class="testimonial-author">Maria Santos</div>
                    <div class="testimonial-rating">★★★★★</div>
                </div>
                <div class="testimonial fade-in">
                    <div class="testimonial-text">
                        Compro na Lavelle há mais de 5 anos. A variedade de produtos é incrível e sempre encontro fragrâncias exclusivas que não acho em outros lugares.
                    </div>
                    <div class="testimonial-author">João Oliveira</div>
                    <div class="testimonial-rating">★★★★★</div>
                </div>
                <div class="testimonial fade-in">
                    <div class="testimonial-text">
                        O que mais me impressiona é a consultoria personalizada. Eles realmente entendem do assunto e sempre me ajudam a escolher o perfume perfeito.
                    </div>
                    <div class="testimonial-author">Ana Paula</div>
                    <div class="testimonial-rating">★★★★★</div>
                </div>
            </div>
        </div>
    </section>

    <footer>
      <?php include 'footer.php'; ?>
    </footer>

    <script>
        // Animação de scroll
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            });

            document.querySelectorAll('.story-text, .story-image, .timeline-item, .value-card, .team-member, .process-step, .testimonial').forEach(el => {
                observer.observe(el);
            });
        });
    </script>
</body>
</html>