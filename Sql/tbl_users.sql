-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Creato il: Gen 03, 2020 alle 16:11
-- Versione del server: 5.6.45-log
-- Versione PHP: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ixellevu_test_fabri`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `users` (
  `user_id` int(15) NOT NULL,
  `firstname` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `lastname` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birthday` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `registration_date` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_visit` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `fb_user_id` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `banned` int(1) NOT NULL DEFAULT '0',
  `level` smallint(2) NOT NULL DEFAULT '0',
  `reset_password_code` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `autologin_hash` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `autologin_expire` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_password` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_secret_question` smallint(1) NOT NULL,
  `new_secret` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `modify_security` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `new_password_code` longtext COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(15) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
