-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               10.11.6-MariaDB-0ubuntu0.23.10.2 - Ubuntu 23.10
-- Server OS:                    debian-linux-gnu
-- HeidiSQL Version:             12.6.0.6765
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table grid_site.abusereports
CREATE TABLE IF NOT EXISTS `abusereports` (
  `id` varchar(255) DEFAULT NULL,
  `reporter` varchar(255) DEFAULT NULL,
  `abuser` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `summary` varchar(255) DEFAULT NULL,
  `details` longtext DEFAULT NULL,
  `posted` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table grid_site.abusereports: ~1 rows (approximately)

-- Dumping structure for table grid_site.blog
CREATE TABLE IF NOT EXISTS `blog` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `poster` varchar(255) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '0',
  `blog` longtext NOT NULL,
  `posted` int(11) NOT NULL DEFAULT 0,
  `edited` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping structure for table grid_site.email_reset
CREATE TABLE IF NOT EXISTS `email_reset` (
  `email` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `expires` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table grid_site.email_reset: ~0 rows (approximately)

-- Dumping structure for table grid_site.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grid_site.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table grid_site.homes
CREATE TABLE IF NOT EXISTS `homes` (
  `parcel_id` varchar(255) DEFAULT NULL,
  `owner_uuid` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `pos` varchar(255) DEFAULT NULL,
  `size` bigint(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table grid_site.homes: ~0 rows (approximately)

-- Dumping structure for table grid_site.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Dumping structure for table grid_site.password_reset_tokens
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grid_site.password_reset_tokens: ~0 rows (approximately)

-- Dumping structure for table grid_site.payment
CREATE TABLE IF NOT EXISTS `payment` (
  `id` varchar(255) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `transaction` varchar(255) NOT NULL,
  `paypal_id` varchar(255) DEFAULT NULL,
  `tier` int(11) NOT NULL DEFAULT 0,
  `amount` float(255,2) DEFAULT 0.00,
  `added` int(255) NOT NULL DEFAULT 0,
  `completed` int(255) DEFAULT 0,
  `cancelled` int(255) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping structure for table grid_site.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table grid_site.personal_access_tokens: ~0 rows (approximately)

-- Dumping structure for table grid_site.proposals
CREATE TABLE IF NOT EXISTS `proposals` (
  `id` varchar(255) DEFAULT NULL,
  `asker` varchar(255) DEFAULT NULL,
  `receiver` varchar(255) DEFAULT NULL,
  `msg` longtext DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `posted` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table grid_site.proposals: ~0 rows (approximately)

-- Dumping structure for table grid_site.sqms
CREATE TABLE IF NOT EXISTS `sqms` (
  `pid` varchar(255) DEFAULT NULL,
  `uuid` varchar(255) DEFAULT NULL,
  `parcel` varchar(255) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `sqm` int(255) DEFAULT 0,
  `checked` int(255) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping structure for table grid_site.tier
CREATE TABLE IF NOT EXISTS `tier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cad` int(255) DEFAULT NULL,
  `sqms` int(255) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table grid_site.tier: ~10 rows (approximately)
INSERT INTO `tier` (`id`, `cad`, `sqms`, `size`) VALUES
	(1, 0, 1024, '1/64'),
	(2, 2, 2048, '1/32'),
	(3, 4, 4096, '1/16'),
	(4, 8, 8192, '1/8'),
	(5, 12, 16384, '1/4'),
	(6, 24, 32768, '1/2'),
	(7, 36, 49152, '3/4'),
	(8, 48, 65536, 'Full'),
	(9, 96, 131072, '2 Full'),
	(10, 192, 262144, '4 Full');

-- Dumping structure for table grid_site.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` char(36) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tos` int(1) DEFAULT 0,
  `paypal_sub` varchar(255) DEFAULT NULL,
  `tier` int(255) DEFAULT 1,
  `tier_expire` int(255) DEFAULT 0,
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
