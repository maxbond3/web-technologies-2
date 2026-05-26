-- MySQL dump 10.13  Distrib 8.4.8, for Win64 (x86_64)
--
-- Host: MySQL-8.4    Database: product_catalog
-- ------------------------------------------------------
-- Server version	8.4.8

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS product_catalog;
USE product_catalog;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `parent_id` int DEFAULT NULL,
  `sort_order` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Электроника',NULL,1),(2,'Одежда',NULL,2),(3,'Дом и сад',NULL,3),(4,'Смартфоны',1,1),(5,'Ноутбуки',1,2),(6,'Планшеты',1,3),(7,'Мужская одежда',2,1),(8,'Женская одежда',2,2),(9,'Обувь',2,3),(10,'Мебель',3,1),(11,'Освещение',3,2),(12,'Текстиль',3,3);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT 'default.jpg',
  `category_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'iPhone 15 Pro',999.99,'Новейший смартфон Apple с процессором A17 Pro и камерой 48 МП','iphone15pro.jpg',4,'2026-05-26 03:07:39','2026-05-26 03:12:05'),(2,'Samsung Galaxy S24',899.99,'Флагманский смартфон Samsung с AI-функциями и Dynamic AMOLED дисплеем','galaxy_s24.jpg',4,'2026-05-26 03:07:39','2026-05-26 03:07:39'),(3,'MacBook Pro 16\"',2499.99,'Мощный ноутбук с чипом M3 Pro для профессиональной работы','macbook_pro.jpg',5,'2026-05-26 03:07:39','2026-05-26 03:07:39'),(4,'iPad Air',599.99,'Планшет с чипом M1 и 10.9-дюймовым Liquid Retina дисплеем','ipad_air.jpg',6,'2026-05-26 03:07:39','2026-05-26 03:07:39'),(5,'Классический костюм Hugo Boss',899.99,'Элегантный мужской костюм из итальянской шерсти. Идеальная посадка и премиальное качество. Подкладка из вискозы обеспечивает комфорт в течение всего дня.','boss_suit.jpg',7,'2026-05-26 03:23:43','2026-05-26 03:23:43'),(6,'Джинсы Levi\'s 501 Original',89.99,'Классические прямые джинсы Levi\'s с легендарной посадкой. 100% хлопок высшего качества. Проверенная временем модель, которая никогда не выходит из моды.','levis501.jpg',7,'2026-05-26 03:23:43','2026-05-26 03:23:43'),(7,'Пуховик The North Face Nuptse',299.99,'Теплый пуховик The North Face с гусиным пухом 700 Fill. Водоотталкивающая ткань Ripstop. Съемный капюшон и ветрозащитная планка.','tnf_jacket.jpg',7,'2026-05-26 03:23:43','2026-05-26 03:23:43'),(8,'Платье Michael Kors',249.99,'Элегантное вечернее платье Michael Kors из итальянского крепа. Приталенный силуэт, длина миди. Идеально для особых случаев и деловых мероприятий.','mk_dress.jpg',8,'2026-05-26 03:23:43','2026-05-26 03:23:43'),(9,'Тренч Burberry Kensington',1990.99,'Легендарный тренч Burberry из хлопкового габардина. Классический крой с поясом, двубортная застежка. Клетчатая подкладка Vintage Check.','burberry_coat.jpg',8,'2026-05-26 03:23:43','2026-05-26 03:23:43'),(11,'Кроссовки Nike Air Max 270',159.99,'Культовые кроссовки Nike с амортизацией Air Max. Дышащий верх из сетки, резиновая подошва. Самый большой Air элемент в пяточной части.','nike_airmax.jpg',9,'2026-05-26 03:23:43','2026-05-26 03:23:43'),(12,'Туфли Christian Louboutin Pigalle',749.99,'Знаменитые туфли-лодочки Christian Louboutin на шпильке 100 мм. Лакированная кожа, знаковая красная подошва. Символ элегантности и роскоши.','louboutin.jpg',9,'2026-05-26 03:23:43','2026-05-26 03:23:43'),(14,'Диван Chesterfield Classic',2499.99,'Роскошный диван Chesterfield с каретной стяжкой. Натуральная кожа, глубокий коричневый цвет. Дубовый каркас и пружинный блок. Вмещает 3-4 человека.','chesterfield.jpg',10,'2026-05-26 03:23:43','2026-05-26 03:23:43'),(16,'Книжный шкаф IKEA BILLY',79.99,'Универсальный книжный шкаф BILLY с регулируемыми полками. Белый цвет, ЛДСП. Глубина 28 см, высота 202 см. Вмещает до 200 книг.','billy.jpg',10,'2026-05-26 03:23:43','2026-05-26 03:23:43'),(18,'Настольная лампа Anglepoise Type 75',249.99,'Классическая настольная лампа Anglepoise с запатентованным пружинным механизмом. Алюминиевый корпус, 3 точки регулировки. Идеально для рабочего стола.','anglepoise.jpg',11,'2026-05-26 03:23:43','2026-05-26 03:23:43'),(19,'Светодиодная лента Philips Hue',79.99,'Умная светодиодная лента Philips Hue с поддержкой 16 млн цветов. Длина 2 метра, управление через приложение. Совместимость с Alexa и Google Home.','hue_strip.jpg',11,'2026-05-26 03:23:43','2026-05-26 03:23:43'),(21,'Плед Tartan Scotland',149.99,'Шерстяной плед в традиционную шотландскую клетку. 100% шерсть мериноса, размер 150x200 см. Ручная работа, классический узор Royal Stewart.','tartan_blanket.jpg',12,'2026-05-26 03:23:43','2026-05-26 03:23:43'),(22,'Шторы Blackout Porto',89.99,'Шторы блэкаут Porto с полным затемнением. Трехслойная ткань, термоизоляция. Размер 140x260 см, 2 штуки в комплекте. Цвет: графит.','blackout_curtains.jpg',12,'2026-05-26 03:23:43','2026-05-26 03:23:43');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reviews` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `author` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `rating` int NOT NULL,
  `comment` text NOT NULL,
  `is_approved` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reviews`
--

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (1,1,'Mahkambek','mahkambek.hamrayev@mail.ru',4,'Super super',1,'2026-05-26 03:56:06'),(2,5,'Mahkambek','mahkambek.hamrayev@mail.ru',5,'Super!!!!!!!!!!',1,'2026-05-26 03:56:45');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-26  5:11:31
