-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 02-Jul-2026 às 00:34
-- Versão do servidor: 5.7.36
-- versão do PHP: 8.1.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `gestao_financeira`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nome_usuario` varchar(100) DEFAULT NULL,
  `moeda` varchar(10) DEFAULT 'BRL',
  `notificacoes` tinyint(1) DEFAULT '1',
  `limite_gastos` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `usuario_id`, `nome_usuario`, `moeda`, `notificacoes`, `limite_gastos`) VALUES
(1, 1, NULL, 'BRL', 1, '10000.00'),
(2, 3, NULL, 'EUR', 1, '0.00'),
(3, 4, NULL, 'USD', 1, '0.00'),
(4, 6, NULL, 'USD', 1, '10.00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `gastos`
--

CREATE TABLE `gastos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `descricao` varchar(200) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_gasto` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `gastos`
--

INSERT INTO `gastos` (`id`, `usuario_id`, `descricao`, `valor`, `data_gasto`) VALUES
(11, 6, 'carne', '40.00', '2026-07-01 03:00:00'),
(12, 6, 'bilhete unico', '120.00', '2026-07-01 03:00:00'),
(13, 1, 'gás', '120.00', '2026-07-01 03:00:00'),
(14, 1, 'bilhete único', '80.00', '2026-07-01 03:00:00'),
(15, 1, 'compra do mês', '800.00', '2026-07-01 03:00:00'),
(16, 1, 'conta de água', '300.00', '2026-07-01 03:00:00'),
(17, 1, 'conta de luz', '400.00', '2026-07-01 03:00:00'),
(18, 1, 'mistura', '400.00', '2026-07-01 03:00:00'),
(19, 1, 'academia', '120.00', '2026-07-01 03:00:00'),
(20, 1, 'medicamentos', '100.00', '2026-07-01 03:00:00'),
(21, 1, 'reserva de emergência  ', '500.00', '2026-07-01 03:00:00'),
(22, 1, 'IPTU', '200.00', '2026-07-01 03:00:00'),
(23, 1, 'jantar fora', '400.00', '2026-07-01 03:00:00'),
(24, 1, 'doces', '45.00', '2026-07-01 03:00:00'),
(25, 1, 'pensão ', '700.00', '2026-07-01 03:00:00'),
(26, 1, 'gasolina', '505.00', '2026-07-01 03:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `salarios`
--

CREATE TABLE `salarios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `salarios`
--

INSERT INTO `salarios` (`id`, `usuario_id`, `valor`, `data_cadastro`) VALUES
(6, 6, '3000.00', '2026-07-01 00:59:49'),
(7, 1, '4000.00', '2026-07-01 23:18:24');

-- --------------------------------------------------------

--
-- Estrutura da tabela `saldo`
--

CREATE TABLE `saldo` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `saldo_atual` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `data_nascimento` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `cpf`, `data_nascimento`) VALUES
(1, 'Raul Pio', 'raulpio2008@gmail.com', '$2y$10$zVXy8FvSH1HvX6Eqs1QfK.hdzQ7kzb1zNJ9uQb4dkq9W238kafPEq', '', NULL),
(3, 'Viniciu jr', 'vinicius.sales@gmail.com', '$2y$10$ENpfmLrx9HeCj1PT5DPmX./cjPD8u0ZtH7Sfd8JTrjv2UG8B6e2ka', '', NULL),
(4, 'Sophia colpaert', 'sophiacolpaert@gmail.com', '$2y$10$zVXy8FvSH1HvX6Eqs1QfK.hdzQ7kzb1zNJ9uQb4dkq9W238kafPEq', '54219346830', '2000-09-23'),
(6, 'Aline', 'alinexdesigner@gmail.com', '$2y$10$nyAyWyYd9ib3V4st4giGHORvlsoHO8vDhGJSt9jXH7j8FCHKMaUlK', '89876598990', '1984-10-01');

-- --------------------------------------------------------

--
-- Estrutura da tabela `valores_extras`
--

CREATE TABLE `valores_extras` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `descricao` varchar(100) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `valores_extras`
--

INSERT INTO `valores_extras` (`id`, `usuario_id`, `descricao`, `valor`, `data_cadastro`) VALUES
(2, 6, 'uber', '200.00', '2026-07-01 01:00:09'),
(3, 1, 'uber', '400.00', '2026-07-01 23:18:35'),
(4, 1, 'produto de limpeza', '250.00', '2026-07-01 23:18:50'),
(5, 1, 'venda de roupas', '320.00', '2026-07-01 23:20:09');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `salarios`
--
ALTER TABLE `salarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `saldo`
--
ALTER TABLE `saldo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices para tabela `valores_extras`
--
ALTER TABLE `valores_extras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `salarios`
--
ALTER TABLE `salarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `saldo`
--
ALTER TABLE `saldo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `valores_extras`
--
ALTER TABLE `valores_extras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD CONSTRAINT `configuracoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `salarios`
--
ALTER TABLE `salarios`
  ADD CONSTRAINT `salarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `saldo`
--
ALTER TABLE `saldo`
  ADD CONSTRAINT `saldo_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Limitadores para a tabela `valores_extras`
--
ALTER TABLE `valores_extras`
  ADD CONSTRAINT `valores_extras_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
