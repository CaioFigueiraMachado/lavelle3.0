-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23-Nov-2025 às 19:53
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

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
  `status` varchar(50) DEFAULT NULL,
  `metodo_pagamento` varchar(50) DEFAULT NULL,
  `endereco_entrega` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `pedidos`
--

INSERT INTO `pedidos` (`id`, `usuario_id`, `data_pedido`, `total`, `status`, `metodo_pagamento`, `endereco_entrega`, `created_at`) VALUES
(11, 8, '2025-11-17 14:45:53', 2209.85, 'confirmado', 'pix', 'Rua Essio Lanfredi, 599 - Parque Residencial Maria Elmira - Caçapava/SP - CEP: 12285-010', '2025-11-17 17:45:53'),
(12, 2, '2025-11-17 16:09:19', 289.99, 'entregue', 'pix', 'Rua Essio Lanfredi, 123 - Parque Residencial Maria Elmira - Caçapava/SP - CEP: 12285-010', '2025-11-17 19:09:19'),
(13, 2, '2025-11-17 16:24:32', 399.99, 'confirmado', 'pix', 'Rua Essio Lanfredi, 1 - Parque Residencial Maria Elmira - Caçapava/SP - CEP: 12285-010', '2025-11-17 19:24:32'),
(14, 1, '2025-11-18 14:55:47', 689.98, 'cancelado', 'pix', 'Rua Essio Lanfredi, 530 - Parque Residencial Maria Elmira - Caçapava/SP - CEP: 12285-010', '2025-11-18 17:55:47'),
(16, 1, '2025-11-18 19:09:22', 289.99, 'confirmado', 'pix', 'Rua Maracanã, 12 - Laranjeiras - Betim/MG - CEP: 32676-345', '2025-11-18 22:09:22'),
(17, 2, '2025-11-19 17:19:34', 399.99, 'confirmado', 'pix', 'Rua Essio Lanfredi, 12 - Parque Residencial Maria Elmira - Caçapava/SP - CEP: 12285-010', '2025-11-19 20:19:34'),
(18, 8, '2025-11-23 00:48:48', 299.99, 'confirmado', 'pix', 'Rua Essio Lanfredi, 1 - Parque Residencial Maria Elmira - Caçapava/SP - CEP: 12285-010', '2025-11-23 03:48:48');

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedido_historico`
--

CREATE TABLE `pedido_historico` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `observacao` text DEFAULT NULL,
  `data_alteracao` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `pedido_historico`
--

INSERT INTO `pedido_historico` (`id`, `pedido_id`, `status`, `observacao`, `data_alteracao`, `usuario_id`) VALUES
(4, 11, 'confirmado', 'sduhduHUI', '2025-11-17 17:47:28', 2),
(5, 12, 'enviado', 'poio', '2025-11-17 19:19:20', 2),
(6, 12, 'entregue', 'Entregue para o vizinho.', '2025-11-17 19:20:01', 2),
(7, 14, 'entregue', 'Chegou a casa do cliente.', '2025-11-18 17:58:43', 2),
(8, 14, 'cancelado', 'cancelado pro motivos do correio.', '2025-11-18 17:58:59', 2),
(9, 16, 'confirmado', 'Cliente confirmou que chegou a sua casa.', '2025-11-18 22:09:53', 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedido_itens`
--

CREATE TABLE `pedido_itens` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `pedido_itens`
--

INSERT INTO `pedido_itens` (`id`, `pedido_id`, `produto_id`, `quantidade`, `preco_unitario`, `subtotal`) VALUES
(8, 11, 56, 1, 329.99, 329.99),
(9, 11, 50, 1, 279.90, 279.90),
(10, 11, 37, 1, 299.99, 299.99),
(11, 11, 36, 1, 199.99, 199.99),
(12, 11, 34, 1, 399.99, 399.99),
(13, 11, 31, 1, 349.99, 349.99),
(14, 11, 15, 1, 350.00, 350.00),
(15, 12, 68, 1, 289.99, 289.99),
(16, 13, 67, 1, 399.99, 399.99),
(17, 14, 68, 1, 289.99, 289.99),
(18, 14, 67, 1, 399.99, 399.99),
(20, 16, 68, 1, 289.99, 289.99),
(21, 17, 67, 1, 399.99, 399.99),
(22, 18, 69, 1, 299.99, 299.99);

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
(13, 'Lavelle Aurore Florale', NULL, 'Aurore Florale, um Eau de Parfum da Lavelle, captura a essência da natureza com sua fragrância.', 'Aurore Florale da Lavelle é um convite para um despertar perfumado, uma sinfonia olfativa que celebra a delicadeza e a força da natureza. Este Eau de Parfum se revela em uma composição floral aldeídica, onde notas frescas e cintilantes se entrelaçam com a riqueza das flores, criando uma fragrância que é ao mesmo tempo vibrante e envolvente.', 299.90, 'img/Lavelle Aurore Florale.png', '2025-10-31 16:39:58', '2025-11-10 13:05:18', 'Masculino', 'Folhas Verdes, Bergamota, Pêra', 'Lírio do Vale, Jasmim, Rosa, Acorde Floral Branco', 'Almíscar Branco, Cedro, Âmbar'),
(15, 'Lavelle Étoile', NULL, 'Um perfume radiante que une a delicadeza flores ao toque envolvente da baunilha.', 'Lavelle Étoile é um convite a uma jornada olfativa sublime, onde a doçura se encontra com a sofisticação. A sua abertura revela um acorde floral sutil, uma melodia etérea de flores delicadas que dançam suavemente, preparando o palco para o coração da fragrância. Não são flores comuns, mas sim um buquê cuidadosamente selecionado para evocar a leveza e a beleza natural, um véu perfumado que acaricia a pele.', 350.00, 'img/LavelleÉtoile.png', '2025-11-03 14:08:49', '2025-11-10 13:05:55', 'Feminino', 'Pêra, Mandarina, Flor de Laranjeira', 'Jasmim, Tuberosa, Gardênia', 'Baunilha, Âmbar, Almíscar, Sândalo'),
(16, 'Lavelle Essence Verte', NULL, 'Frescor verde e floral que transmite pureza e vitalidade.', 'Essence Verte se abre com uma explosão de frescor, como o orvalho da manhã em um jardim botânico recém-despertado. O \"frescor verde e floral\" não é apenas uma nota, mas uma experiência: imagine-se caminhando por um campo orvalhado, onde as folhas exalam seu aroma vital e as flores delicadas liberam seus eflúvios mais puros. Esta combinação cria uma sensação imediata de clareza, leveza e um otimismo contagiante.', 400.00, 'img/Lavelle EssenceVerte.png', '2025-11-03 14:14:23', '2025-11-10 13:06:40', 'Compartilhável', 'Acorde Verde, Bergamota, Limão', 'Frésia, Chá Verde, Jasmim', 'Cedro, Almíscar, Âmbar'),
(17, 'Lavelle Rouge Amour', NULL, 'Frutas vibrantes com um fundo doce e envolvente.', 'A primeira borrifada de Rouge Amour é uma explosão efervescente de frutas vibrantes. Imagine uma cesta farta de frutas vermelhas e exóticas, suculentas e cheias de vida, que despertam os sentidos com sua acidez alegre e doçura natural. Esta abertura é como o primeiro encontro, cheio de expectativa e um entusiasmo contagiante, uma promessa de momentos deliciosos que estão por vir. A combinação frutada é efervescente e luminosa, criando um rastro inicial de pura energia e alegria.', 389.90, 'img/Lavelle RougeAmour.png', '2025-11-03 14:18:22', '2025-11-10 13:07:19', 'Feminino', 'Frutas Vermelhas, Framboesa, Lichia', 'Rosa, Jasmim, Lírio', 'Baunilha, Almíscar, Sândalo'),
(18, 'Lavelle Belle Lune', NULL, 'Mistério suave que une flores envolventes e notas cremosas.', 'A primeira impressão de Belle Lune é um abraço delicado de um mistério suave, que imediatamente transporta os sentidos para um jardim noturno, onde a luz da lua ilumina as flores. Não é um mistério pesado, mas sim etéreo e intrigante, como um segredo sussurrado ao vento. Esta abertura já indica a complexidade e a profundidade que estão por vir, prometendo uma experiência olfativa única e memorável.', 380.00, 'img/Lavelle BelleLune.png', '2025-11-03 14:20:17', '2025-11-10 13:07:48', 'Feminino', 'Pêra, Bergamota, Frésia', 'Jasmim, Flor de Laranjeira, Tuberosa', 'Baunilha, Almíscar, Sândalo, Âmbar'),
(20, 'Lavelle L’Essence Divine', NULL, 'Elegância moderna que une flores delicadas e frescor cítrico.', 'Desde o primeiro contato, L’Essence Divine se revela com um frescor cítrico radiante, como os primeiros raios de sol da manhã que tocam a pele. Notas vivazes de bergamota, limão siciliano ou mandarina dançam efervescentes, despertando os sentidos e infundindo uma sensação imediata de clareza, energia e otimismo. Este frescor inicial não é apenas revigorante, mas serve como um convite luminoso para o coração delicado da fragrância.', 349.99, 'img/Lavelle L’EssenceDivine.png', '2025-11-03 14:25:55', '2025-11-10 13:08:23', 'Feminino', 'Bergamota, Laranja, Pêra', 'Jasmim, Flor de Laranjeira, Tuberosa', 'Baunilha, Sândalo, Almíscar'),
(21, 'Lavelle Verde Élégant', NULL, 'Vitalidade fresca com cítricos e flores leves.', 'A jornada olfativa de Verde Élégant começa com uma dose vibrante de cítricos, como um sopro revigorante de brisa matinal. Notas efervescentes de limão verde, grapefruit suculento ou bergamota espumante dançam na abertura, infundindo uma sensação imediata de euforia e clareza. Este frescor cítrico não é apenas energizante; ele prepara o palco para a harmonia que está por vir, evocando a sensação de um campo verde banhado pelo sol.', 299.90, 'img/Lavelle VerdeÉlégant.png', '2025-11-03 14:28:25', '2025-11-10 13:09:26', 'Compartilhável', 'Limão, Bergamota, Folhas Verdes', 'Lírio do Vale, Jasmim, Chá Verde', 'Cedro, Almíscar Branco, Âmbar'),
(22, 'Lavelle Belle Harmonie', NULL, 'Feminilidade radiante em um floral frutado sofisticado.', 'A primeira borrifada de Belle Harmonie se revela com uma abertura cintilante de notas frutadas. Pense em frutas suculentas e alegres como a pera, a lichia ou o pêssego, que oferecem uma doçura natural e um frescor convidativo. Estas frutas vibrantes despertam os sentidos com sua energia positiva, preparando o cenário para o coração floral que está por vir. É um convite para experimentar a leveza e a doçura da vida.', 229.99, 'img/Lavelle BelleHarmonie.png', '2025-11-03 14:32:47', '2025-11-10 13:10:12', 'Feminino', 'Pêssego, Lichia, Frésia', 'Rosa, Jasmim, Lírio do Vale', 'Baunilha, Sândalo, Almíscar'),
(23, 'Lavelle Urban Flame', NULL, 'Um perfume ousado e envolvente, que mistura calor e sofisticação em cada nota.', 'Desde o primeiro spray, Urban Flame se revela com uma audácia inegável, uma explosão de vitalidade que é ao mesmo tempo intrigante e convidativa. A fragrância é uma fusão magnética que mistura calor e sofisticação em cada nota. Pense em uma abertura que pode incluir toques picantes de pimenta preta ou cardamomo, combinados com um frescor inesperado de bergamota ou grapefruit, criando um contraste dinâmico que reflete o ritmo acelerado da cidade.', 329.99, 'img/Lavelle UrbanFlame.png', '2025-11-03 14:35:58', '2025-11-10 13:10:52', 'Masculino', 'Bergamota, Pimenta Preta, Cardamomo', 'Jasmim, Gerânio, Lavanda', 'Âmbar, Baunilha, Almíscar, Patchouli'),
(24, 'Lavelle Classic Oud', NULL, 'Um perfume atemporal, que une tradição e intensidade.', 'Desde o primeiro contato, Classic Oud revela seu caráter atemporal e sua complexidade hipnotizante. A fragrância se abre com notas que preparam para a opulência que está por vir, talvez um toque de especiarias quentes como açafrão ou cominho, ou um resinoso balsâmico, que introduzem o opulento coração do perfume. É uma abertura que sugere uma história rica e uma jornada olfativa profunda.', 499.99, 'img/Lavelle ClassicOud.png', '2025-11-03 14:41:18', '2025-11-10 13:11:43', 'Masculino', 'Açafrão, Framboesa, Rosa', 'Oud, Patchouli, Jasmim', 'Âmbar, Almíscar, Sândalo, Couro'),
(26, 'Lavelle Intense Noir', NULL, 'Um perfume marcante que une especiarias e um toque amadeirado.', 'Desde o primeiro spray, Intense Noir revela seu caráter marcante, com uma abertura poderosa que cativa instantaneamente. É um convite para explorar um mundo de complexidade e sofisticação. A fragrância se desdobra com uma fusão intrigante de especiarias. Imagine a vivacidade do cardamomo, o calor da pimenta preta e a profundidade da noz-moscada, criando um bouquet olfativo que é ao mesmo tempo picante, vibrante e misterioso. Estas especiarias não são apenas um toque, mas o coração pulsante do perfume, conferindo uma energia ardente e um carisma inegável. À medida que as especiarias se e', 399.90, 'img/Lavelle Intense Noir.png', '2025-11-07 12:37:09', '2025-11-10 13:14:34', 'Masculino', 'Pimenta Preta, Bergamota, Noz-moscada', 'Patchouli, Cedro, Âmbar', 'Couro, Fava Tonka, Almíscar'),
(29, 'Lavelle Gold', NULL, 'Um perfume compartilhável de elegância dourada e luxo radiante.', 'Lavelle Gold Eau de Parfum é uma fragrância compartilhável que personifica a elegância atemporal e o luxo discreto. Apresentado em um frasco de vidro translúcido com linhas clássicas, o design minimalista é coroado por uma tampa dourada robusta e sofisticada, refletindo a essência preciosa que ele contém', 249.99, 'img/Lavelle Gold.png', '2025-11-07 13:52:50', '2025-11-10 13:17:12', 'Compartilhável', 'Bergamota, Pêssego, Mandarina', 'Jasmim, Flor de Laranjeira, Gardênia', 'Baunilha, Sândalo, Âmbar, Almíscar'),
(30, 'Lavelle Noir Intense', NULL, 'Um perfume marcante, profundo e sofisticado, feito para noites que pedem presença.', 'Lavelle Noir Intense é a assinatura do homem que não precisa falar alto para ser notado. A fragrância se abre com notas de pimenta preta e bergamota , um impacto intrigante e elegante. No coração, vetiver e cedro trazem um lado amadeirado de força e refinamento, enquanto o patchouli e o âmbar finalizam com calor e sensualidade duradoura. Ideal para noites especiais, encontros e situações onde confiança é o único acessório necessário.', 399.90, 'img/Lavelle Noir.png', '2025-11-07 14:19:55', '2025-11-10 13:17:56', 'Masculino', 'Bergamota, Pimenta Preta, Cardamomo', 'Gerânio, Lavanda, Patchouli', 'Cedro, Âmbar, Fava Tonka, Almíscar'),
(31, 'Lavelle Fleur de Lune', NULL, 'Flores brancas envoltas em um toque adocicado e sensual, inspirado no brilho da lua.', 'Lavelle Fleur de Lune é uma celebração do feminino delicado e, ao mesmo tempo, poderoso. Notas de saída de pêra e tangerina despertam leveza e frescor. No coração, jasmim e flor de laranjeira criam um buquê envolvente, quase hipnotizante. A base traz baunilha e sândalo, deixando um rastro cremoso e viciante. É o perfume de mulheres que transformam qualquer lugar que passam em poesia.', 349.99, 'img/Lavelle Fleur de Lune.png', '2025-11-07 14:23:19', '2025-11-10 14:14:48', 'Feminino', '', '', ''),
(32, 'Lavelle Rouge Essence', NULL, 'Doce, sedutor e envolvent, feito para quem gosta de perfume que marca.', 'Lavelle Rouge Essence é ousadia embalada em vermelho. Se abre com framboesa e mandarina, deliciosamente vibrantes. O coração traz rosa e praline, equilibrando doçura e elegância. Na base, baunilha, fava tonka e âmbar criam uma aura quente, doce e irresistível. Ideal para quem ama deixar um rastro inesquecível e não tem medo de ser o centro das atenções.', 199.99, 'img/Lavelle Rouge Essence.png', '2025-11-07 14:26:27', '2025-11-10 13:18:46', 'Feminino', 'Frutas Vermelhas, Pêra, Pimenta Rosa', 'Rosa, Jasmim, Lírio', 'Baunilha, Sândalo, Almíscar'),
(33, 'Lavelle Platinum', NULL, 'Elegância moderna. Um perfume limpo, caro e minimalista.', 'Lavelle Platinum é inspirado no universo do luxo discreto. A abertura traz bergamota e alecrim, criando um frescor refinado. No coração, notas de chá preto e sálvia adicionam sofisticação. O fundo de musk, iso e sândalo garante fixação prolongada com uma assinatura limpa e elegante. É o perfume para quem prefere qualidade a exagero, minimalista, porém inesquecível.', 299.90, 'img/Lavelle Platinum.png', '2025-11-07 14:28:15', '2025-11-10 13:19:26', 'Masculino', 'Bergamota, Limão, Gengibre', 'Lavanda, Alecrim, Gerânio', 'Cedro, Vetiver, Almíscar, Âmbar'),
(34, 'Lavelle Cloud', NULL, 'Frescor doce de maçã verde com jujuba.', 'Inspirado na sensação de flutuar. Maçã verde e pera gelada trazem frescor imediato, enquanto jujuba e flor de algodão criam um fundo adocicado e leve. Almíscar branco garante conforto e fixação suave.', 399.99, 'img/Lavelle Cloud.png', '2025-11-07 14:31:58', '2025-11-10 13:20:37', 'Compartilhável', 'Bergamota, Pêra, Algodão Doce', 'Coco, Chantilly, Jasmim', 'Baunilha, Almíscar, Sândalo'),
(35, 'Lavelle Pure Blossom', NULL, 'Floral suave e limpo, cheiro de banho caro.', 'Mandarina e chá branco abrem o perfume com pureza. Flor de laranjeira e magnólia trazem feminilidade discreta. Musk e sândalo deixam o rastro leve e elegante.', 249.99, 'img/Lavelle Pure Blossom.png', '2025-11-07 14:35:12', '2025-11-10 13:21:15', 'Compartilhável', 'Bergamota, Pêra, Notas Verdes', 'Flor de Laranjeira, Jasmim, Lírio do Vale', 'Almíscar Branco, Cedro, Âmbar'),
(36, 'Lavelle Citrus Wave', NULL, 'Explosão de refrescância com limão e notas marinhas.', 'Inspirado em ondas e energia. Limão, bergamota e acorde oceânico se misturam com ervas aromáticas. Base de cedro e musk para fixação limpa.', 199.99, 'img/Lavelle Citrus Wave.png', '2025-11-07 14:38:44', '2025-11-10 13:22:04', 'Compartilhável', 'Limão Siciliano, Bergamota, Notas Marinhas', 'Jasmim Aquático, Alecrim, Chá Verde', 'Cedro, Almíscar Branco, Âmbar'),
(37, 'Lavelle Velvet Sky', NULL, 'Doce leve com toque de lavanda e vanilla musk.', 'Lavanda cremosa com baunilha leve cria um perfume aconchegante e viciante. A sensação é de abraço, de calmaria, de paz.', 299.99, 'img/Lavelle Velvet Sky.png', '2025-11-07 14:40:36', '2025-11-10 13:22:46', 'Compartilhável', 'Bergamota, Lavanda, Pêra', 'Íris, Jasmim, Flor de Algodão', 'Baunilha, Almíscar, Sândalo'),
(38, 'Lavelle Forest Mist', NULL, 'Frescor de floresta, leve e herbal.', 'Notas de eucalipto, alecrim e folhas verdes criam sensação de ar puro. Base amadeirada com cedro e vetiver.', 199.99, 'img/Lavelle Forest Mist.png', '2025-11-07 14:43:07', '2025-11-10 13:23:25', 'Compartilhável', 'Folhas Verdes, Bergamota, Alecrim', 'Eucalipto, Menta, Lavanda', 'Cedro, Vetiver, Musgo de Carvalho'),
(39, 'Lavelle Blue Spirit', NULL, 'Aquático fresco e elegante.', 'Bergamota e hortelã criam frescor, enquanto notas aquáticas dão modernidade. Perfeito para calor ou academia.', 199.99, 'img/Lavelle Blue Spirit.png', '2025-11-07 14:44:41', '2025-11-10 13:24:11', 'Masculino', 'Limão, Menta, Acorde Aquático', 'Gerânio, Lavanda, Pimenta Preta', 'Cedro, Âmbar, Almíscar'),
(40, 'Lavelle Eclipse Leather', NULL, 'Couro e café, intenso e marcante.', 'Grapefruit e pimenta criam impacto, couro com café traz presença e sedução.', 229.99, 'img/Lavelle Eclipse Leather.png', '2025-11-07 14:49:35', '2025-11-10 13:56:40', 'Masculino', 'Toranja, Pimenta Rosa, Cardamomo', 'Café, Couro, Jasmim', 'Baunilha, Cedro, Patchouli, Fava Tonka'),
(41, 'Lavelle Urban Steel', NULL, 'Perfume de status. Frio, amadeirado, urbano.', 'Notas de metal + cedro + gengibre. Cheiro de homem moderno, executivo e minimalista.', 159.99, 'img/Lavelle Urban Steel.png', '2025-11-07 14:51:18', '2025-11-10 14:08:02', 'Masculino', 'Bergamota, Gengibre, Maçã', 'Gerânio, Lavanda, Sálvia', 'Cedro, Âmbar, Almíscar, Fava Tonka'),
(42, 'Lavelle Savage Night', NULL, 'Selvagem, atrativo, explosivo.', 'Pimenta, ambroxan e bergamota formam um perfume hipnótico para a noite.', 199.99, 'img/Lavelle Savage Night.png', '2025-11-07 14:52:44', '2025-11-10 14:10:13', 'Masculino', 'Bergamota, Pimenta Preta, Elemi', 'Gerânio, Lavanda, Vetiver', 'Cedro, Ambroxan, Fava Tonka'),
(43, 'Lavelle Royal Wood', NULL, 'Madeira pura, quente e imponente.', 'Sândalo, cedro e âmbar. A sensação de elegância clássica.', 199.99, 'img/Lavelle Royal Wood.png', '2025-11-07 14:54:26', '2025-11-10 14:11:02', 'Masculino', 'Bergamota, Pimenta Rosa, Cardamomo', 'Cedro, Patchouli, Sândalo', 'Âmbar, Almíscar, Fava Tonka'),
(44, 'Lavelle Thunder', NULL, 'Energia, impacto e adrenalina.', 'Gengibre, limão e vetiver. Aroma que transmite força e atitude.', 159.99, 'img/Lavelle Thunder.png', '2025-11-07 15:01:17', '2025-11-10 14:14:14', 'Masculino', 'Limão, Gengibre, Pimenta Preta', 'Lavanda, Sálvia, Gerânio', 'Cedro, Âmbar, Almíscar'),
(49, 'Lavelle Horizon', NULL, 'A essência da liberdade e do frescor. Uma fragrância masculina que evoca a imensidão.', 'Lavelle Horizon encapsula a sensação revigorante de um novo começo, a vastidão do oceano e a brisa leve. O frasco, com seu design clássico e robusto, abriga um líquido de tonalidade azul-claro cristalina, que remete à pureza da água e ao céu aberto. Detalhes em dourado na tampa contrastam elegantemente com o azul, adicionando um toque de sofisticação. A frase \'Liberdade e Frescor\' no rótulo reforça a proposta de uma fragrância que inspira aventura, expansão e bem-estar.', 349.99, 'img/Lavelle Horizon.png', '2025-11-10 13:01:49', '2025-11-10 13:01:49', 'Masculino', 'Limão Siciliano, Menta, Acorde Aquático', 'Lavanda, Gerânio, Acorde Verde', 'Vetiver, Cedro, Almíscar Branco'),
(50, 'Lavelle Rose Sublime', NULL, 'A delicadeza e a paixão da rosa em uma fragrância feminina e envolvente, com um toque de sofisticação', 'Lavelle Rose Sublime é a celebração da feminilidade e da beleza atemporal da rosa. O frasco, com seu design elegante e clássico, revela um líquido de tonalidade rosada suave, complementado por detalhes em ouro rosé, transmitindo uma sensação de delicadeza e requinte. A menção \'Coleção Feminina Lavelle\' reforça seu posicionamento para a mulher que aprecia a graciosidade. A iluminação suave e o fundo em tons pastéis e roseados, juntamente com a superfície texturizada que lembra um tecido delicado, evocam uma atmosfera de romance, suavidade e um luxo acessível.', 279.90, 'img/Lavelle Rose Sublime.jpg', '2025-11-10 13:03:27', '2025-11-10 13:03:27', 'Feminino', 'Pêra, Lichia, Bergamota', 'Rosa Damascena, Peônia, Jasmim', 'Baunilha, Almíscar Branco, Cedro'),
(51, 'Lavelle Aureum', NULL, 'A essência dourada da conquista. Um perfume sofisticado que irradia luxo e confiança.', 'Lavelle Aureum é mais do que um perfume, é uma declaração. Envolto em um frasco de design clássico e elegante, sua tonalidade dourada promete uma fragrância rica e envolvente. Com a inscrição \'Intensidade que Conquista\', ele é feito para quem busca deixar uma marca inesquecível. As notas sutis de flores brancas ao fundo, juntamente com a apresentação em um pedestal texturizado e o drapeado suave, sugerem uma experiência olfativa premium, ideal para momentos que exigem distinção e poder.', 299.90, 'img/Lavelle Aureum.png', '2025-11-10 13:15:58', '2025-11-10 13:15:58', 'Compartilhável', 'Bergamota, Pêssego, Pimenta Rosa', 'Jasmim, Flor de Laranjeira, Caramelo', 'Baunilha, Sândalo, Âmbar, Almíscar'),
(53, 'Lavelle Iron Pulse', NULL, 'Forte, metálico, moderno — cheiro de força urbana.', 'Lavelle Iron Pulse abre com notas vibrantes de gengibre e bergamota, criando energia instantânea. No coração, um acorde metálico moderno se mistura ao cardamomo, trazendo sofisticação. A base de cedro e vetiver dá identidade poderosa e masculina. Perfume marcante, ideal para quem vive intensidade.', 289.99, 'img/Lavelle Iron Pulse.png', '2025-11-14 13:08:16', '2025-11-14 13:08:16', 'Masculino', 'Gengibre, Bergamota', 'Acorde Metálico, Cardamomo', 'Cedro, Vetiver'),
(54, 'Lavelle Black Ember', NULL, 'Quente, amadeirado e extremamente sedutor.', 'Uma combinação profunda de canela, pimenta preta e âmbar queimado. Toques de couro e fava tonka criam uma aura intensa e envolvente. Fragrância de presença e sedução, feita para noites especiais.', 319.99, 'img/Lavelle Black Ember.png', '2025-11-14 13:14:18', '2025-11-14 13:14:18', 'Masculino', 'Canela, Pimenta Preta', 'Âmbar Queimado, Couro', 'Fava Tonka'),
(55, 'Lavelle Steel Storm', NULL, 'Fresco e eletrizante, com energia de tempestade.', 'Lavelle Steel Storm traz um frescor cortante de limão e pimenta rosa, seguido por notas marinhas. Madeira de guaiaco e musk estruturam o fundo, criando um perfume firme, elegante e moderno.', 299.90, 'img/Lavelle Steel Storm.png', '2025-11-14 13:18:01', '2025-11-14 13:18:01', 'Masculino', 'Limão, Pimenta Rosa', 'Notas Marinhas', 'Madeira de Guaiaco, Musk'),
(56, 'Lavelle Midnight Prime', NULL, 'Elegante, misterioso e irresistível.', 'A abertura com grapefruit e noz-moscada cria um impacto sofisticado. O coração é dominado por lavanda escura e sálvia, criando atmosfera noturna. Finaliza com patchouli e amberwood, conferindo profundidade e charme.', 329.99, 'img/Lavelle Midnight Prime.png', '2025-11-14 13:21:15', '2025-11-14 13:21:15', 'Masculino', 'Grapefruit, Noz-moscada', 'Lavanda Escura, Sálvia', 'Patchouli, Amberwood'),
(57, 'Lavelle Woodline King', NULL, 'Dominação amadeirada. Cheiro de liderança.', 'Sândalo, cedro, tabaco suave e âmbar formam um acorde robusto e luxuoso. A intensidade é equilibrada com notas de limão siciliano e pimenta branca. Perfume de personalidade forte.', 339.99, 'img/Lavelle Woodline King.png', '2025-11-14 13:23:20', '2025-11-14 13:23:20', 'Masculino', 'Limão Siciliano, Pimenta Branca', 'Tabaco Suave', 'Sândalo, Cedro, Âmbar'),
(59, 'Lavelle Sweet  Harmony', NULL, 'Doce suave, equilibrado e encantador.', 'Framboesa, algodão doce e baunilha se unem a flores brancas delicadas. Criado para quem gosta de perfumes açucarados e elegantes, sem exagero.', 279.00, 'img/Lavelle Sweet Harmony.png', '2025-11-14 13:52:58', '2025-11-14 13:53:13', 'Feminino', 'Framboesa', 'Algodão Doce, Flores Brancas', 'Baunilha'),
(61, 'Lavelle Éclat Floral', NULL, 'A luminosidade e a delicadeza das flores em uma essência feminina e radiante. Celebre sua beleza natural.', 'Lavelle Éclat Floral é um convite para desabrochar. O frasco clássico e elegante abriga uma fragrância de tom rosa-pêssego suave, evocando a frescura e a beleza das manhãs de primavera. Detalhes em ouro rosé e a menção \'Luz e Delicadeza\' no rótulo, junto com \'Coleção Feminina Lavelle\', reforçam sua natureza grácil. A apresentação em um pedestal de cristal, rodeado por rosas e jasmins em flor, pétalas espalhadas e brilhos etéreos, cria uma atmosfera de pureza, romance e sofisticação. É ideal para a mulher que irradia alegria, charme e uma feminilidade inesquecível, perfeita para uso diário e momentos especiais.', 329.99, 'img/Lavelle Éclat Floral.png', '2025-11-14 15:27:35', '2025-11-14 15:27:35', 'Feminino', 'Pêra, Mandarina, Flor de Laranjeira', 'Jasmim, Tuberosa, Gardênia', 'Baunilha, Âmbar, Almíscar, Sândalo'),
(62, 'Lavelle Enigma Sedutor', NULL, 'O mistério e a conquista em um aroma feminino e envolvente. Desperte sua aura magnética.', 'Lavelle Enigma Sedutor é a fragrância para a mulher que abraça seu poder de atração. Seu frasco, com o design icônico da Lavelle, ostenta um líquido de tom púrpura intenso, refletindo a profundidade e a paixão. A tampa dourada facetada e o rótulo com a inscrição \'Mistério e Conquista\' acentuam sua personalidade ousada. A ambientação escura e esfumaçada, com toques de brilho dourado e um reflexo espelhado, cria uma aura de intriga e sensualidade noturna. Este perfume é a escolha para a mulher confiante, que gosta de deixar um rastro memorável e enigmático, ideal para noites inesquecíveis e momentos de grande impacto.', 389.99, 'img/Lavelle Enigma Sedutor.png', '2025-11-14 15:28:38', '2025-11-14 15:28:38', 'Feminino', 'Bergamota, Pimenta Rosa, Acordes Licorosos', 'Rosa Negra, Jasmim Sambac, Íris', 'Patchouli, Baunilha Negra, Âmbar, Oud'),
(63, 'Lavelle Brisa Serena', NULL, 'A leveza e a calma de um dia perfeito. Uma fragrância feminina que refresca a alma e tranquiliza o espírito.', 'Lavelle Brisa Serena é um abraço de paz e bem-estar. O frasco tradicional, com seu líquido azul-esverdeado claro, remete à tranquilidade das águas cristalinas e à serenidade de um céu limpo. Detalhes prateados na tampa e a frase \'Leveza e Calma\' no rótulo, junto com \'Coleção Feminina Lavelle\', sugerem uma essência refrescante e reconfortante. A ambientação com tecido esvoaçante em tons pastéis, gotas de água simulando uma garoa suave e uma superfície de mármore branco, evoca a pureza e a frescura de um ambiente natural e relaxante. Este perfume é ideal para a mulher que busca equilíbrio, frescor e uma fragrância que a acompanhe em seu dia a dia com delicadeza e elegância natural.', 299.99, 'img/Lavelle Brisa Serena.png', '2025-11-14 15:29:54', '2025-11-14 15:29:54', 'Feminino', 'Bergamota, Limão Siciliano, Notas Marinhas', 'Flor de Lótus, Lírio-do-Vale, Chá Verde', 'Almíscar Branco, Cedro, Âmbar Suave'),
(64, 'Lavelle Aurora Radiante', NULL, 'A luminosidade do amanhecer em uma fragrância feminina que irradia luz e positividade. Desperte seu brilho interior.', 'Lavelle Aurora Radiante captura a beleza serena e o otimismo do nascer do sol. O frasco, com seu design clássico e elegante, apresenta um líquido em tons de âmbar claro e dourado, que evoca os primeiros raios solares. A tampa facetada e os detalhes em ouro dourado brilhante reforçam a sensação de luxo e calor. O rótulo, com a inscrição \'Luz e Positividade\', transmite a essência de uma fragrância alegre e envolvente. A ambientação com uma névoa suave e dourada, brilhos etéreos e um reflexo espelhado, cria uma aura de encantamento e renovação. Este perfume é ideal para a mulher que busca inspiração, que ilumina por onde passa e que deseja uma fragrância que celebre a sua natureza radiante e otimista, perfeita para o dia a dia e momentos de celebração.', 369.99, 'img/Lavelle Aurora Radiante.png', '2025-11-14 15:32:36', '2025-11-14 15:32:36', 'Feminino', 'Bergamota, Damasco, Flor de Laranjeira', 'Rosa Dourada, Ylang-Ylang, Peônia', 'Baunilha Solar, Âmbar Dourado, Sândalo, Almíscar Suave'),
(65, 'Lavelle Noite Estrelada', NULL, 'A magia do firmamento em uma fragrância feminina que inspira sonhos e mistério. Deixe-se envolver pela sua aura.', 'Lavelle Noite Estrelada é um mergulho no infinito do universo. O frasco elegante abriga um líquido azul-marinho profundo, salpicado de brilhos dourados, remetendo a uma galáxia distante e cintilante. A tampa facetada e os detalhes em ouro dourado vibrante conferem um toque celestial e luxuoso. O rótulo, com a inscrição \'Sonhos & Mistério\', transmite a essência de uma fragrância intrigante e inspiradora. A ambientação com um céu estrelado em tons de azul e roxo, brilhos que simulam poeira cósmica e um reflexo espelhado, cria uma atmosfera de encantamento e profundidade. Este perfume é ideal para a mulher sonhadora, que admira a imensidão e que busca uma fragrância que a transporte para um universo de possibilidades, perfeita para noites especiais e momentos de introspecção.', 390.99, 'img/Lavelle Noite Estrelada.png', '2025-11-14 15:33:37', '2025-11-14 15:33:37', 'Feminino', 'Bergamota, Lavanda, Pimenta Rosa', 'Incenso, Violeta, Cedro', 'Vetiver, Patchouli, Âmbar Negro, Fava Tonka'),
(66, 'Lavelle Jardins Secretos', NULL, 'A essência da natureza intocada em uma fragrância feminina que desvenda encantos ocultos. Explore sua magia interior.', 'Lavelle Jardins Secretos transporta você para um oásis de tranquilidade e mistério. O frasco elegante abriga um líquido verde esmeralda vibrante, que remete à exuberância de folhagens e flores escondidas. A tampa facetada em ouro rosé e os detalhes delicados no rótulo, com a inscrição \'Descoberta & Encanto\', evocam a beleza e a serenidade de um jardim secreto. A ambientação com raios de sol filtrando-se entre as árvores, vegetação densa, flores silvestres e um pedestal de pedra rústica, cria uma atmosfera de conto de fadas e naturalidade. Este perfume é ideal para a mulher que se conecta com a natureza, que busca a paz e a inspiração nos detalhes, e que deseja uma fragrância que a envolva com frescor, delicadeza e um toque de magia. Perfeito para o dia a dia e para momentos de reflexão.', 349.99, 'img/Lavelle Jardins Secretos.png', '2025-11-14 16:48:39', '2025-11-14 16:48:39', 'Feminino', 'Seiva Verde, Bergamota Italiana, Folhas de Hera', 'Lírio-do-Vale (Muguet), Peônia, Orquídea Esmeralda', 'Musgo de Carvalho, Patchouli, Madeira de Sândalo, Âmbar'),
(67, 'Lavelle Sedução Dourada', NULL, 'O brilho da conquista em uma fragrância feminina que exala luxo e poder. Deixe sua marca irresistível.', 'Lavelle Sedução Dourada é a essência do glamour e da autoconfiança. O frasco sofisticado abriga um líquido âmbar intenso, que irradia o calor e o esplendor do ouro. A tampa facetada em ouro rosé e os detalhes ricos no rótulo, com a inscrição \'Luxo & Conquista\', evocam a opulência e a sofisticação. A ambientação com cortinas teatrais em veludo vermelho, um fundo de lantejoulas douradas cintilantes, uma névoa suave e um pedestal dourado, cria uma atmosfera de palco e grandiosidade. Este perfume é ideal para a mulher que domina a cena, que se sente poderosa e que busca uma fragrância que a acompanhe em seus momentos de destaque, perfeita para eventos de gala, celebrações e para deixar uma impressão inesquecível.', 399.99, 'img/Lavelle Sedução Dourada.png', '2025-11-14 16:50:53', '2025-11-14 16:50:53', 'Feminino', 'Pimenta Rosa, Cardamomo, Açafrão', 'Madeira de Oud, Patchouli Exótico, Incenso', 'Baunilha Negra, Couro Aveludado, Âmbar Intenso, Sândalo Cremoso'),
(68, 'Lavelle Doce Encanto', NULL, 'A magia da infância e a alegria em uma fragrância feminina gourmand. Um toque de fantasia e doçura para seu dia.', 'Lavelle Doce Encanto é uma viagem nostálgica a um mundo de alegria e fantasia. O frasco elegante abriga um líquido rosa claro e delicado, que remete aos doces e à leveza da infância. A tampa facetada em ouro rosé e os detalhes delicados no rótulo, com a inscrição \'Alegria & Fantasia\', evocam a doçura e a inocência. A ambientação com um cenário de algodão-doce, pirulitos, marshmallows e confeitos coloridos, tudo sob uma chuva de brilhos e luz suave, cria uma atmosfera lúdica e irresistível. Este perfume é ideal para a mulher que abraça sua jovialidade, que adora um toque de diversão e que busca uma fragrância que a envolva em um aroma delicioso e reconfortante, perfeita para o dia a dia e para momentos de pura felicidade.', 289.99, 'img/Lavelle Doce Encanto.png', '2025-11-14 16:52:55', '2025-11-14 16:52:55', 'Feminino', 'Licor de Ameixa, Gengibre, Pimenta Preta', 'Absoluto de Jasmim, Rosa Turca, Flor de Laranjeira', 'Baunilha Bourbon, Fava Tonka, Madeira de Cashmere, Almíscar Negro'),
(69, 'Lavelle Espírito Livre', NULL, 'A essência da liberdade e do ar puro. Lavelle Espírito Livre é uma fragrância aquática, fresca e limpa.', 'Lavelle Espírito Livre é um convite para respirar fundo no topo do mundo. Esta fragrância é uma ode à liberdade sem limites e à serenidade da natureza. Começa com a explosão revigorante da Bergamota Gelada, misturada a um inebriante Acorde de Névoa do Mar que limpa a mente.', 299.99, 'img/Lavelle Espírito Livre.png', '2025-11-14 16:55:32', '2025-11-14 16:55:32', 'Feminino', 'Acorde de Névoa do Mar, Bergamota Gelada, Folhas de Violeta', 'Flor de Lótus, Ozônio, Chá Branco', 'Madeira Flutuante, Almíscar Branco, Ambroxan');

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
(1, 'Caio Machado', 'adm@gmail.com', '$2y$10$43WPJTUWckJ39fMgZhUUCOCUiNoKDVpogPz5wQlVm5poNs3ffEE7G', '2025-10-24 01:18:52', '', 'Rua Maracanã, 12 - Laranjeiras', 'Betim', 'MG', '32676-345', 'uploads/perfis/perfil_1_1761567884.png', '2025-10-31 12:13:07'),
(2, 'ADM LAVELLE PERFUMES', 'admlavelle@gmail.com', '$2y$10$kImXpMcxoT3ysLvVG3Pj5O4wZPwzYrAJfbnfLjcP6D4TP87Xfom4m', '2025-10-31 11:56:05', '(12) 99733-3349', 'Rua Essio Lanfredi, 12 - Parque Residencial Maria Elmira', 'Caçapava', 'SP', '12285-010', 'uploads/perfis/perfil_2_1763503925.png', '2025-10-31 12:13:07'),
(4, 'CAIO FIGUEIRA MACHADO', 'caio@gmail.com', '$2y$10$xl/t9kmktv3SvLRG.rjkkONj4t2UwnCyaijM/7XEwPc1aUTFvOWta', '2025-10-31 17:01:18', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-31 17:01:18'),
(5, 'Caio', 'junnalvez.cpv@gmail.com', '$2y$10$s/CC1OOS0ibwzW5O7C.PVePms88/Ni31g866C4DdCbZFSnOS70qP6', '2025-10-31 17:24:46', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-31 17:24:46'),
(6, 'caio', 'jovem@gmail.com', '$2y$10$NtGGpP3nA4O4nNJaTOqTYe8JAGtMf0pgXeJ0mpBYL5cDJaPBqhn9u', '2025-11-10 12:31:17', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-10 12:31:17'),
(8, 'Sophia', 'sophia@gmail.com', '$2y$10$dkhYnLBD/DyZhsm5v4yvyOcm0B1kbX/gzanbfrf7CTNXSEeVFnJIa', '2025-11-17 14:56:33', '', 'Rua Essio Lanfredi, 1 - Parque Residencial Maria Elmira', 'Caçapava', 'SP', '12285-010', 'uploads/perfis/perfil_8_1763869688.png', '2025-11-17 14:56:33'),
(9, 'Bianca', 'bianca@ESTRANHA.com', '$2y$10$uuGcFWTlk6arVi9BqUzOjeonWfkbV0gWXUyus8EnkLbbSF3HyTDaa', '2025-11-17 17:49:09', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-17 17:49:09'),
(10, 'Ana', 'anabanana@regia.com', '$2y$10$9RPK1cRVygdEF1b9G..mMe0nQQCguPHfIs3cNm479Cz298UWDYCla', '2025-11-17 17:49:36', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-17 17:49:36');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pedido_usuario` (`usuario_id`),
  ADD KEY `idx_pedido_status` (`status`),
  ADD KEY `idx_pedido_data` (`data_pedido`);

--
-- Índices para tabela `pedido_historico`
--
ALTER TABLE `pedido_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`);

--
-- Índices para tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `produto_id` (`produto_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `pedido_historico`
--
ALTER TABLE `pedido_historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `pedido_historico`
--
ALTER TABLE `pedido_historico`
  ADD CONSTRAINT `pedido_historico_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `pedido_itens`
--
ALTER TABLE `pedido_itens`
  ADD CONSTRAINT `pedido_itens_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`),
  ADD CONSTRAINT `pedido_itens_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
