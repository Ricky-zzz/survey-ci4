-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for survey
CREATE DATABASE IF NOT EXISTS `survey` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `survey`;

-- Dumping structure for table survey.admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(150) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.admins: ~0 rows (approximately)
DELETE FROM `admins`;
INSERT INTO `admins` (`id`, `username`, `password_hash`, `email`, `created_at`, `updated_at`) VALUES
	(1, 'admin', '$2y$12$TFIerNPvGP3nuTaIvC.vXuRZdxc3OV1SIRdJg2BezZCF6ISeRVvWq', 'admin@survey.local', '2026-03-05 16:48:52', '2026-03-08 06:59:33');

-- Dumping structure for table survey.files
CREATE TABLE IF NOT EXISTS `files` (
  `id` int NOT NULL AUTO_INCREMENT,
  `respondent_id` int NOT NULL,
  `question_id` int NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `file_size` int NOT NULL,
  `file_type` varchar(50) DEFAULT 'pdf',
  `uploaded_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  KEY `idx_respondent_question` (`respondent_id`,`question_id`),
  CONSTRAINT `files_ibfk_1` FOREIGN KEY (`respondent_id`) REFERENCES `respondents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `files_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.files: ~0 rows (approximately)
DELETE FROM `files`;

-- Dumping structure for table survey.questions
CREATE TABLE IF NOT EXISTS `questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `section_id` int NOT NULL,
  `question_text` text NOT NULL,
  `type` enum('text','yesno','scale','multiple_choice','file_upload') NOT NULL,
  `required` tinyint(1) DEFAULT '1',
  `allow_multiple_files` tinyint(1) DEFAULT '0',
  `matrix_group_id` varchar(100) DEFAULT NULL,
  `order_sequence` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `section_id` (`section_id`),
  KEY `idx_matrix_group` (`matrix_group_id`),
  CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.questions: ~16 rows (approximately)
DELETE FROM `questions`;
INSERT INTO `questions` (`id`, `section_id`, `question_text`, `type`, `required`, `allow_multiple_files`, `matrix_group_id`, `order_sequence`, `created_at`, `updated_at`) VALUES
	(1, 1, 'First Name', 'text', 1, 0, NULL, 1, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(2, 1, 'Last Name', 'text', 1, 0, NULL, 2, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(3, 1, 'Middle Name', 'text', 0, 0, NULL, 3, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(4, 1, 'Email', 'text', 1, 0, NULL, 4, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(5, 1, 'Sex', 'multiple_choice', 1, 0, NULL, 5, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(6, 1, 'Age', 'text', 0, 0, NULL, 6, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(7, 2, 'How satisfied are you with our service?', 'scale', 1, 0, NULL, 1, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(8, 2, 'Would you recommend us to others?', 'yesno', 1, 0, NULL, 2, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(9, 2, 'What is your primary feedback?', 'text', 0, 0, NULL, 3, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(10, 2, 'Which department helped you most?', 'multiple_choice', 1, 0, NULL, 4, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(11, 2, 'Please upload your feedback document (PDF only)', 'file_upload', 0, 0, NULL, 5, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(12, 3, 'Product Quality', 'scale', 1, 0, 'service-quality', 1, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(13, 3, 'Customer Service', 'scale', 1, 0, 'service-quality', 2, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(14, 3, 'Communication', 'scale', 1, 0, 'service-quality', 3, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(15, 3, 'Price Value', 'scale', 1, 0, 'service-quality', 4, '2026-03-05 16:48:52', '2026-03-05 16:48:52'),
	(16, 3, 'Timeliness of Delivery', 'scale', 1, 0, 'service-quality', 5, '2026-03-05 16:48:52', '2026-03-05 16:48:52');

-- Dumping structure for table survey.question_options
CREATE TABLE IF NOT EXISTS `question_options` (
  `id` int NOT NULL AUTO_INCREMENT,
  `question_id` int NOT NULL,
  `option_text` varchar(255) NOT NULL,
  `value` varchar(100) NOT NULL,
  `order_sequence` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `question_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.question_options: ~17 rows (approximately)
DELETE FROM `question_options`;
INSERT INTO `question_options` (`id`, `question_id`, `option_text`, `value`, `order_sequence`, `created_at`) VALUES
	(1, 5, 'Male', 'M', 1, '2026-03-05 16:48:52'),
	(2, 5, 'Female', 'F', 2, '2026-03-05 16:48:52'),
	(3, 5, 'Other', 'O', 3, '2026-03-05 16:48:52'),
	(4, 7, 'Very Unsatisfied', '1', 1, '2026-03-05 16:48:52'),
	(5, 7, 'Unsatisfied', '2', 2, '2026-03-05 16:48:52'),
	(6, 7, 'Neutral', '3', 3, '2026-03-05 16:48:52'),
	(7, 7, 'Satisfied', '4', 4, '2026-03-05 16:48:52'),
	(8, 7, 'Very Satisfied', '5', 5, '2026-03-05 16:48:52'),
	(9, 12, 'Strongly Disagree', '1', 1, '2026-03-05 16:48:52'),
	(10, 12, 'Disagree', '2', 2, '2026-03-05 16:48:52'),
	(11, 12, 'Neutral', '3', 3, '2026-03-05 16:48:52'),
	(12, 12, 'Agree', '4', 4, '2026-03-05 16:48:52'),
	(13, 12, 'Strongly Agree', '5', 5, '2026-03-05 16:48:52'),
	(34, 10, 'Sales', '1', 1, '2026-03-05 16:48:52'),
	(35, 10, 'Support', '2', 2, '2026-03-05 16:48:52'),
	(36, 10, 'Technical', '3', 3, '2026-03-05 16:48:52'),
	(37, 10, 'Other', '4', 4, '2026-03-05 16:48:52');

-- Dumping structure for table survey.respondents
CREATE TABLE IF NOT EXISTS `respondents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `survey_id` int NOT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_survey_submitted` (`survey_id`,`submitted_at`),
  CONSTRAINT `respondents_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.respondents: ~0 rows (approximately)
DELETE FROM `respondents`;

-- Dumping structure for table survey.responses
CREATE TABLE IF NOT EXISTS `responses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `respondent_id` int NOT NULL,
  `question_id` int NOT NULL,
  `answer_value` longtext,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_respondent_question` (`respondent_id`,`question_id`),
  KEY `question_id` (`question_id`),
  CONSTRAINT `responses_ibfk_1` FOREIGN KEY (`respondent_id`) REFERENCES `respondents` (`id`) ON DELETE CASCADE,
  CONSTRAINT `responses_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.responses: ~0 rows (approximately)
DELETE FROM `responses`;

-- Dumping structure for table survey.sections
CREATE TABLE IF NOT EXISTS `sections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `survey_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `is_respondent_info` tinyint(1) DEFAULT '0',
  `order_sequence` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `survey_id` (`survey_id`),
  CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.sections: ~3 rows (approximately)
DELETE FROM `sections`;
INSERT INTO `sections` (`id`, `survey_id`, `title`, `description`, `is_respondent_info`, `order_sequence`, `created_at`) VALUES
	(1, 1, 'Your Information', 'Please provide your details', 1, 0, '2026-03-05 16:48:52'),
	(2, 1, 'Service Feedback', 'Tell us about your experience', 0, 1, '2026-03-05 16:48:52'),
	(3, 1, 'Service Quality Matrix', 'Rate the following aspects of our service', 0, 2, '2026-03-05 16:48:52');

-- Dumping structure for table survey.surveys
CREATE TABLE IF NOT EXISTS `surveys` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `is_public` tinyint(1) DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `passkey` varchar(100) DEFAULT NULL,
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `surveys_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.surveys: ~0 rows (approximately)
DELETE FROM `surveys`;
INSERT INTO `surveys` (`id`, `name`, `description`, `is_public`, `is_active`, `passkey`, `created_by`, `created_at`, `updated_at`) VALUES
	(1, 'Customer Feedback Survey', 'Help us improve our service', 1, 1, NULL, 1, '2026-03-05 16:48:52', '2026-03-05 16:48:52');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
