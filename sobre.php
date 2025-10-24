<!DOCTYPE html>
<html lang="pt-BR">
<head>
<?php
session_start();
include 'conexao.php';

// Verificar se usuário está logado
$usuarioLogado = false;
$usuarioNome = "";

if (isset($_SESSION['usuario_id'])) {
    $usuarioLogado = true;
    $usuarioNome = $_SESSION['usuario_nome'];
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
        
        /* Process Section */
        .process-steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .process-step {
            text-align: center;
            padding: 30px;
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            background: #8b7355;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            margin: 0 auto 20px;
        }
        
        .process-step h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #000;
        }
        
        .process-step p {
            color: #666;
            line-height: 1.6;
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
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div class="header-top">
                <div class="logo">LAVELLE</div>
                <nav>
                    <ul>
                        <li><a href="index.php">INÍCIO</a></li>
                        <li><a href="paginaprodutos.php">PRODUTOS</a></li>
                        <li><a href="sobre.php" style="color: #8b7355;">SOBRE</a></li>
                        <li><a href="contato.php">CONTATO</a></li>
                        
                        <!-- Menu do Usuário -->
                        <?php if ($usuarioLogado): ?>
                            <div class="user-menu">
                                <li><a href="perfil.php" class="profile-link">MEU PERFIL</a></li>
                                <li><a href="logout.php">SAIR</a></li>
                            </div>
                        <?php else: ?>
                            <div class="user-menu">
                                <li><a href="login.php">ENTRAR</a></li>
                                <li><a href="cadastro.php">CADASTRAR</a></li>
                            </div>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
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
                <a href="produtos.php" class="btn">Explorar Produtos</a>
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
                    <img src="lc.png" alt="Nossa História">
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
                        <div class="timeline-year">2020</div>
                        <h3>Fundação</h3>
                        <p>Abertura do primeiro site, com foco em perfumes importados de alta qualidade.</p>
                    </div>
                </div>
                <div class="timeline-item fade-in">
                    <div class="timeline-content">
                        <div class="timeline-year">2021</div>
                        <h3>Expansão Digital</h3>
                        <p>Lançamento da loja online, permitindo atender clientes em todo o Brasil com entrega rápida e segura.</p>
                    </div>
                </div>
                <div class="timeline-item fade-in">
                    <div class="timeline-content">
                        <div class="timeline-year">2022</div>
                        <h3>Linha Própria</h3>
                        <p>Desenvolvimento da primeira linha de perfumes exclusivos Lavelle, criada por perfumistas renomados.</p>
                    </div>
                </div>
                <div class="timeline-item fade-in">
                    <div class="timeline-content">
                        <div class="timeline-year">2023</div>
                        <h3>Certificação Premium</h3>
                        <p>Conquista das principais certificações de qualidade e sustentabilidade do mercado de cosméticos.</p>
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
                        <img src="caii.png" alt="Caio Machado">
                    </div>
                    <h3>Caio Machado</h3>
                    <div class="role">DEV TEAM</div>
                </div>
                <div class="team-member fade-in">
                    <div class="member-photo">
                        <img src="lucas (1).png" alt="Lucas Henrique">
                    </div>
                    <h3>Lucas Henrique</h3>
                    <div class="role">DEV TEAM</div>
                </div>
                <div class="team-member fade-in">
                    <div class="member-photo">
                        <img src="sophia.png" alt="Sophia Ruiz">
                    </div>
                    <h3>Sophia Ruiz</h3>
                    <div class="role">SCRUM MASTER</div>
                </div>
                <div class="team-member fade-in">
                    <div class="member-photo">
                        <img src="ana.png" alt="Ana Victoria">
                    </div>
                    <h3>Ana Victoria</h3>
                    <div class="role">DESIGNER DE PRODUTOS</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
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
                <div class="process-step fade-in">
                    <div class="step-number">4</div>
                    <h3>Produção</h3>
                    <p>Produção em pequenos lotes para garantir frescor e qualidade, utilizando apenas ingredientes premium selecionados.</p>
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