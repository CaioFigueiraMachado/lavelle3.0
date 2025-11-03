-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 03-Nov-2025 às 12:00
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
(13, 'Lavelle Aurore Florale', NULL, 'Aurore Florale, um Eau de Parfum da Lavelle, captura a essência da natureza com sua fragrância.', 'Aurore Florale da Lavelle é um convite para um despertar perfumado, uma sinfonia olfativa que celebra a delicadeza e a força da natureza. Este Eau de Parfum se revela em uma composição floral aldeídica, onde notas frescas e cintilantes se entrelaçam com a riqueza das flores, criando uma fragrância que é ao mesmo tempo vibrante e envolvente.', '299.90', 'auroreflorale2.png', '2025-10-31 16:39:58', '2025-10-31 16:40:20', 'Masculino');

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
(2, 'ADM LAVELLE PERFUMES', 'admlavelle@gmail.com', '$2y$10$kImXpMcxoT3ysLvVG3Pj5O4wZPwzYrAJfbnfLjcP6D4TP87Xfom4m', '2025-10-31 11:56:05', '', '', '', '', '', NULL, '2025-10-31 12:13:07'),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
