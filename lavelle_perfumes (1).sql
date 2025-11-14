-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14-Nov-2025 às 12:34
-- Versão do servidor: 10.4.27-MariaDB
-- versão do PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `lavelle_perfumes`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_pedido` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pendente',
  `metodo_pagamento` varchar(50) DEFAULT NULL,
  `endereco_entrega` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `data_pedido`, `total`, `status`, `metodo_pagamento`, `endereco_entrega`, `created_at`) VALUES
(1, 1, '2025-10-27 09:37:37', '299.90', 'completado', NULL, NULL, '2025-10-27 12:37:37'),
(2, 1, '2025-10-27 09:37:37', '349.90', 'processando', NULL, NULL, '2025-10-27 12:37:37');

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `descricao_breve` varchar(255) DEFAULT NULL,
  `descricao_longa` text DEFAULT NULL,
  `preco` decimal(10,2) NOT NULL,
  `imagem` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `categoria` varchar(50) DEFAULT 'Compartilhável',
  `notas_saida` varchar(255) DEFAULT NULL,
  `notas_coracao` varchar(255) DEFAULT NULL,
  `notas_fundo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `descricao_breve`, `descricao_longa`, `preco`, `imagem`, `created_at`, `updated_at`, `categoria`, `notas_saida`, `notas_coracao`, `notas_fundo`) VALUES
(13, 'Lavelle Aurore Florale', NULL, 'Aurore Florale, um Eau de Parfum da Lavelle, captura a essência da natureza com sua fragrância.', 'Aurore Florale da Lavelle é um convite para um despertar perfumado, uma sinfonia olfativa que celebra a delicadeza e a força da natureza. Este Eau de Parfum se revela em uma composição floral aldeídica, onde notas frescas e cintilantes se entrelaçam com a riqueza das flores, criando uma fragrância que é ao mesmo tempo vibrante e envolvente.', '299.90', 'img/Lavelle Aurore Florale.png', '2025-10-31 16:39:58', '2025-11-10 13:05:18', 'Masculino', 'Folhas Verdes, Bergamota, Pêra', 'Lírio do Vale, Jasmim, Rosa, Acorde Floral Branco', 'Almíscar Branco, Cedro, Âmbar'),
(15, 'Lavelle Étoile', NULL, 'Um perfume radiante que une a delicadeza flores ao toque envolvente da baunilha.', 'Lavelle Étoile é um convite a uma jornada olfativa sublime, onde a doçura se encontra com a sofisticação. A sua abertura revela um acorde floral sutil, uma melodia etérea de flores delicadas que dançam suavemente, preparando o palco para o coração da fragrância. Não são flores comuns, mas sim um buquê cuidadosamente selecionado para evocar a leveza e a beleza natural, um véu perfumado que acaricia a pele.', '350.00', 'img/LavelleÉtoile.png', '2025-11-03 14:08:49', '2025-11-10 13:05:55', 'Feminino', 'Pêra, Mandarina, Flor de Laranjeira', 'Jasmim, Tuberosa, Gardênia', 'Baunilha, Âmbar, Almíscar, Sândalo'),
(16, 'Lavelle Essence Verte', NULL, 'Frescor verde e floral que transmite pureza e vitalidade.', 'Essence Verte se abre com uma explosão de frescor, como o orvalho da manhã em um jardim botânico recém-despertado. O \"frescor verde e floral\" não é apenas uma nota, mas uma experiência: imagine-se caminhando por um campo orvalhado, onde as folhas exalam seu aroma vital e as flores delicadas liberam seus eflúvios mais puros. Esta combinação cria uma sensação imediata de clareza, leveza e um otimismo contagiante.', '400.00', 'img/Lavelle EssenceVerte.png', '2025-11-03 14:14:23', '2025-11-10 13:06:40', 'Compartilhável', 'Acorde Verde, Bergamota, Limão', 'Frésia, Chá Verde, Jasmim', 'Cedro, Almíscar, Âmbar'),
(17, 'Lavelle Rouge Amour', NULL, 'Frutas vibrantes com um fundo doce e envolvente.', 'A primeira borrifada de Rouge Amour é uma explosão efervescente de frutas vibrantes. Imagine uma cesta farta de frutas vermelhas e exóticas, suculentas e cheias de vida, que despertam os sentidos com sua acidez alegre e doçura natural. Esta abertura é como o primeiro encontro, cheio de expectativa e um entusiasmo contagiante, uma promessa de momentos deliciosos que estão por vir. A combinação frutada é efervescente e luminosa, criando um rastro inicial de pura energia e alegria.', '389.90', 'img/Lavelle RougeAmour.png', '2025-11-03 14:18:22', '2025-11-10 13:07:19', 'Feminino', 'Frutas Vermelhas, Framboesa, Lichia', 'Rosa, Jasmim, Lírio', 'Baunilha, Almíscar, Sândalo'),
(18, 'Lavelle Belle Lune', NULL, 'Mistério suave que une flores envolventes e notas cremosas.', 'A primeira impressão de Belle Lune é um abraço delicado de um mistério suave, que imediatamente transporta os sentidos para um jardim noturno, onde a luz da lua ilumina as flores. Não é um mistério pesado, mas sim etéreo e intrigante, como um segredo sussurrado ao vento. Esta abertura já indica a complexidade e a profundidade que estão por vir, prometendo uma experiência olfativa única e memorável.', '380.00', 'img/Lavelle BelleLune.png', '2025-11-03 14:20:17', '2025-11-10 13:07:48', 'Feminino', 'Pêra, Bergamota, Frésia', 'Jasmim, Flor de Laranjeira, Tuberosa', 'Baunilha, Almíscar, Sândalo, Âmbar'),
(20, 'Lavelle L’Essence Divine', NULL, 'Elegância moderna que une flores delicadas e frescor cítrico.', 'Desde o primeiro contato, L’Essence Divine se revela com um frescor cítrico radiante, como os primeiros raios de sol da manhã que tocam a pele. Notas vivazes de bergamota, limão siciliano ou mandarina dançam efervescentes, despertando os sentidos e infundindo uma sensação imediata de clareza, energia e otimismo. Este frescor inicial não é apenas revigorante, mas serve como um convite luminoso para o coração delicado da fragrância.', '349.99', 'img/Lavelle L’EssenceDivine.png', '2025-11-03 14:25:55', '2025-11-10 13:08:23', 'Feminino', 'Bergamota, Laranja, Pêra', 'Jasmim, Flor de Laranjeira, Tuberosa', 'Baunilha, Sândalo, Almíscar'),
(21, 'Lavelle Verde Élégant', NULL, 'Vitalidade fresca com cítricos e flores leves.', 'A jornada olfativa de Verde Élégant começa com uma dose vibrante de cítricos, como um sopro revigorante de brisa matinal. Notas efervescentes de limão verde, grapefruit suculento ou bergamota espumante dançam na abertura, infundindo uma sensação imediata de euforia e clareza. Este frescor cítrico não é apenas energizante; ele prepara o palco para a harmonia que está por vir, evocando a sensação de um campo verde banhado pelo sol.', '299.90', 'img/Lavelle VerdeÉlégant.png', '2025-11-03 14:28:25', '2025-11-10 13:09:26', 'Compartilhável', 'Limão, Bergamota, Folhas Verdes', 'Lírio do Vale, Jasmim, Chá Verde', 'Cedro, Almíscar Branco, Âmbar'),
(22, 'Lavelle Belle Harmonie', NULL, 'Feminilidade radiante em um floral frutado sofisticado.', 'A primeira borrifada de Belle Harmonie se revela com uma abertura cintilante de notas frutadas. Pense em frutas suculentas e alegres como a pera, a lichia ou o pêssego, que oferecem uma doçura natural e um frescor convidativo. Estas frutas vibrantes despertam os sentidos com sua energia positiva, preparando o cenário para o coração floral que está por vir. É um convite para experimentar a leveza e a doçura da vida.', '229.99', 'img/Lavelle BelleHarmonie.png', '2025-11-03 14:32:47', '2025-11-10 13:10:12', 'Feminino', 'Pêssego, Lichia, Frésia', 'Rosa, Jasmim, Lírio do Vale', 'Baunilha, Sândalo, Almíscar'),
(23, 'Lavelle Urban Flame', NULL, 'Um perfume ousado e envolvente, que mistura calor e sofisticação em cada nota.', 'Desde o primeiro spray, Urban Flame se revela com uma audácia inegável, uma explosão de vitalidade que é ao mesmo tempo intrigante e convidativa. A fragrância é uma fusão magnética que mistura calor e sofisticação em cada nota. Pense em uma abertura que pode incluir toques picantes de pimenta preta ou cardamomo, combinados com um frescor inesperado de bergamota ou grapefruit, criando um contraste dinâmico que reflete o ritmo acelerado da cidade.', '329.99', 'img/Lavelle UrbanFlame.png', '2025-11-03 14:35:58', '2025-11-10 13:10:52', 'Masculino', 'Bergamota, Pimenta Preta, Cardamomo', 'Jasmim, Gerânio, Lavanda', 'Âmbar, Baunilha, Almíscar, Patchouli'),
(24, 'Lavelle Classic Oud', NULL, 'Um perfume atemporal, que une tradição e intensidade.', 'Desde o primeiro contato, Classic Oud revela seu caráter atemporal e sua complexidade hipnotizante. A fragrância se abre com notas que preparam para a opulência que está por vir, talvez um toque de especiarias quentes como açafrão ou cominho, ou um resinoso balsâmico, que introduzem o opulento coração do perfume. É uma abertura que sugere uma história rica e uma jornada olfativa profunda.', '499.99', 'img/Lavelle ClassicOud.png', '2025-11-03 14:41:18', '2025-11-10 13:11:43', 'Masculino', 'Açafrão, Framboesa, Rosa', 'Oud, Patchouli, Jasmim', 'Âmbar, Almíscar, Sândalo, Couro'),
(26, 'Lavelle Intense Noir', NULL, 'Um perfume marcante que une especiarias e um toque amadeirado.', 'Desde o primeiro spray, Intense Noir revela seu caráter marcante, com uma abertura poderosa que cativa instantaneamente. É um convite para explorar um mundo de complexidade e sofisticação. A fragrância se desdobra com uma fusão intrigante de especiarias. Imagine a vivacidade do cardamomo, o calor da pimenta preta e a profundidade da noz-moscada, criando um bouquet olfativo que é ao mesmo tempo picante, vibrante e misterioso. Estas especiarias não são apenas um toque, mas o coração pulsante do perfume, conferindo uma energia ardente e um carisma inegável. À medida que as especiarias se e', '399.90', 'img/Lavelle Intense Noir.png', '2025-11-07 12:37:09', '2025-11-10 13:14:34', 'Masculino', 'Pimenta Preta, Bergamota, Noz-moscada', 'Patchouli, Cedro, Âmbar', 'Couro, Fava Tonka, Almíscar'),
(29, 'Lavelle Gold', NULL, 'Um perfume compartilhável de elegância dourada e luxo radiante.', 'Lavelle Gold Eau de Parfum é uma fragrância compartilhável que personifica a elegância atemporal e o luxo discreto. Apresentado em um frasco de vidro translúcido com linhas clássicas, o design minimalista é coroado por uma tampa dourada robusta e sofisticada, refletindo a essência preciosa que ele contém', '249.99', 'img/Lavelle Gold.png', '2025-11-07 13:52:50', '2025-11-10 13:17:12', 'Compartilhável', 'Bergamota, Pêssego, Mandarina', 'Jasmim, Flor de Laranjeira, Gardênia', 'Baunilha, Sândalo, Âmbar, Almíscar'),
(30, 'Lavelle Noir Intense', NULL, 'Um perfume marcante, profundo e sofisticado, feito para noites que pedem presença.', 'Lavelle Noir Intense é a assinatura do homem que não precisa falar alto para ser notado. A fragrância se abre com notas de pimenta preta e bergamota , um impacto intrigante e elegante. No coração, vetiver e cedro trazem um lado amadeirado de força e refinamento, enquanto o patchouli e o âmbar finalizam com calor e sensualidade duradoura. Ideal para noites especiais, encontros e situações onde confiança é o único acessório necessário.', '399.90', 'img/Lavelle Noir.png', '2025-11-07 14:19:55', '2025-11-10 13:17:56', 'Masculino', 'Bergamota, Pimenta Preta, Cardamomo', 'Gerânio, Lavanda, Patchouli', 'Cedro, Âmbar, Fava Tonka, Almíscar'),
(31, 'Lavelle Fleur de Lune', NULL, 'Flores brancas envoltas em um toque adocicado e sensual, inspirado no brilho da lua.', 'Lavelle Fleur de Lune é uma celebração do feminino delicado e, ao mesmo tempo, poderoso. Notas de saída de pêra e tangerina despertam leveza e frescor. No coração, jasmim e flor de laranjeira criam um buquê envolvente, quase hipnotizante. A base traz baunilha e sândalo, deixando um rastro cremoso e viciante. É o perfume de mulheres que transformam qualquer lugar que passam em poesia.', '349.99', 'img/Lavelle Fleur de Lune.png', '2025-11-07 14:23:19', '2025-11-10 14:14:48', 'Feminino', '', '', ''),
(32, 'Lavelle Rouge Essence', NULL, 'Doce, sedutor e envolvent, feito para quem gosta de perfume que marca.', 'Lavelle Rouge Essence é ousadia embalada em vermelho. Se abre com framboesa e mandarina, deliciosamente vibrantes. O coração traz rosa e praline, equilibrando doçura e elegância. Na base, baunilha, fava tonka e âmbar criam uma aura quente, doce e irresistível. Ideal para quem ama deixar um rastro inesquecível e não tem medo de ser o centro das atenções.', '199.99', 'img/Lavelle Rouge Essence.png', '2025-11-07 14:26:27', '2025-11-10 13:18:46', 'Feminino', 'Frutas Vermelhas, Pêra, Pimenta Rosa', 'Rosa, Jasmim, Lírio', 'Baunilha, Sândalo, Almíscar'),
(33, 'Lavelle Platinum', NULL, 'Elegância moderna. Um perfume limpo, caro e minimalista.', 'Lavelle Platinum é inspirado no universo do luxo discreto. A abertura traz bergamota e alecrim, criando um frescor refinado. No coração, notas de chá preto e sálvia adicionam sofisticação. O fundo de musk, iso e sândalo garante fixação prolongada com uma assinatura limpa e elegante. É o perfume para quem prefere qualidade a exagero, minimalista, porém inesquecível.', '299.90', 'img/Lavelle Platinum.png', '2025-11-07 14:28:15', '2025-11-10 13:19:26', 'Masculino', 'Bergamota, Limão, Gengibre', 'Lavanda, Alecrim, Gerânio', 'Cedro, Vetiver, Almíscar, Âmbar'),
(34, 'Lavelle Cloud', NULL, 'Frescor doce de maçã verde com jujuba.', 'Inspirado na sensação de flutuar. Maçã verde e pera gelada trazem frescor imediato, enquanto jujuba e flor de algodão criam um fundo adocicado e leve. Almíscar branco garante conforto e fixação suave.', '399.99', 'img/Lavelle Cloud.png', '2025-11-07 14:31:58', '2025-11-10 13:20:37', 'Compartilhável', 'Bergamota, Pêra, Algodão Doce', 'Coco, Chantilly, Jasmim', 'Baunilha, Almíscar, Sândalo'),
(35, 'Lavelle Pure Blossom', NULL, 'Floral suave e limpo, cheiro de banho caro.', 'Mandarina e chá branco abrem o perfume com pureza. Flor de laranjeira e magnólia trazem feminilidade discreta. Musk e sândalo deixam o rastro leve e elegante.', '249.99', 'img/Lavelle Pure Blossom.png', '2025-11-07 14:35:12', '2025-11-10 13:21:15', 'Compartilhável', 'Bergamota, Pêra, Notas Verdes', 'Flor de Laranjeira, Jasmim, Lírio do Vale', 'Almíscar Branco, Cedro, Âmbar'),
(36, 'Lavelle Citrus Wave', NULL, 'Explosão de refrescância com limão e notas marinhas.', 'Inspirado em ondas e energia. Limão, bergamota e acorde oceânico se misturam com ervas aromáticas. Base de cedro e musk para fixação limpa.', '199.99', 'img/Lavelle Citrus Wave.png', '2025-11-07 14:38:44', '2025-11-10 13:22:04', 'Compartilhável', 'Limão Siciliano, Bergamota, Notas Marinhas', 'Jasmim Aquático, Alecrim, Chá Verde', 'Cedro, Almíscar Branco, Âmbar'),
(37, 'Lavelle Velvet Sky', NULL, 'Doce leve com toque de lavanda e vanilla musk.', 'Lavanda cremosa com baunilha leve cria um perfume aconchegante e viciante. A sensação é de abraço, de calmaria, de paz.', '299.99', 'img/Lavelle Velvet Sky.png', '2025-11-07 14:40:36', '2025-11-10 13:22:46', 'Compartilhável', 'Bergamota, Lavanda, Pêra', 'Íris, Jasmim, Flor de Algodão', 'Baunilha, Almíscar, Sândalo'),
(38, 'Lavelle Forest Mist', NULL, 'Frescor de floresta, leve e herbal.', 'Notas de eucalipto, alecrim e folhas verdes criam sensação de ar puro. Base amadeirada com cedro e vetiver.', '199.99', 'img/Lavelle Forest Mist.png', '2025-11-07 14:43:07', '2025-11-10 13:23:25', 'Compartilhável', 'Folhas Verdes, Bergamota, Alecrim', 'Eucalipto, Menta, Lavanda', 'Cedro, Vetiver, Musgo de Carvalho'),
(39, 'Lavelle Blue Spirit', NULL, 'Aquático fresco e elegante.', 'Bergamota e hortelã criam frescor, enquanto notas aquáticas dão modernidade. Perfeito para calor ou academia.', '199.99', 'img/Lavelle Blue Spirit.png', '2025-11-07 14:44:41', '2025-11-10 13:24:11', 'Masculino', 'Limão, Menta, Acorde Aquático', 'Gerânio, Lavanda, Pimenta Preta', 'Cedro, Âmbar, Almíscar'),
(40, 'Lavelle Eclipse Leather', NULL, 'Couro e café, intenso e marcante.', 'Grapefruit e pimenta criam impacto, couro com café traz presença e sedução.', '229.99', 'img/Lavelle Eclipse Leather.png', '2025-11-07 14:49:35', '2025-11-10 13:56:40', 'Masculino', 'Toranja, Pimenta Rosa, Cardamomo', 'Café, Couro, Jasmim', 'Baunilha, Cedro, Patchouli, Fava Tonka'),
(41, 'Lavelle Urban Steel', NULL, 'Perfume de status. Frio, amadeirado, urbano.', 'Notas de metal + cedro + gengibre. Cheiro de homem moderno, executivo e minimalista.', '159.99', 'img/Lavelle Urban Steel.png', '2025-11-07 14:51:18', '2025-11-10 14:08:02', 'Masculino', 'Bergamota, Gengibre, Maçã', 'Gerânio, Lavanda, Sálvia', 'Cedro, Âmbar, Almíscar, Fava Tonka'),
(42, 'Lavelle Savage Night', NULL, 'Selvagem, atrativo, explosivo.', 'Pimenta, ambroxan e bergamota formam um perfume hipnótico para a noite.', '199.99', 'img/Lavelle Savage Night.png', '2025-11-07 14:52:44', '2025-11-10 14:10:13', 'Masculino', 'Bergamota, Pimenta Preta, Elemi', 'Gerânio, Lavanda, Vetiver', 'Cedro, Ambroxan, Fava Tonka'),
(43, 'Lavelle Royal Wood', NULL, 'Madeira pura, quente e imponente.', 'Sândalo, cedro e âmbar. A sensação de elegância clássica.', '199.99', 'img/Lavelle Royal Wood.png', '2025-11-07 14:54:26', '2025-11-10 14:11:02', 'Masculino', 'Bergamota, Pimenta Rosa, Cardamomo', 'Cedro, Patchouli, Sândalo', 'Âmbar, Almíscar, Fava Tonka'),
(44, 'Lavelle Thunder', NULL, 'Energia, impacto e adrenalina.', 'Gengibre, limão e vetiver. Aroma que transmite força e atitude.', '159.99', 'img/Lavelle Thunder.png', '2025-11-07 15:01:17', '2025-11-10 14:14:14', 'Masculino', 'Limão, Gengibre, Pimenta Preta', 'Lavanda, Sálvia, Gerânio', 'Cedro, Âmbar, Almíscar'),
(48, 'Lavelle Aureum', NULL, 'A essência dourada da conquista. Um perfume sofisticado que irradia luxo e confiança.', 'Lavelle Aureum é mais do que um perfume, é uma declaração. Envolto em um frasco de design clássico e elegante, sua tonalidade dourada promete uma fragrância rica e envolvente. Com a inscrição \'Intensidade que Conquista\', ele é feito para quem busca deixar uma marca inesquecível. As notas sutis de flores brancas ao fundo, juntamente com a apresentação em um pedestal texturizado e o drapeado suave, sugerem uma experiência olfativa premium, ideal para momentos que exigem distinção e poder. Lavelle Aurum é a fragrância para quem vive intensamente e conquista com graça e força.', '299.90', 'img/Lavelle Aureum.png', '2025-11-10 12:59:25', '2025-11-10 12:59:25', 'Compartilhável', 'Bergamota, Pêssego, Pimenta Rosa', 'Jasmim, Flor de Laranjeira, Rosa', 'Baunilha, Sândalo, Âmbar'),
(49, 'Lavelle Horizon', NULL, 'A essência da liberdade e do frescor. Uma fragrância masculina que evoca a imensidão.', 'Lavelle Horizon encapsula a sensação revigorante de um novo começo, a vastidão do oceano e a brisa leve. O frasco, com seu design clássico e robusto, abriga um líquido de tonalidade azul-claro cristalina, que remete à pureza da água e ao céu aberto. Detalhes em dourado na tampa contrastam elegantemente com o azul, adicionando um toque de sofisticação. A frase \'Liberdade e Frescor\' no rótulo reforça a proposta de uma fragrância que inspira aventura, expansão e bem-estar.', '349.99', 'img/Lavelle Horizon.png', '2025-11-10 13:01:49', '2025-11-10 13:01:49', 'Masculino', 'Limão Siciliano, Menta, Acorde Aquático', 'Lavanda, Gerânio, Acorde Verde', 'Vetiver, Cedro, Almíscar Branco'),
(50, 'Lavelle Rose Sublime', NULL, 'A delicadeza e a paixão da rosa em uma fragrância feminina e envolvente, com um toque de sofisticação', 'Lavelle Rose Sublime é a celebração da feminilidade e da beleza atemporal da rosa. O frasco, com seu design elegante e clássico, revela um líquido de tonalidade rosada suave, complementado por detalhes em ouro rosé, transmitindo uma sensação de delicadeza e requinte. A menção \'Coleção Feminina Lavelle\' reforça seu posicionamento para a mulher que aprecia a graciosidade. A iluminação suave e o fundo em tons pastéis e roseados, juntamente com a superfície texturizada que lembra um tecido delicado, evocam uma atmosfera de romance, suavidade e um luxo acessível.', '279.90', 'img/Lavelle Rose Sublime.jpg', '2025-11-10 13:03:27', '2025-11-10 13:03:27', 'Feminino', 'Pêra, Lichia, Bergamota', 'Rosa Damascena, Peônia, Jasmim', 'Baunilha, Almíscar Branco, Cedro'),
(51, 'Lavelle Aureum', NULL, 'A essência dourada da conquista. Um perfume sofisticado que irradia luxo e confiança.', 'Lavelle Aureum é mais do que um perfume, é uma declaração. Envolto em um frasco de design clássico e elegante, sua tonalidade dourada promete uma fragrância rica e envolvente. Com a inscrição \'Intensidade que Conquista\', ele é feito para quem busca deixar uma marca inesquecível. As notas sutis de flores brancas ao fundo, juntamente com a apresentação em um pedestal texturizado e o drapeado suave, sugerem uma experiência olfativa premium, ideal para momentos que exigem distinção e poder.', '299.90', 'img/Lavelle Aureum.png', '2025-11-10 13:15:58', '2025-11-10 13:15:58', 'Compartilhável', 'Bergamota, Pêssego, Pimenta Rosa', 'Jasmim, Flor de Laranjeira, Caramelo', 'Baunilha, Sândalo, Âmbar, Almíscar');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `data_cadastro`, `telefone`, `endereco`, `cidade`, `estado`, `cep`, `foto_perfil`, `created_at`) VALUES
(1, 'Caio Machado', 'adm@gmail.com', '$2y$10$43WPJTUWckJ39fMgZhUUCOCUiNoKDVpogPz5wQlVm5poNs3ffEE7G', '2025-10-24 01:18:52', '', '', '', '', '', 'uploads/perfis/perfil_1_1761567884.png', '2025-10-31 12:13:07'),
(2, 'ADM LAVELLE PERFUMES', 'admlavelle@gmail.com', '$2y$10$kImXpMcxoT3ysLvVG3Pj5O4wZPwzYrAJfbnfLjcP6D4TP87Xfom4m', '2025-10-31 11:56:05', '(12) 99733-3349', 'sesi', 'Caçapava', 'AM', '12285-010', 'uploads/perfis/perfil_2_1762528325.png', '2025-10-31 12:13:07'),
(3, 'Hythalo Santos', 'bc2@gmail.com', '$2y$10$VnwV2yJIm9ZWvWNL.7m1Ke93Uqa6iuFnuaXb1V.L3qgO4o596LZ0W', '2025-10-31 16:50:08', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-31 16:50:08'),
(4, 'CAIO FIGUEIRA MACHADO', 'caio@gmail.com', '$2y$10$xl/t9kmktv3SvLRG.rjkkONj4t2UwnCyaijM/7XEwPc1aUTFvOWta', '2025-10-31 17:01:18', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-31 17:01:18'),
(5, 'Caio', 'junnalvez.cpv@gmail.com', '$2y$10$s/CC1OOS0ibwzW5O7C.PVePms88/Ni31g866C4DdCbZFSnOS70qP6', '2025-10-31 17:24:46', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-31 17:24:46'),
(6, 'caio', 'jovem@gmail.com', '$2y$10$NtGGpP3nA4O4nNJaTOqTYe8JAGtMf0pgXeJ0mpBYL5cDJaPBqhn9u', '2025-11-10 12:31:17', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-10 12:31:17');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
