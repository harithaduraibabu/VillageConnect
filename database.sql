-- ============================================================
-- Database: csp
-- Project: Community Portal for Village Democracy
-- Project: VillageConnect – Community Portal for Village Democracy
-- Resume Title: Community Portal for Village Democracy
-- Technologies: Core PHP, MySQL, HTML5, CSS3, JavaScript
-- ============================================================

CREATE DATABASE IF NOT EXISTS `csp` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `csp`;

-- ------------------------------------------------------------
-- 1. Table structure for `citizens`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `citizens` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `phone` VARCHAR(15) NOT NULL,
    `village_name` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 2. Table structure for `admins`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admins` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 3. Table structure for `grievances`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grievances` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `citizen_id` INT NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `status` ENUM('Pending', 'In Progress', 'Resolved') DEFAULT 'Pending',
    `admin_remark` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`citizen_id`) REFERENCES `citizens`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 4. Table structure for `announcements`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `announcements` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT NOT NULL,
    `published_date` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 5. Table structure for `government_schemes`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `government_schemes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `scheme_name` VARCHAR(200) NOT NULL,
    `description` TEXT NOT NULL,
    `eligibility` TEXT NOT NULL,
    `procedure_info` TEXT NOT NULL,
    `additional_info` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
-- 6. Table structure for `feedback`
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `feedback` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `citizen_id` INT NOT NULL,
    `subject` VARCHAR(200) NOT NULL,
    `message` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`citizen_id`) REFERENCES `citizens`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SEED DATA FOR DEMONSTRATION & COLLEGE TESTING
-- ============================================================

-- Seed Administrator Account
-- Username: admin
-- Password: admin123
INSERT INTO `admins` (`id`, `username`, `full_name`, `email`, `password`) VALUES
(1, 'admin', 'System Administrator', 'admin@csp.local', '$2y$10$NvMljkm5/s8bEa.ZayQod.dlURjLt06pyx0PodiqljXWJ2yk6kndS')
ON DUPLICATE KEY UPDATE `username` = `username`;

-- Seed Sample Citizen Account
-- Email: ramesh@example.com
-- Password: password123
INSERT INTO `citizens` (`id`, `full_name`, `email`, `phone`, `village_name`, `password`) VALUES
(1, 'Ramesh Sharma', 'ramesh@example.com', '9876543210', 'Sundarpur', '$2y$10$2TGzbzfZVCDJY/vC/VHMM.5ka7QJ.VkUFvLDPwGUjkrv06N9e5agm')
ON DUPLICATE KEY UPDATE `email` = `email`;

-- Seed Sample Announcements
INSERT INTO `announcements` (`id`, `title`, `description`, `published_date`) VALUES
(1, 'Annual Gram Sabha General Meeting', 'All village citizens are cordially invited to attend the upcoming Gram Sabha meeting on 25th November at the Panchayat Community Hall. Topics include water distribution, local road repairs, and new self-help group funds.', '2026-11-20'),
(2, 'Free Health Checkup and Eye Screening Camp', 'A comprehensive free health checkup camp organized by the District Health Mission will be held at the Primary Health Center this Sunday from 9:00 AM to 4:00 PM. General physicians and eye specialists will be available.', '2026-11-18'),
(3, 'Monsoon Drainage Clearing and Sanitation Drive', 'The village administration will begin a thorough cleaning and desilting of roadside drains starting this Monday. All residents are requested to ensure garbage is not dumped into open drains.', '2026-11-15'),
(4, 'Drinking Water Pipeline Maintenance Schedule', 'Please take note that maintenance work on the main drinking water pipeline will be conducted tomorrow between 10:00 AM and 2:00 PM. Residents are advised to store sufficient water in advance.', '2026-11-10')
ON DUPLICATE KEY UPDATE `title` = `title`;

-- Seed Sample Government Schemes
INSERT INTO `government_schemes` (`id`, `scheme_name`, `description`, `eligibility`, `procedure_info`, `additional_info`) VALUES
(1, 'Pradhan Mantri Gram Sadak Yojana (PMGSY)', 'A nationwide initiative to provide all-weather road connectivity to unconnected rural habitations, fostering socioeconomic rural progress.', 'Rural habitations with designated population criteria lacking all-weather road connections.', 'Gram Panchayat prepares and passes road proposals in Gram Sabha; proposals are forwarded to the Block Development Office for survey and state sanction.', 'Funded centrally under rural infrastructure programs.'),
(2, 'Jal Jeevan Mission (Har Ghar Jal)', 'Flagship program to provide functional household tap connection (FHTC) providing safe and clean drinking water to every rural household.', 'All rural families, village schools, and Anganwadi centers in need of tap connections.', 'Apply via Gram Panchayat or Village Water and Sanitation Committee (VWSC) desk; connection installed after feasibility check.', 'Water quality testing kits are made available at the Panchayat.'),
(3, 'Pradhan Mantri Awas Yojana - Gramin (PMAY-G)', 'Financial assistance program for homeless rural families and those living in kutcha or dilapidated houses to construct a durable pucca house.', 'Rural families verified by Socio-Economic and Caste Census (SECC) list and approved by the Gram Sabha.', 'Beneficiary list is verified during Gram Sabha. Sanctioned funds are credited in installments directly into verified bank accounts.', 'Includes financial support for sanitary toilet construction.'),
(4, 'Deen Dayal Upadhyaya Gram Jyoti Yojana', 'Electrification scheme aimed at continuous power supply to rural homes and dedicated agricultural feeder separation for farmers.', 'Rural households and agricultural land clusters requiring domestic or farm power connections.', 'Submit application with land/identity proof at the local electricity sub-station or village camps.', 'Ensures reliable electrical supply and metering.')
ON DUPLICATE KEY UPDATE `scheme_name` = `scheme_name`;

-- Seed Sample Grievances
INSERT INTO `grievances` (`id`, `citizen_id`, `title`, `category`, `description`, `status`, `admin_remark`) VALUES
(1, 1, 'Low water pressure in Ward 3 supply line', 'Water Supply', 'For the past 4 days, the municipal tap water pressure has been very low in our lane. Families are struggling to fill storage containers in the morning.', 'In Progress', 'Field technician has been dispatched to check the feeder pump valve.'),
(2, 1, 'Street light not working near primary school', 'Street Lights', 'The sodium street light pole right opposite the village primary school has been flickering and completely off for one week. Children face difficulty during evening tuition hours.', 'Pending', NULL)
ON DUPLICATE KEY UPDATE `title` = `title`;

-- Seed Sample Feedback
INSERT INTO `feedback` (`id`, `citizen_id`, `subject`, `message`) VALUES
(1, 1, 'Great initiative for our village', 'The portal makes it very convenient to track grievances without visiting the Panchayat office multiple times. Thank you for this digital platform.')
ON DUPLICATE KEY UPDATE `subject` = `subject`;
