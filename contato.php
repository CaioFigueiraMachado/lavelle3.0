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
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - LAVELLE Perfumes</title>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
        
        /* Hero Section com Vídeo */
        .page-hero {
            position: relative;
            height: 50vh;
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
        
        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6));
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
        
        /* Contact Section */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: start;
        }
        
        .contact-form {
            background-color: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .contact-form h2 {
            font-size: 28px;
            margin-bottom: 30px;
            color: #000;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #000;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
            font-family: inherit;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #8b7355;
        }
        
        .form-textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .submit-btn {
            display: inline-block;
            background-color: #000;
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        
        .submit-btn:hover {
            background-color: #333;
        }
        
        /* Contact Info */
        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .info-card {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
            border-left: 4px solid #8b7355;
        }
        
        .info-card:hover {
            transform: translateY(-5px);
        }
        
        .info-card h3 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #000;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .info-card p {
            color: #666;
            margin-bottom: 8px;
            line-height: 1.6;
        }
        
        .info-card a {
            color: #8b7355;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .info-card a:hover {
            color: #000;
            text-decoration: underline;
        }
        
        /* Map Section */
        .map-section {
            background-color: #f5f5f5;
        }
        
        .map-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: 400px;
        }
        
        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        
        /* FAQ Section */
        .faq-grid {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .faq-item {
            background: white;
            margin-bottom: 15px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .faq-item:hover {
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        
        .faq-question {
            background: white;
            color: #000;
            padding: 25px;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .faq-question:hover {
            background: #f9f5f0;
        }
        
        .faq-answer {
            padding: 0 25px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
            background: #f9f9f9;
        }
        
        .faq-answer.active {
            padding: 25px;
            max-height: 200px;
        }
        
        .faq-answer p {
            color: #666;
            line-height: 1.6;
        }
        
        .faq-toggle {
            font-size: 20px;
            transition: transform 0.3s ease;
            color: #8b7355;
        }
        
        .faq-toggle.active {
            transform: rotate(45deg);
        }
        
        /* Error Styles */
        .error {
            border-color: #e74c3c !important;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
        
        /* Notification */
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
            
            .page-hero p {
                font-size: 18px;
            }
            
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            
            .contact-form {
                padding: 30px 20px;
            }
            
            .info-card {
                padding: 20px;
            }
            
            .map-container {
                height: 300px;
            }
            
            .faq-question {
                padding: 20px;
            }
            
            .faq-answer.active {
                padding: 20px;
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
        <div class="container">
            <div class="header-top">
                <div class="logo">LAVELLE</div>
                <nav>
                    <ul>
                        <li><a href="index.php">INÍCIO</a></li>
                        <li><a href="paginaprodutos.php">PRODUTOS</a></li>
                        <li><a href="sobre.php">SOBRE</a></li>
                        <li><a href="contato.php" style="color: #8b7355;">CONTATO</a></li>
                        
                        <!-- Menu do Usuário -->
                        <?php if ($usuarioLogado): ?>
                            <div class="user-menu">
                                <li><a href="perfil.php" class="profile-link">MEU PERFIL</a></li>
                                <li><a href="logout.php">SAIR</a></li>
                            </div>
                        <?php else: ?>
                           
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section com Vídeo -->
    <section class="page-hero">
        <video class="hero-video" autoplay muted loop playsinline>
            <source src="contato.mp4" type="video/mp4">
            <source src="hero-video.webm" type="video/webm">
            <!-- Fallback para navegadores que não suportam vídeo -->
            Seu navegador não suporta o elemento de vídeo.
        </video>
        <div class="video-overlay"></div>
        <div class="container">
            <div class="hero-content fade-in">
                <h1>Entre em Contato</h1>
                <p>Estamos aqui para ajudar você a encontrar a fragrância perfeita</p>
                <a href="paginaprodutos.php" class="btn">Explorar Produtos</a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="section">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-form fade-in">
                    <h2>Envie sua Mensagem</h2>
                    <form id="contactForm">
                        <div class="form-group">
                            <label class="form-label" for="name">Nome Completo *</label>
                            <input type="text" class="form-input" id="name" name="name" required>
                            <span class="error-message" id="nameError"></span>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="email">E-mail *</label>
                            <input type="email" class="form-input" id="email" name="email" required>
                            <span class="error-message" id="emailError"></span>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="phone">Telefone</label>
                            <input type="tel" class="form-input" id="phone" name="phone">
                            <span class="error-message" id="phoneError"></span>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="subject">Assunto *</label>
                            <select class="form-select" id="subject" name="subject" required>
                                <option value="">Selecione um assunto</option>
                                <option value="duvida">Dúvida sobre produtos</option>
                                <option value="pedido">Informações sobre pedido</option>
                                <option value="troca">Trocas e devoluções</option>
                                <option value="sugestao">Sugestões</option>
                                <option value="outro">Outro</option>
                            </select>
                            <span class="error-message" id="subjectError"></span>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="message">Mensagem *</label>
                            <textarea class="form-textarea" id="message" name="message" placeholder="Conte-nos como podemos ajudar..." required></textarea>
                            <span class="error-message" id="messageError"></span>
                        </div>
                        
                        <button type="submit" class="submit-btn">Enviar Mensagem</button>
                    </form>
                </div>
                
                <div class="contact-info">
                    <div class="info-card fade-in">
                        <h3> Endereço</h3>
                        <p>Av. Monsenhor Theodomiro Lobo, 100</p>
                        <p>Parque Res. Maria Elmira, Caçapava - SP</p>
                        <p>CEP: 12285-050</p>
                    </div>
                    
                    <div class="info-card fade-in">
                        <h3> Telefones</h3>
                        <p><a href="tel:+5512998516345">(12) 99851-6345</a> - WhatsApp</p>
                        <p>Atendimento: Segunda a Sexta, 9h às 18h</p>
                    </div>
                    
                    <div class="info-card fade-in">
                        <h3> E-mail</h3>
                        <p><a href="mailto:contato@lavelle.com.br">contato@lavelle.com.br</a></p>
                        <p><a href="mailto:vendas@lavelle.com.br">vendas@lavelle.com.br</a></p>
                        <p>Resposta em até 24 horas</p>
                    </div>
                    
                    <div class="info-card fade-in">
                        <h3> Horário de Funcionamento</h3>
                        <p><strong>Segunda a Sexta:</strong> 9h às 18h</p>
                        <p><strong>Sábado:</strong> 9h às 13h</p>
                        <p><strong>Domingo:</strong> Fechado</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="section map-section">
        <div class="container">
            <h2 class="section-title">Nossa Localização</h2>
            <div class="map-container fade-in">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3669.5394011259195!2d-45.70961732485665!3d-23.11395107910983!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94cc53fe5d561195%3A0xf8b1e6391017595b!2sSesi%20Ca%C3%A7apava!5e0!3m2!1spt-BR!2sbr!4v1757698776755!5m2!1spt-BR!2sbr" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="section">
        <div class="container">
            <h2 class="section-title">Perguntas Frequentes</h2>
            <div class="faq-grid">
                <div class="faq-item fade-in">
                    <div class="faq-question" onclick="toggleFAQ(0)">
                        <span>Como posso saber se um perfume é original?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Todos os nossos perfumes são 100% originais e importados diretamente dos fabricantes. Fornecemos certificado de autenticidade e nota fiscal em todas as compras.</p>
                    </div>
                </div>
                
                <div class="faq-item fade-in">
                    <div class="faq-question" onclick="toggleFAQ(1)">
                        <span>Qual o prazo de entrega?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Para São Paulo capital: 1-2 dias úteis. Para outras regiões: 3-7 dias úteis. Oferecemos frete grátis para compras acima de R$ 200,00.</p>
                    </div>
                </div>
                
                <div class="faq-item fade-in">
                    <div class="faq-question" onclick="toggleFAQ(2)">
                        <span>Posso trocar se não gostar do perfume?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Sim! Você tem 30 dias para trocar produtos lacrados. Para produtos abertos, oferecemos troca apenas em caso de defeito de fabricação.</p>
                    </div>
                </div>
                
                <div class="faq-item fade-in">
                    <div class="faq-question" onclick="toggleFAQ(3)">
                        <span>Vocês têm loja física?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Sim! Nossa loja física fica na Av. Monsenhor Theodomiro Lobo, 100 - Caçapava. Você pode visitar para conhecer pessoalmente nossos produtos e receber consultoria especializada.</p>
                    </div>
                </div>
                
                <div class="faq-item fade-in">
                    <div class="faq-question" onclick="toggleFAQ(4)">
                        <span>Como escolher o perfume ideal?</span>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>Recomendamos considerar a ocasião de uso, sua personalidade e preferências olfativas. Nossa equipe está disponível para consultoria gratuita via WhatsApp ou na loja física.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
   <section class="newsletter">
        <div class="container">
            <h2>Junte-se ao Nosso Mundo de Fragrâncias</h2>
            <p>Receba novidades, lançamentos exclusivos e ofertas especiais diretamente no seu e-mail.</p>
            <form class="newsletter-form" id="newsletterForm">
                <input type="email" class="newsletter-input" id="newsletterEmail" placeholder="Seu melhor e-mail" required>
                <button type="submit" class="newsletter-btn">Assinar</button>
            </form>
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

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>

         // Newsletter form com SweetAlert2
        document.getElementById('newsletterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('newsletterEmail').value;
            
            // Validação básica de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                Swal.fire({
                    title: 'E-mail inválido!',
                    text: 'Por favor, insira um endereço de e-mail válido.',
                    icon: 'warning',
                    confirmButtonColor: '#8b7355'
                });
                return;
            }
            
            // Mostra SweetAlert2 de sucesso
            Swal.fire({
                title: 'Bem-vindo(a) ao Mundo Lavelle!',
                html: `
                    <div style="text-align: center;">
                        <div style="font-size: 48px; margin-bottom: 20px;"></div>
                        <p style="font-size: 18px; margin-bottom: 15px;"><strong>Inscrição realizada com sucesso!</strong></p>
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; margin: 15px 0;">
                            <p style="margin: 5px 0;"><strong>E-mail cadastrado:</strong> ${email}</p>
                            <p style="margin: 5px 0; color: #666;">Você receberá nossas novidades em primeira mão!</p>
                        </div>
                        <p style="color: #8b7355; font-style: italic; margin-top: 20px;">
                            Prepare-se para descobrir fragrâncias exclusivas!
                        </p>
                    </div>
                `,
                icon: 'success',
                confirmButtonText: 'Continuar Navegando',
                confirmButtonColor: '#8b7355',
                width: 500,
                customClass: {
                    popup: 'newsletter-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Limpa o formulário
                    this.reset();
                }
            });
        });

        // Animação de scroll
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    }
                });
            });

            document.querySelectorAll('.contact-form, .info-card, .map-container, .faq-item').forEach(el => {
                observer.observe(el);
            });

            setupForms();
        });

        // Funções do FAQ
        function toggleFAQ(index) {
            const faqItems = document.querySelectorAll('.faq-item');
            const currentItem = faqItems[index];
            const answer = currentItem.querySelector('.faq-answer');
            const toggle = currentItem.querySelector('.faq-toggle');
            
            // Fecha todas as outras FAQs
            faqItems.forEach((item, i) => {
                if (i !== index) {
                    item.querySelector('.faq-answer').classList.remove('active');
                    item.querySelector('.faq-toggle').classList.remove('active');
                }
            });
            
            // Alterna a FAQ atual
            answer.classList.toggle('active');
            toggle.classList.toggle('active');
        }

        // Validação do formulário
        function validateForm() {
            let isValid = true;
            
            // Remove erros anteriores
            document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            
            // Validação do nome
            const name = document.getElementById('name');
            if (name.value.trim().length < 2) {
                showFieldError('name', 'Nome deve ter pelo menos 2 caracteres');
                isValid = false;
            }
            
            // Validação do email
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                showFieldError('email', 'E-mail inválido');
                isValid = false;
            }
            
            // Validação do telefone (opcional)
            const phone = document.getElementById('phone');
            if (phone.value && phone.value.replace(/\D/g, '').length < 10) {
                showFieldError('phone', 'Telefone deve ter pelo menos 10 dígitos');
                isValid = false;
            }
            
            // Validação do assunto
            const subject = document.getElementById('subject');
            if (!subject.value) {
                showFieldError('subject', 'Selecione um assunto');
                isValid = false;
            }
            
            // Validação da mensagem
            const message = document.getElementById('message');
            if (message.value.trim().length < 10) {
                showFieldError('message', 'Mensagem deve ter pelo menos 10 caracteres');
                isValid = false;
            }
            
            return isValid;
        }

        function showFieldError(fieldId, message) {
            const field = document.getElementById(fieldId);
            const errorDiv = document.getElementById(fieldId + 'Error');
            field.classList.add('error');
            errorDiv.textContent = message;
        }

        function setupForms() {
            const contactForm = document.getElementById('contactForm');
            
            if (contactForm) {
                contactForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (validateForm()) {
                        // Usando SweetAlert2 para o formulário de contato também
                        Swal.fire({
                            title: 'Mensagem Enviada!',
                            text: 'Obrigado pelo seu contato. Responderemos em breve.',
                            icon: 'success',
                            confirmButtonColor: '#8b7355',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.reset();
                            }
                        });
                    }
                });
            }
            
            // Validação em tempo real
            const nameField = document.getElementById('name');
            const emailField = document.getElementById('email');
            const messageField = document.getElementById('message');
            
            if (nameField) {
                nameField.addEventListener('blur', function() {
                    if (this.value.trim().length < 2 && this.value.length > 0) {
                        showFieldError('name', 'Nome deve ter pelo menos 2 caracteres');
                    }
                });
            }
            
            if (emailField) {
                emailField.addEventListener('blur', function() {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (this.value && !emailRegex.test(this.value)) {
                        showFieldError('email', 'E-mail inválido');
                    }
                });
            }
            
            if (messageField) {
                messageField.addEventListener('blur', function() {
                    if (this.value.trim().length < 10 && this.value.length > 0) {
                        showFieldError('message', 'Mensagem deve ter pelo menos 10 caracteres');
                    }
                });
            }
        }

        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.textContent = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 4000);
        }
    </script>
</body>
</html>