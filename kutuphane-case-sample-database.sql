-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 19 Haz 2025, 10:42:00
-- Sunucu sürümü: 10.4.28-MariaDB
-- PHP Sürümü: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `kutuphane-case`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `authors`
--

CREATE TABLE `authors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `authors`
--

INSERT INTO `authors` (`id`, `name`, `email`, `created_at`) VALUES
(1, 'Nazım Hikmet', 'nhikmet@dogankitap.com', '2025-06-19 08:16:42'),
(3, 'Orhan Pamuk', 'orhan@dogankitap.com', '2025-06-19 08:25:48'),
(4, 'Sabahattin Ali', 'sabahattin@dogankitap.com', '2025-06-19 08:35:21');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `author_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `publication_year` year(4) NOT NULL,
  `page_count` int(11) NOT NULL,
  `is_available` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `books`
--

INSERT INTO `books` (`id`, `title`, `isbn`, `author_id`, `category_id`, `publication_year`, `page_count`, `is_available`, `created_at`) VALUES
(3, 'Memleketimden İnsan Manzaraları', '9789750803772', 1, 2, '1957', 258, 1, '2025-06-19 08:41:34'),
(4, 'Sevdalı Bulut', '978-975-08-4475-1', 1, 2, '1947', 140, 1, '2025-06-19 08:41:34'),
(5, 'Benim Adım Kırmızı', '978-975-08-2592-7', 3, 3, '2012', 452, 1, '2025-06-19 08:41:34'),
(6, 'Masumiyet Müzesi', '978-975-08-2614-6', 3, 3, '2007', 387, 1, '2025-06-19 08:41:34'),
(7, 'Değirmen', '978-975-08-0660-3', 4, 4, '1947', 240, 1, '2025-06-19 08:41:34'),
(8, 'Yeni Dünya', '978-975-363-0661-1', 4, 4, '1953', 106, 1, '2025-06-19 08:41:34');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(2, 'Şiir', 'Bu kategoride yazarlara ait şiirleri bulabilirsiniz.', '2025-06-19 08:30:59'),
(3, 'Roman', 'Bu kategoride yazarlara ait romanları bulabilirsiniz', '2025-06-19 08:30:59'),
(4, 'Hikaye', 'Bu kategoride yazarlara ait hikayeleri görebilirsiniz', '2025-06-19 08:30:59');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_authors_name` (`name`),
  ADD KEY `idx_authors_email` (`email`);

--
-- Tablo için indeksler `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `idx_books_title` (`title`),
  ADD KEY `idx_books_isbn` (`isbn`),
  ADD KEY `idx_books_author_id` (`author_id`),
  ADD KEY `idx_books_category_id` (`category_id`);

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categories_name` (`name`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `authors`
--
ALTER TABLE `authors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `fk_books_author` FOREIGN KEY (`author_id`) REFERENCES `authors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_books_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
