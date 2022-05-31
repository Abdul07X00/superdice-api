-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 31, 2022 at 09:16 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 7.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `superdice_v1`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_boards`
--

CREATE TABLE `tbl_boards` (
  `id` int(11) NOT NULL,
  `drawn` varchar(100) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_board_bets`
--

CREATE TABLE `tbl_board_bets` (
  `id` int(11) NOT NULL,
  `wallet_address` text NOT NULL,
  `side` int(11) NOT NULL,
  `board_id` int(11) NOT NULL,
  `network` varchar(100) NOT NULL,
  `currency` varchar(100) NOT NULL,
  `amount` decimal(10,7) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_transactions`
--

CREATE TABLE `tbl_transactions` (
  `id` int(11) NOT NULL,
  `wallet_address` text NOT NULL,
  `txn_token` text NOT NULL,
  `method` enum('deposit','withdraw','earned','lost') NOT NULL DEFAULT 'deposit',
  `board_id` int(11) NOT NULL,
  `side` int(11) NOT NULL,
  `network` varchar(100) NOT NULL,
  `currency` varchar(100) NOT NULL,
  `last_amount` decimal(10,7) NOT NULL DEFAULT 0.0000000,
  `new_amount` decimal(10,7) NOT NULL DEFAULT 0.0000000,
  `status` tinyint(4) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_wallets`
--

CREATE TABLE `tbl_wallets` (
  `id` int(11) NOT NULL,
  `network` varchar(100) NOT NULL,
  `currency` varchar(100) NOT NULL,
  `option_values` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_wallets`
--

INSERT INTO `tbl_wallets` (`id`, `network`, `currency`, `option_values`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ETHEREUM', 'ETH', '[\"0.01\",\"0.05\",\"0.1\",\"0.5\",\"1\",\"2\",\"3\",\"5\",\"10\",\"25\",\"50\",\"100\"]', 1, '2022-05-30 06:17:33', '2022-05-30 10:15:22'),
(2, 'ETHEREUM', 'USDC', '[\"1\",\"5\",\"10\",\"25\",\"50\",\"100\",\"250\",\"500\",\"1000\",\"2500\",\"5000\",\"10000\"]', 1, '2022-05-30 06:19:42', '2022-05-30 10:15:22'),
(3, 'ETHEREUM', 'USDT', '[\"1\",\"5\",\"10\",\"25\",\"50\",\"100\",\"250\",\"500\",\"1000\",\"2500\",\"5000\",\"10000\"]', 1, '2022-05-30 06:19:49', '2022-05-30 10:15:22'),
(4, 'BINANCE', 'BNB', '[\"0.01\",\"0.05\",\"0.1\",\"0.5\",\"1\",\"2\",\"3\",\"5\",\"10\",\"25\",\"50\",\"100\"]', 1, '2022-05-30 06:20:55', '2022-05-30 10:15:22'),
(5, 'BINANCE', 'USDC', '[\"1\",\"5\",\"10\",\"25\",\"50\",\"100\",\"250\",\"500\",\"1000\",\"2500\",\"5000\",\"10000\"]', 1, '2022-05-30 06:19:42', '2022-05-30 10:15:22'),
(6, 'BINANCE', 'USDT', '[\"1\",\"5\",\"10\",\"25\",\"50\",\"100\",\"250\",\"500\",\"1000\",\"2500\",\"5000\",\"10000\"]', 1, '2022-05-30 06:19:49', '2022-05-30 10:15:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_boards`
--
ALTER TABLE `tbl_boards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_board_bets`
--
ALTER TABLE `tbl_board_bets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_transactions`
--
ALTER TABLE `tbl_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_wallets`
--
ALTER TABLE `tbl_wallets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_boards`
--
ALTER TABLE `tbl_boards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_board_bets`
--
ALTER TABLE `tbl_board_bets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_transactions`
--
ALTER TABLE `tbl_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_wallets`
--
ALTER TABLE `tbl_wallets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
