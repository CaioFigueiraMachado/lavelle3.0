-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 07-Nov-2025 às 12:18
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
  `categoria` varchar(50) DEFAULT 'Compartilhável'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `descricao_breve`, `descricao_longa`, `preco`, `imagem`, `created_at`, `updated_at`, `categoria`) VALUES
(1, 'Lavelle Aureum', 'Fragrância exclusiva com notas amadeiradas', NULL, NULL, '299.90', 'lavelleaureum.jpg', '2025-10-31 12:13:07', '2025-10-31 12:13:07', 'Compartilhável'),
(2, 'Lavelle Horizon', 'Perfume oriental com toques especiados', NULL, NULL, '349.90', 'horizon.png', '2025-10-31 12:13:07', '2025-10-31 12:13:07', 'Compartilhável'),
(3, 'Lavelle Rose Sublime', 'Essência floral romântica e suave', NULL, NULL, '279.90', 'Lavelle Rose Sublime.jpg', '2025-10-31 12:13:07', '2025-10-31 12:13:07', 'Compartilhável'),
(13, 'Lavelle Aurore Florale', NULL, 'Aurore Florale, um Eau de Parfum da Lavelle, captura a essência da natureza com sua fragrância.', 'Aurore Florale da Lavelle é um convite para um despertar perfumado, uma sinfonia olfativa que celebra a delicadeza e a força da natureza. Este Eau de Parfum se revela em uma composição floral aldeídica, onde notas frescas e cintilantes se entrelaçam com a riqueza das flores, criando uma fragrância que é ao mesmo tempo vibrante e envolvente.', '299.90', 'auroreflorale2.png', '2025-10-31 16:39:58', '2025-10-31 16:40:20', 'Masculino'),
(15, 'Lavelle Étoile', NULL, 'Um perfume radiante que une a delicadeza flores ao toque envolvente da baunilha.', 'Lavelle Étoile é um convite a uma jornada olfativa sublime, onde a doçura se encontra com a sofisticação. A sua abertura revela um acorde floral sutil, uma melodia etérea de flores delicadas que dançam suavemente, preparando o palco para o coração da fragrância. Não são flores comuns, mas sim um buquê cuidadosamente selecionado para evocar a leveza e a beleza natural, um véu perfumado que acaricia a pele.', '350.00', 'Lavelle Étoile.png', '2025-11-03 14:08:49', '2025-11-03 14:08:58', 'Feminino'),
(16, 'Lavelle Essence Verte', NULL, 'Frescor verde e floral que transmite pureza e vitalidade.', 'Essence Verte se abre com uma explosão de frescor, como o orvalho da manhã em um jardim botânico recém-despertado. O \"frescor verde e floral\" não é apenas uma nota, mas uma experiência: imagine-se caminhando por um campo orvalhado, onde as folhas exalam seu aroma vital e as flores delicadas liberam seus eflúvios mais puros. Esta combinação cria uma sensação imediata de clareza, leveza e um otimismo contagiante.', '400.00', 'Lavelle Essence Verte.png', '2025-11-03 14:14:23', '2025-11-03 14:14:23', 'Compartilhável'),
(17, 'Lavelle Rouge Amour', NULL, 'Frutas vibrantes com um fundo doce e envolvente.', 'A primeira borrifada de Rouge Amour é uma explosão efervescente de frutas vibrantes. Imagine uma cesta farta de frutas vermelhas e exóticas, suculentas e cheias de vida, que despertam os sentidos com sua acidez alegre e doçura natural. Esta abertura é como o primeiro encontro, cheio de expectativa e um entusiasmo contagiante, uma promessa de momentos deliciosos que estão por vir. A combinação frutada é efervescente e luminosa, criando um rastro inicial de pura energia e alegria.', '389.90', 'Lavelle Rouge Amour.png', '2025-11-03 14:18:22', '2025-11-03 14:18:22', 'Feminino'),
(18, 'Lavelle Belle Lune', NULL, 'Mistério suave que une flores envolventes e notas cremosas.', 'A primeira impressão de Belle Lune é um abraço delicado de um mistério suave, que imediatamente transporta os sentidos para um jardim noturno, onde a luz da lua ilumina as flores. Não é um mistério pesado, mas sim etéreo e intrigante, como um segredo sussurrado ao vento. Esta abertura já indica a complexidade e a profundidade que estão por vir, prometendo uma experiência olfativa única e memorável.', '380.00', 'Lavelle Belle Lune.png', '2025-11-03 14:20:17', '2025-11-03 14:20:33', 'Feminino'),
(19, 'Lavelle Intense Noir', NULL, 'Um perfume marcante que une especiarias e um toque amadeirado.', 'Desde o primeiro spray, Intense Noir revela seu caráter marcante, com uma abertura poderosa que cativa instantaneamente. É um convite para explorar um mundo de complexidade e sofisticação. A fragrância se desdobra com uma fusão intrigante de especiarias. Imagine a vivacidade do cardamomo, o calor da pimenta preta e a profundidade da noz-moscada, criando um bouquet olfativo que é ao mesmo tempo picante, vibrante e misterioso. Estas especiarias não são apenas um toque, mas o coração pulsante do perfume, conferindo uma energia ardente e um carisma inegável.\r\nÀ medida que as especiarias se e', '400.00', 'Lavelle – Intense Noir.png', '2025-11-03 14:23:40', '2025-11-03 14:23:40', 'Masculino'),
(20, 'Lavelle L’Essence Divine', NULL, 'Elegância moderna que une flores delicadas e frescor cítrico.', 'Desde o primeiro contato, L’Essence Divine se revela com um frescor cítrico radiante, como os primeiros raios de sol da manhã que tocam a pele. Notas vivazes de bergamota, limão siciliano ou mandarina dançam efervescentes, despertando os sentidos e infundindo uma sensação imediata de clareza, energia e otimismo. Este frescor inicial não é apenas revigorante, mas serve como um convite luminoso para o coração delicado da fragrância.', '349.99', 'Lavelle L’Essence Divine.png', '2025-11-03 14:25:55', '2025-11-03 14:25:55', 'Feminino'),
(21, 'Lavelle Verde Élégant', NULL, 'Vitalidade fresca com cítricos e flores leves.', 'A jornada olfativa de Verde Élégant começa com uma dose vibrante de cítricos, como um sopro revigorante de brisa matinal. Notas efervescentes de limão verde, grapefruit suculento ou bergamota espumante dançam na abertura, infundindo uma sensação imediata de euforia e clareza. Este frescor cítrico não é apenas energizante; ele prepara o palco para a harmonia que está por vir, evocando a sensação de um campo verde banhado pelo sol.', '299.90', 'Lavelle Verde Élégant.png', '2025-11-03 14:28:25', '2025-11-03 14:28:25', 'Compartilhável'),
(22, 'Lavelle Belle Harmonie', NULL, 'Feminilidade radiante em um floral frutado sofisticado.', 'A primeira borrifada de Belle Harmonie se revela com uma abertura cintilante de notas frutadas. Pense em frutas suculentas e alegres como a pera, a lichia ou o pêssego, que oferecem uma doçura natural e um frescor convidativo. Estas frutas vibrantes despertam os sentidos com sua energia positiva, preparando o cenário para o coração floral que está por vir. É um convite para experimentar a leveza e a doçura da vida.', '229.99', 'Lavelle Belle Harmonie.png', '2025-11-03 14:32:47', '2025-11-03 14:32:47', 'Feminino'),
(23, 'Lavelle Urban Flame', NULL, 'Um perfume ousado e envolvente, que mistura calor e sofisticação em cada nota.', 'Desde o primeiro spray, Urban Flame se revela com uma audácia inegável, uma explosão de vitalidade que é ao mesmo tempo intrigante e convidativa. A fragrância é uma fusão magnética que mistura calor e sofisticação em cada nota. Pense em uma abertura que pode incluir toques picantes de pimenta preta ou cardamomo, combinados com um frescor inesperado de bergamota ou grapefruit, criando um contraste dinâmico que reflete o ritmo acelerado da cidade.', '329.99', 'Lavelle Urban Flame.png', '2025-11-03 14:35:58', '2025-11-03 14:35:58', 'Masculino'),
(24, 'Lavelle Classic Oud', NULL, 'Um perfume atemporal, que une tradição e intensidade.', 'Desde o primeiro contato, Classic Oud revela seu caráter atemporal e sua complexidade hipnotizante. A fragrância se abre com notas que preparam para a opulência que está por vir, talvez um toque de especiarias quentes como açafrão ou cominho, ou um resinoso balsâmico, que introduzem o opulento coração do perfume. É uma abertura que sugere uma história rica e uma jornada olfativa profunda.', '499.99', 'Lavelle Classic Oud.png', '2025-11-03 14:41:18', '2025-11-03 14:41:18', 'Masculino');

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
(2, 'ADM LAVELLE PERFUMES', 'admlavelle@gmail.com', '$2y$10$kImXpMcxoT3ysLvVG3Pj5O4wZPwzYrAJfbnfLjcP6D4TP87Xfom4m', '2025-10-31 11:56:05', '', '', '', '', '', 'uploads/perfis/perfil_2_1762175218.png', '2025-10-31 12:13:07'),
(3, 'Hythalo Santos', 'bc@gmail.com', '$2y$10$VnwV2yJIm9ZWvWNL.7m1Ke93Uqa6iuFnuaXb1V.L3qgO4o596LZ0W', '2025-10-31 16:50:08', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-31 16:50:08'),
(4, 'CAIO FIGUEIRA MACHADO', 'caio@gmail.com', '$2y$10$xl/t9kmktv3SvLRG.rjkkONj4t2UwnCyaijM/7XEwPc1aUTFvOWta', '2025-10-31 17:01:18', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-31 17:01:18'),
(5, 'Caio', 'junnalvez.cpv@gmail.com', '$2y$10$s/CC1OOS0ibwzW5O7C.PVePms88/Ni31g866C4DdCbZFSnOS70qP6', '2025-10-31 17:24:46', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-31 17:24:46');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
