-- Adminer 4.8.1 MySQL 8.0.44-0ubuntu0.24.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `attributes`;
CREATE TABLE `attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `attr_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attr_slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attr_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `att_status` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `attr_input_type` enum('option','text') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'text',
  `categories_id` bigint unsigned DEFAULT NULL,
  `parent_lan_id` bigint unsigned DEFAULT NULL,
  `lan_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_id` (`categories_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `attributes` (`id`, `attr_name`, `attr_slug`, `attr_type`, `att_status`, `attr_input_type`, `categories_id`, `parent_lan_id`, `lan_id`, `created_at`, `updated_at`) VALUES
(1,	'Titel Ihrer Ausschreibung',	'titel-ihrer-ausschreibung',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-14 06:56:26',	'2024-06-17 04:07:21'),
(2,	'Auftragsbeschreibung',	'auftragsbeschreibung',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-17 03:58:01',	'2024-06-17 04:07:37'),
(3,	'Mengen- und Massangaben',	'mengen-und-massangaben',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-17 03:58:47',	'2024-06-17 04:07:57'),
(4,	'Angaben zu Materialien',	'angaben-zu-materialien',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-17 03:59:02',	'2024-06-17 04:08:15'),
(5,	'Aktuelle Situation',	'aktuelle-situation',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-17 03:59:17',	'2024-06-17 04:08:31'),
(6,	'Haben Sie Bilder und Dokumente zu zeigen?',	'haben-sie-bilder-und-dokumente-zu-zeigen',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-19 07:37:07',	'2024-06-19 07:37:07'),
(7,	'Wo ist der Einsatzort? (Bitte Postleitzahl eingeben und Ort aus Vorschlag auswählen)',	'wo-ist-der-einsatzort-bitte-postleitzahl-eingeben-und-ort-aus-vorschlag-auswahlen',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-21 00:21:47',	'2024-06-21 00:21:47'),
(8,	'Wann soll die Arbeit erledigt werden?',	'wann-soll-die-arbeit-erledigt-werden',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-21 00:23:59',	'2024-06-21 00:23:59'),
(9,	'Vorname',	'vorname',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-24 02:20:36',	'2024-06-24 02:20:36'),
(10,	'Nachname',	'nachname',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-24 02:20:46',	'2024-06-24 02:20:46'),
(11,	'Strasse, Nr.',	'strasse-nr',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-24 02:21:10',	'2024-06-24 02:22:18'),
(12,	'PLZ/Ort',	'plzort',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-24 02:21:35',	'2024-06-24 02:22:36'),
(13,	'E-Mail',	'e-mail',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-24 02:21:53',	'2024-06-24 02:23:11'),
(14,	'Mobile',	'mobile',	NULL,	'1',	'text',	NULL,	NULL,	NULL,	'2024-06-24 02:23:28',	'2024-06-24 02:23:28');

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_spatie.permission.cache',	'a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:8:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:9:\"role-list\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:11:\"role-create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:9:\"role-edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:11:\"role-delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:12:\"product-list\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:14:\"product-create\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:12:\"product-edit\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:14:\"product-delete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:2:{i:0;i:1;i:1;i:3;}}}s:5:\"roles\";a:3:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:5:\"Admin\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:6:\"Editor\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:11:\"Super Admin\";s:1:\"c\";s:3:\"web\";}}}',	1767268926);

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `cart_products`;
CREATE TABLE `cart_products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(6,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cart_products` (`id`, `name`, `description`, `image`, `price`, `created_at`, `updated_at`) VALUES
(1,	'Test one',	'test one',	'',	100.00,	'2025-05-29 10:47:37',	'2025-05-29 10:47:37'),
(2,	'Test two',	'Test two',	'',	500.00,	'2025-05-29 10:47:31',	'2025-05-29 10:47:31'),
(3,	'Test Three',	'Test Three',	NULL,	400.00,	'2025-05-29 10:48:24',	'2025-05-29 10:48:24'),
(4,	'Test Four',	'Test Four',	NULL,	200.00,	'2025-05-29 10:49:53',	'2025-05-29 10:49:53'),
(5,	'Test Five',	'Test Five',	NULL,	600.00,	'2025-05-29 10:49:53',	'2025-05-29 10:49:53'),
(6,	'Test six',	'Test six',	NULL,	300.00,	'2025-05-29 10:49:53',	'2025-05-29 10:49:53');

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1,	'0001_01_01_000000_create_users_table',	1),
(2,	'0001_01_01_000001_create_cache_table',	1),
(3,	'0001_01_01_000002_create_jobs_table',	1),
(4,	'2025_05_28_051602_create_permission_tables',	1),
(5,	'2025_05_28_051750_create_products_table',	2),
(6,	'2025_05_29_102646_create_products_table',	3);

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1,	'App\\Models\\User',	1),
(3,	'App\\Models\\User',	2);

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1,	'role-list',	'web',	'2025-05-28 00:19:04',	'2025-05-28 00:19:04'),
(2,	'role-create',	'web',	'2025-05-28 00:19:04',	'2025-05-28 00:19:04'),
(3,	'role-edit',	'web',	'2025-05-28 00:19:04',	'2025-05-28 00:19:04'),
(4,	'role-delete',	'web',	'2025-05-28 00:19:04',	'2025-05-28 00:19:04'),
(5,	'product-list',	'web',	'2025-05-28 00:19:04',	'2025-05-28 00:19:04'),
(6,	'product-create',	'web',	'2025-05-28 00:19:04',	'2025-05-28 00:19:04'),
(7,	'product-edit',	'web',	'2025-05-28 00:19:04',	'2025-05-28 00:19:04'),
(8,	'product-delete',	'web',	'2025-05-28 00:19:04',	'2025-05-28 00:19:04');

DROP TABLE IF EXISTS `product_attribute_values`;
CREATE TABLE `product_attribute_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `products_id` bigint unsigned NOT NULL,
  `attributes_id` bigint unsigned NOT NULL,
  `categories_id` bigint unsigned NOT NULL,
  `product_attributes_id` bigint unsigned NOT NULL,
  `pro_attr_value` varchar(255) NOT NULL,
  `status` enum('0','1') NOT NULL,
  `parent_lan_id` bigint unsigned DEFAULT NULL,
  `lan_id` bigint unsigned DEFAULT NULL,
  `ordering` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_id` (`products_id`),
  KEY `attributes_id` (`attributes_id`),
  KEY `categories_id` (`categories_id`),
  KEY `product_attributes_id` (`product_attributes_id`),
  CONSTRAINT `product_attribute_values_ibfk_1` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`),
  CONSTRAINT `product_attribute_values_ibfk_2` FOREIGN KEY (`attributes_id`) REFERENCES `attributes` (`id`),
  CONSTRAINT `product_attribute_values_ibfk_3` FOREIGN KEY (`categories_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `product_attribute_values_ibfk_4` FOREIGN KEY (`product_attributes_id`) REFERENCES `product_attributes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `product_attribute_values` (`id`, `products_id`, `attributes_id`, `categories_id`, `product_attributes_id`, `pro_attr_value`, `status`, `parent_lan_id`, `lan_id`, `ordering`, `created_at`, `updated_at`) VALUES
(1,	2,	8,	7,	8,	'As soon as possible',	'1',	NULL,	NULL,	NULL,	'2024-06-21 02:04:24',	'2024-06-21 02:04:24'),
(2,	2,	8,	7,	8,	'Within the next 30 days',	'1',	NULL,	NULL,	NULL,	'2024-06-21 02:05:21',	'2024-06-21 02:05:21'),
(3,	2,	8,	7,	8,	'In more than 3 months',	'1',	NULL,	NULL,	NULL,	'2024-06-21 02:06:01',	'2024-06-21 02:06:01'),
(4,	2,	8,	7,	8,	'According to the arrangement',	'1',	NULL,	NULL,	NULL,	'2024-06-21 02:06:24',	'2024-06-21 02:06:24');

DROP TABLE IF EXISTS `product_attributes`;
CREATE TABLE `product_attributes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `products_id` bigint unsigned NOT NULL,
  `attributes_id` bigint unsigned NOT NULL,
  `categories_id` bigint unsigned DEFAULT NULL,
  `attribute_values` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pro_att_status` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1',
  `parent_lan_id` bigint unsigned DEFAULT NULL,
  `lan_id` bigint unsigned DEFAULT NULL,
  `attribute_input_types_id` bigint unsigned DEFAULT NULL,
  `form_steps_id` bigint unsigned DEFAULT NULL,
  `mandatory_field` enum('Yes','No') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'No',
  `ordering` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `placeholder` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_attributes_products_id_foreign` (`products_id`),
  KEY `product_attributes_attributes_id_foreign` (`attributes_id`),
  KEY `attribute_input_types_id` (`attribute_input_types_id`),
  KEY `form_steps_id` (`form_steps_id`),
  CONSTRAINT `product_attributes_attributes_id_foreign` FOREIGN KEY (`attributes_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_attributes_ibfk_1` FOREIGN KEY (`attribute_input_types_id`) REFERENCES `attribute_input_types` (`id`),
  CONSTRAINT `product_attributes_ibfk_2` FOREIGN KEY (`form_steps_id`) REFERENCES `form_steps` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_attributes` (`id`, `products_id`, `attributes_id`, `categories_id`, `attribute_values`, `pro_att_status`, `parent_lan_id`, `lan_id`, `attribute_input_types_id`, `form_steps_id`, `mandatory_field`, `ordering`, `created_at`, `updated_at`, `placeholder`) VALUES
(1,	1,	1,	7,	NULL,	'1',	NULL,	NULL,	2,	1,	'Yes',	1,	'2024-06-17 03:42:43',	'2024-06-21 01:50:48',	'Geben Sie Ihrer Ausschreibung einen Titel, der sie gut beschreibt.'),
(2,	1,	2,	7,	NULL,	'1',	NULL,	NULL,	5,	1,	'Yes',	2,	'2024-06-17 04:17:36',	'2024-06-17 04:17:36',	'Geben Sie Ihrer Ausschreibung einen Titel, der sie gut beschreibt.'),
(3,	1,	3,	7,	NULL,	'1',	NULL,	NULL,	7,	1,	'No',	3,	'2024-06-17 04:20:39',	'2024-06-18 07:56:37',	NULL),
(4,	1,	4,	7,	NULL,	'1',	NULL,	NULL,	7,	1,	'No',	4,	'2024-06-17 04:21:01',	'2024-06-19 05:24:59',	NULL),
(5,	1,	5,	7,	NULL,	'1',	NULL,	NULL,	7,	1,	'No',	6,	'2024-06-17 04:21:18',	'2024-06-19 05:25:00',	NULL),
(6,	1,	6,	7,	NULL,	'1',	NULL,	NULL,	8,	1,	'No',	7,	'2024-06-19 07:37:54',	'2024-06-19 07:37:54',	'Klicken Sie zum Hochladen oder legen Sie die Datei hier ab (Zulässige Dateitypen: PDF, JPG, PNG, MP4)'),
(7,	1,	7,	7,	NULL,	'1',	NULL,	NULL,	9,	2,	'Yes',	1,	'2024-06-21 00:23:30',	'2024-06-24 03:06:23',	'Wo ist der Einsatzort? (Bitte Postleitzahl eingeben und Ort aus Vorschlag auswählen)'),
(8,	1,	8,	7,	NULL,	'1',	NULL,	NULL,	1,	2,	'No',	2,	'2024-06-21 00:24:58',	'2024-06-21 00:24:58',	NULL),
(9,	1,	9,	7,	NULL,	'1',	NULL,	NULL,	2,	3,	'Yes',	1,	'2024-06-24 02:24:26',	'2024-06-24 02:25:06',	'Vorname'),
(10,	1,	10,	7,	NULL,	'1',	NULL,	NULL,	2,	3,	'Yes',	2,	'2024-06-24 02:24:55',	'2024-06-24 02:24:55',	'Nachname'),
(11,	1,	11,	7,	NULL,	'1',	NULL,	NULL,	2,	3,	'Yes',	3,	'2024-06-24 02:25:37',	'2024-06-24 02:25:37',	'Strasse, Nr.'),
(12,	1,	12,	7,	NULL,	'1',	NULL,	NULL,	9,	3,	'Yes',	4,	'2024-06-24 02:26:05',	'2024-06-24 03:07:26',	'PLZ / Ort'),
(13,	1,	13,	7,	NULL,	'1',	NULL,	NULL,	2,	3,	'Yes',	5,	'2024-06-24 02:26:32',	'2024-06-24 02:26:32',	'Kontaktmöglichkeiten: (basierend auf der angegebenen E-Mail wird Ihr Account erstellt)'),
(14,	1,	14,	7,	NULL,	'1',	NULL,	NULL,	2,	3,	'Yes',	6,	'2024-06-24 02:26:59',	'2024-06-24 02:26:59',	'Format: +41 98 765 43 21'),
(15,	2,	1,	7,	NULL,	'1',	NULL,	NULL,	2,	1,	'Yes',	1,	'2024-09-19 06:04:08',	'2024-09-19 06:04:08',	'Enter title');

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` (`id`, `name`, `detail`, `created_at`, `updated_at`) VALUES
(1,	'test',	'test product',	'2025-05-28 00:40:02',	'2025-05-28 00:40:02');

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1,	1),
(2,	1),
(3,	1),
(4,	1),
(5,	1),
(6,	1),
(7,	1),
(8,	1),
(1,	2),
(5,	2),
(1,	3),
(2,	3),
(3,	3),
(4,	3),
(5,	3),
(6,	3),
(7,	3),
(8,	3);

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1,	'Admin',	'web',	'2025-05-28 00:20:17',	'2025-05-28 00:20:17'),
(2,	'Editor',	'web',	'2025-05-28 00:38:35',	'2025-05-28 00:38:35'),
(3,	'Super Admin',	'web',	'2025-05-28 07:37:51',	'2025-05-28 07:37:51');

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('QVVpLd9yv5BpJLXafaMlRrfuSe85Tztg7bGfRoLX',	2,	'::1',	'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36',	'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiWjc4TzJLU0lSRGh1MmtNMWxBRDQxclk2bnFnZHBqRzZ3bnJFd281MyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDY6Imh0dHA6Ly9sb2NhbGhvc3QvVXNlcnJvbGVhbmRwZXJtaXNzaW9ucy9wdWJsaWMiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjQ6ImNhcnQiO2E6MTp7aToxO2E6NDp7czo0OiJuYW1lIjtzOjg6IlRlc3Qgb25lIjtzOjg6InF1YW50aXR5IjtpOjE7czo1OiJwcmljZSI7czo2OiIxMDAuMDAiO3M6NToiaW1hZ2UiO3M6MDoiIjt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6NDoiYXV0aCI7YToxOntzOjIxOiJwYXNzd29yZF9jb25maXJtZWRfYXQiO2k6MTc2NzE4MjUyMDt9fQ==',	1767182765);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1,	'Hardik Savani',	'admin@gmail.com',	NULL,	'$2y$12$7Yth8kJQnWPAp1EQzfcGmOJzwqCNBO2aB3uSGcdrrZO7WyQ3/vN6S',	NULL,	'2025-05-28 00:20:17',	'2025-05-28 00:20:17'),
(2,	'Editor',	'admin@admin.com',	NULL,	'$2y$12$dS1us2icDxUDM6yfwQDffuCwRT/wInRbmR1mav6yiFC8l5xLu5PhS',	NULL,	'2025-05-28 00:39:07',	'2025-05-28 00:39:07');

-- 2025-12-31 12:12:11
