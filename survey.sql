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
DROP DATABASE IF EXISTS `survey`;
CREATE DATABASE IF NOT EXISTS `survey` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `survey`;

-- Dumping structure for table survey.admins
DROP TABLE IF EXISTS `admins`;
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

-- Dumping data for table survey.admins: ~1 rows (approximately)
DELETE FROM `admins`;
INSERT INTO `admins` (`id`, `username`, `password_hash`, `email`, `created_at`, `updated_at`) VALUES
	(1, 'admin', '$2y$12$TFIerNPvGP3nuTaIvC.vXuRZdxc3OV1SIRdJg2BezZCF6ISeRVvWq', 'admin@survey.local', '2026-03-05 16:48:52', '2026-03-08 06:59:33');

-- Dumping structure for table survey.files
DROP TABLE IF EXISTS `files`;
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
DROP TABLE IF EXISTS `questions`;
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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.questions: ~21 rows (approximately)
DELETE FROM `questions`;
INSERT INTO `questions` (`id`, `section_id`, `question_text`, `type`, `required`, `allow_multiple_files`, `matrix_group_id`, `order_sequence`, `created_at`, `updated_at`) VALUES
	(17, 4, 'What are questions?', 'text', 1, 0, NULL, 0, '2026-03-15 08:06:50', '2026-03-15 08:06:50'),
	(18, 4, 'Yes or No?', 'yesno', 1, 0, NULL, 1, '2026-03-15 08:07:08', '2026-03-15 08:07:08'),
	(19, 4, 'Scale Questions', 'scale', 1, 0, NULL, 2, '2026-03-15 08:08:03', '2026-03-15 08:08:03'),
	(20, 4, 'Multiple Choice', 'multiple_choice', 1, 0, NULL, 3, '2026-03-15 08:08:42', '2026-03-15 08:08:42'),
	(21, 5, 'Hi', 'yesno', 1, 0, NULL, 0, '2026-03-15 08:29:08', '2026-03-15 08:29:08'),
	(22, 6, 'Which of the following best describes your current status?', 'multiple_choice', 1, 0, NULL, 0, '2026-03-26 04:31:53', '2026-03-26 04:31:53'),
	(23, 6, 'How often do you participate in elections?', 'multiple_choice', 1, 0, NULL, 1, '2026-03-26 04:34:50', '2026-03-26 04:34:50'),
	(24, 6, 'What is your main reason for voting?', 'multiple_choice', 1, 0, NULL, 2, '2026-03-26 04:38:46', '2026-03-26 04:38:46'),
	(25, 6, 'Which factor do you mostly consider when choosing a candidate?', 'multiple_choice', 1, 0, NULL, 3, '2026-03-26 04:41:01', '2026-03-26 04:44:19'),
	(26, 6, 'Which factor most influences your final voting decision?', 'multiple_choice', 1, 0, NULL, 4, '2026-03-26 04:43:28', '2026-03-26 04:43:28'),
	(27, 7, 'To what extent do you trust the current Philippine\'s National Government?', 'scale', 1, 0, NULL, 0, '2026-03-26 04:46:18', '2026-03-26 04:46:18'),
	(28, 7, 'How satisfied are you with the current administration’s handling of the national economy?', 'scale', 1, 0, NULL, 1, '2026-03-26 04:48:14', '2026-03-26 04:48:14'),
	(29, 7, 'Do you believe corruption is a major issue in our Government?', 'yesno', 1, 0, NULL, 2, '2026-03-26 04:49:32', '2026-03-26 04:49:32'),
	(30, 7, 'To what extent do you believe corruption affects the delivery of public services?', 'multiple_choice', 1, 0, NULL, 3, '2026-03-26 04:51:30', '2026-03-26 04:51:30'),
	(31, 7, 'Overall, how optimistic are you about the future of the Philippines under the current leadership?', 'multiple_choice', 1, 0, NULL, 4, '2026-03-26 04:53:47', '2026-03-26 04:53:47'),
	(32, 8, 'How would you rate our government\'s response to the current surge in oil prices?', 'multiple_choice', 1, 0, NULL, 0, '2026-03-26 04:56:14', '2026-03-26 04:56:14'),
	(33, 8, 'Which measure should the government prioritize to address the oil crisis?', 'multiple_choice', 1, 0, NULL, 1, '2026-03-26 04:59:06', '2026-03-26 04:59:06'),
	(34, 8, 'How confident are you that officials involved in failed or \'scam\' flood control projects will be held legally accountable', 'scale', 1, 0, NULL, 2, '2026-03-26 05:02:13', '2026-03-26 05:02:13'),
	(35, 8, 'How satisfied are you with the current state of disaster preparedness and flood management in your local area?', 'scale', 1, 0, NULL, 3, '2026-03-26 05:03:24', '2026-03-26 05:03:24'),
	(36, 8, 'Do you believe the government is being transparent about where the tax money are actually being used?', 'multiple_choice', 1, 0, NULL, 4, '2026-03-26 05:04:44', '2026-03-26 05:04:44'),
	(37, 8, 'In your view, which of these is the MOST urgent problem the administration needs to solve right now?', 'multiple_choice', 1, 0, NULL, 5, '2026-03-26 05:08:33', '2026-03-26 05:08:33');

-- Dumping structure for table survey.question_options
DROP TABLE IF EXISTS `question_options`;
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
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.question_options: ~79 rows (approximately)
DELETE FROM `question_options`;
INSERT INTO `question_options` (`id`, `question_id`, `option_text`, `value`, `order_sequence`, `created_at`) VALUES
	(38, 19, 'very Disagree', 'very Disagree', 0, '2026-03-15 08:08:03'),
	(39, 19, 'disagreee', 'disagreee', 1, '2026-03-15 08:08:03'),
	(40, 19, 'meh', 'meh', 2, '2026-03-15 08:08:03'),
	(41, 19, 'agreee', 'agreee', 3, '2026-03-15 08:08:03'),
	(42, 19, 'very agreee', 'very agreee', 4, '2026-03-15 08:08:03'),
	(43, 20, 'A', 'A', 0, '2026-03-15 08:08:42'),
	(44, 20, 'B', 'B', 1, '2026-03-15 08:08:42'),
	(45, 20, 'C', 'C', 2, '2026-03-15 08:08:42'),
	(46, 20, 'D', 'D', 3, '2026-03-15 08:08:42'),
	(47, 22, 'Student', 'Student', 0, '2026-03-26 04:31:53'),
	(48, 22, 'Working Student', 'Working Student', 1, '2026-03-26 04:31:53'),
	(49, 22, 'Employed / Working Professional', 'Employed / Working Professional', 2, '2026-03-26 04:31:53'),
	(50, 22, 'Unemployed / Looking for work', 'Unemployed / Looking for work', 3, '2026-03-26 04:31:53'),
	(51, 22, 'Senior Citizen', 'Senior Citizen', 4, '2026-03-26 04:31:53'),
	(52, 22, 'Prefer not to say', 'Prefer not to say', 5, '2026-03-26 04:31:53'),
	(53, 23, 'Always', 'Always', 0, '2026-03-26 04:34:50'),
	(54, 23, 'Barely', 'Barely', 1, '2026-03-26 04:34:50'),
	(55, 23, 'Never', 'Never', 2, '2026-03-26 04:34:50'),
	(56, 24, 'Civic duty', 'Civic duty', 0, '2026-03-26 04:38:46'),
	(57, 24, 'Desire for Change', 'Desire for Change', 1, '2026-03-26 04:38:46'),
	(58, 24, 'Family/Social Influence', 'Family/Social Influence', 2, '2026-03-26 04:38:46'),
	(59, 24, 'Strategic Voting', 'Strategic Voting', 3, '2026-03-26 04:38:46'),
	(60, 24, 'Personal/Cultural Interest', 'Personal/Cultural Interest', 4, '2026-03-26 04:38:46'),
	(66, 26, 'Debates', 'Debates', 0, '2026-03-26 04:43:28'),
	(67, 26, 'Social Media', 'Social Media', 1, '2026-03-26 04:43:28'),
	(68, 26, 'News Outlets', 'News Outlets', 2, '2026-03-26 04:43:28'),
	(69, 26, 'Personal Networks', 'Personal Networks', 3, '2026-03-26 04:43:28'),
	(70, 26, 'Least-Worst', 'Least-Worst', 4, '2026-03-26 04:43:28'),
	(71, 25, 'Track record', 'Track record', 0, '2026-03-26 04:44:19'),
	(72, 25, 'Political Party', 'Political Party', 1, '2026-03-26 04:44:19'),
	(73, 25, 'Popularity', 'Popularity', 2, '2026-03-26 04:44:19'),
	(74, 25, 'Recomendation', 'Recomendation', 3, '2026-03-26 04:44:19'),
	(75, 25, 'Least-worst', 'Least-worst', 4, '2026-03-26 04:44:19'),
	(76, 27, 'High', 'High', 0, '2026-03-26 04:46:18'),
	(77, 27, 'Moderate', 'Moderate', 1, '2026-03-26 04:46:18'),
	(78, 27, 'Low', 'Low', 2, '2026-03-26 04:46:18'),
	(79, 27, 'No Trust at all', 'No Trust at all', 3, '2026-03-26 04:46:18'),
	(80, 28, 'Very Satisfied', 'Very Satisfied', 0, '2026-03-26 04:48:14'),
	(81, 28, 'Satisfied', 'Satisfied', 1, '2026-03-26 04:48:14'),
	(82, 28, 'Neither Satisfied nor Dissatisfied', 'Neither Satisfied nor Dissatisfied', 2, '2026-03-26 04:48:14'),
	(83, 28, 'Dissatisfied', 'Dissatisfied', 3, '2026-03-26 04:48:14'),
	(84, 28, 'Very Dissatisfied', 'Very Dissatisfied', 4, '2026-03-26 04:48:14'),
	(85, 30, 'Extremely (It ruins almost everything)', 'Extremely (It ruins almost everything)', 0, '2026-03-26 04:51:30'),
	(86, 30, 'Very much (It’s a huge obstacle)', 'Very much (It’s a huge obstacle)', 1, '2026-03-26 04:51:30'),
	(87, 30, 'Moderately (It’s there, but things still work)', 'Moderately (It’s there, but things still work)', 2, '2026-03-26 04:51:30'),
	(88, 30, 'Slightly (It’s a minor issue)', 'Slightly (It’s a minor issue)', 3, '2026-03-26 04:51:30'),
	(89, 30, 'Not at all (our government is clean)', 'Not at all (our government is clean)', 4, '2026-03-26 04:51:30'),
	(90, 31, 'Very Optimistic (I believe things will get much better)', 'Very Optimistic (I believe things will get much better)', 0, '2026-03-26 04:53:47'),
	(91, 31, 'Somewhat Optimistic (I see some potential for improvement)', 'Somewhat Optimistic (I see some potential for improvement)', 1, '2026-03-26 04:53:47'),
	(92, 31, 'Somewhat Pessimistic (I am worried about where we are headed)', 'Somewhat Pessimistic (I am worried about where we are headed)', 2, '2026-03-26 04:53:47'),
	(93, 31, 'Very Pessimistic (I don\'t see things getting better anytime soon)', 'Very Pessimistic (I don\'t see things getting better anytime soon)', 3, '2026-03-26 04:53:47'),
	(94, 32, 'Excellent (Proactive and effective subsidies)', 'Excellent (Proactive and effective subsidies)', 0, '2026-03-26 04:56:14'),
	(95, 32, 'Good (Adequate measures to cushion the impact)', 'Good (Adequate measures to cushion the impact)', 1, '2026-03-26 04:56:14'),
	(96, 32, 'Fair (Some effort, but not enough for the average citizen)', 'Fair (Some effort, but not enough for the average citizen)', 2, '2026-03-26 04:56:14'),
	(97, 32, 'Poor (The response has been slow or ineffective)', 'Poor (The response has been slow or ineffective)', 3, '2026-03-26 04:56:14'),
	(98, 32, 'Very Poor (No noticeable effort to help consumers)', 'Very Poor (No noticeable effort to help consumers)', 4, '2026-03-26 04:56:14'),
	(99, 33, 'Suspend fuel excise taxes to lower pump prices immediately.', 'Suspend fuel excise taxes to lower pump prices immediately.', 0, '2026-03-26 04:59:06'),
	(100, 33, 'Provide more fuel subsidies for public transport and farmers.', 'Provide more fuel subsidies for public transport and farmers.', 1, '2026-03-26 04:59:06'),
	(101, 33, 'Impose a price cap on petroleum products.', 'Impose a price cap on petroleum products.', 2, '2026-03-26 04:59:06'),
	(102, 33, 'Promote long-term alternatives (e.g., electric vehicles, better mass transit).', 'Promote long-term alternatives (e.g., electric vehicles, better mass transit).', 3, '2026-03-26 04:59:06'),
	(103, 33, 'Limit and manage national consumption of remaining fuel reserves', 'Limit and manage national consumption of remaining fuel reserves', 4, '2026-03-26 04:59:06'),
	(104, 34, 'Highly Confident (They will be punished)', 'Highly Confident (They will be punished)', 0, '2026-03-26 05:02:13'),
	(105, 34, 'Somewhat Confident (Some might face consequences)', 'Somewhat Confident (Some might face consequences)', 1, '2026-03-26 05:02:13'),
	(106, 34, 'Not Confident (Investigation will likely lead to nothing)', 'Not Confident (Investigation will likely lead to nothing)', 2, '2026-03-26 05:02:13'),
	(107, 34, 'Not Confident at all (It will be forgotten like previous scandals)', 'Not Confident at all (It will be forgotten like previous scandals)', 3, '2026-03-26 05:02:13'),
	(108, 35, 'Very Satisfied', 'Very Satisfied', 0, '2026-03-26 05:03:24'),
	(109, 35, 'Satisfied', 'Satisfied', 1, '2026-03-26 05:03:24'),
	(110, 35, 'Neither Satisfied nor Dissatisfied', 'Neither Satisfied nor Dissatisfied', 2, '2026-03-26 05:03:24'),
	(111, 35, 'Dissatisfied', 'Dissatisfied', 3, '2026-03-26 05:03:24'),
	(112, 35, 'Very Dissatisfied', 'Very Dissatisfied', 4, '2026-03-26 05:03:24'),
	(113, 36, 'Yes, fully transparent', 'Yes, fully transparent', 0, '2026-03-26 05:04:44'),
	(114, 36, 'Somewhat transparent', 'Somewhat transparent', 1, '2026-03-26 05:04:44'),
	(115, 36, 'Not transparent; many projects are questionable', 'Not transparent; many projects are questionable', 2, '2026-03-26 05:04:44'),
	(116, 36, 'Not transparent at all; it’s a systematic scam', 'Not transparent at all; it’s a systematic scam', 3, '2026-03-26 05:04:44'),
	(117, 37, 'Strengthening the Philippine Peso (to lower the cost of imports and food)', 'Strengthening the Philippine Peso (to lower the cost of imports and food)', 0, '2026-03-26 05:08:33'),
	(118, 37, 'Investigating and punishing those involved in graft and corruption.', 'Investigating and punishing those involved in graft and corruption.', 1, '2026-03-26 05:08:33'),
	(119, 37, 'Increasing workers\' daily wages', 'Increasing workers\' daily wages', 2, '2026-03-26 05:08:33'),
	(120, 37, 'Improving Access to Basic Services (better healthcare, water, and electricity for all)', 'Improving Access to Basic Services (better healthcare, water, and electricity for all)', 3, '2026-03-26 05:08:33'),
	(121, 37, 'Improving Education & Skills (to prevent the PH from being left behind globally)', 'Improving Education & Skills (to prevent the PH from being left behind globally)', 4, '2026-03-26 05:08:33');

-- Dumping structure for table survey.respondents
DROP TABLE IF EXISTS `respondents`;
CREATE TABLE IF NOT EXISTS `respondents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `survey_id` int NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `address` text,
  `age` int DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_survey_submitted` (`survey_id`,`submitted_at`),
  KEY `email` (`email`),
  KEY `age` (`age`),
  CONSTRAINT `respondents_ibfk_1` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.respondents: ~2 rows (approximately)
DELETE FROM `respondents`;
INSERT INTO `respondents` (`id`, `survey_id`, `fullname`, `email`, `address`, `age`, `submitted_at`, `created_at`, `updated_at`) VALUES
	(1, 3, '', 't.gamil.com', 'balibago', 14, '2026-03-15 08:42:36', '2026-03-15 08:42:36', '2026-03-15 08:42:36'),
	(2, 2, '', 'z.gamil.com', 'balibago', 19, '2026-03-15 08:43:50', '2026-03-15 08:43:50', '2026-03-15 08:43:50');

-- Dumping structure for table survey.responses
DROP TABLE IF EXISTS `responses`;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.responses: ~5 rows (approximately)
DELETE FROM `responses`;
INSERT INTO `responses` (`id`, `respondent_id`, `question_id`, `answer_value`, `created_at`, `updated_at`) VALUES
	(1, 1, 21, 'yes', '2026-03-15 08:42:36', '2026-03-15 08:42:36'),
	(2, 2, 17, 'an inquiry', '2026-03-15 08:43:50', '2026-03-15 08:43:50'),
	(3, 2, 18, 'yes', '2026-03-15 08:43:50', '2026-03-15 08:43:50'),
	(4, 2, 19, 'very Disagree', '2026-03-15 08:43:50', '2026-03-15 08:43:50'),
	(5, 2, 20, 'A', '2026-03-15 08:43:50', '2026-03-15 08:43:50');

-- Dumping structure for table survey.sections
DROP TABLE IF EXISTS `sections`;
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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.sections: ~5 rows (approximately)
DELETE FROM `sections`;
INSERT INTO `sections` (`id`, `survey_id`, `title`, `description`, `is_respondent_info`, `order_sequence`, `created_at`) VALUES
	(4, 2, 'Questions', 'questioning', 0, 0, '2026-03-15 08:06:33'),
	(5, 3, 'Section A', 'A', 0, 0, '2026-03-15 08:28:59'),
	(6, 4, 'Political Background and Participation', '', 0, 0, '2026-03-25 08:48:54'),
	(7, 4, 'Governance Satisfaction and Institutional Trust', '', 0, 1, '2026-03-25 08:50:20'),
	(8, 4, 'Current National Issues & Governance', '', 0, 2, '2026-03-25 08:51:00');

-- Dumping structure for table survey.surveys
DROP TABLE IF EXISTS `surveys`;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table survey.surveys: ~3 rows (approximately)
DELETE FROM `surveys`;
INSERT INTO `surveys` (`id`, `name`, `description`, `is_public`, `is_active`, `passkey`, `created_by`, `created_at`, `updated_at`) VALUES
	(2, 'The Survey', 'a survey of surveys', 0, 1, 'dd5577de0ec0a5cc1d2c', 1, '2026-03-15 08:03:18', '2026-03-26 05:12:26'),
	(3, 'Private Survey', 'this is private', 0, 1, '94215ca129516d1e46e2', 1, '2026-03-15 08:28:47', '2026-03-15 08:29:19'),
	(4, 'Politics in the Philippines', 'a survey about political issues and trends', 1, 0, '7d8b4e6c84946a700115', 1, '2026-03-25 08:48:25', '2026-03-25 08:48:25');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
