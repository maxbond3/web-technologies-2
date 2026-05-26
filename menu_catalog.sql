-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.4:3306
-- Время создания: Май 26 2026 г., 07:43
-- Версия сервера: 8.4.8
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `menu_catalog`
--

CREATE DATABASE IF NOT EXISTS menu_catalog; 
USE menu_catalog;

-- --------------------------------------------------------

--
-- Структура таблицы `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int NOT NULL,
  `parent_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT '#',
  `sort_order` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `menu_items`
--

INSERT INTO `menu_items` (`id`, `parent_id`, `title`, `url`, `sort_order`, `created_at`) VALUES
(1, NULL, 'Каталог товаров', '#', 1, '2026-05-26 02:41:24'),
(5, 1, 'Мойки', '#', 1, '2026-05-26 02:41:24'),
(7, 1, 'Фильтры', '#', 3, '2026-05-26 02:41:24'),
(9, 5, 'Ulgran', '#', 1, '2026-05-26 02:41:24'),
(10, 5, 'Vigro Mramor', '#', 2, '2026-05-26 02:41:24'),
(11, 5, 'Handmade', '#', 3, '2026-05-26 02:41:24'),
(12, 5, 'Vigro Glass', '#', 4, '2026-05-26 02:41:24'),
(13, 9, 'Smth', '#', 1, '2026-05-26 02:41:24'),
(14, 9, 'Smth', '#', 2, '2026-05-26 02:41:24'),
(15, 9, 'Smth', '#', 3, '2026-05-26 02:41:24'),
(16, 11, 'Smth', '#', 1, '2026-05-26 02:41:24'),
(17, 11, 'Smth', '#', 2, '2026-05-26 02:41:24'),
(18, 7, 'Ulgran', '#', 1, '2026-05-26 02:41:24'),
(19, 7, 'Vigro Mramor', '#', 2, '2026-05-26 02:41:24'),
(20, 18, 'Smth', '#', 1, '2026-05-26 02:41:24'),
(21, 18, 'Smth', '#', 2, '2026-05-26 02:41:24');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
