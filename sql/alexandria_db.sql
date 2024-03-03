-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Εξυπηρετητής: 127.0.0.1:3306
-- Χρόνος δημιουργίας: 03 Μαρ 2024 στις 22:10:01
-- Έκδοση διακομιστή: 5.7.31
-- Έκδοση PHP: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Βάση δεδομένων: `alexandria_db`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `department`
--

DROP TABLE IF EXISTS `department`;
CREATE TABLE IF NOT EXISTS `department` (
  `dpt_id` varchar(30) NOT NULL,
  `dpt_school_id` varchar(20) NOT NULL,
  PRIMARY KEY (`dpt_id`),
  KEY `fk_school_id` (`dpt_school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `department_info`
--

DROP TABLE IF EXISTS `department_info`;
CREATE TABLE IF NOT EXISTS `department_info` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `dptid` varchar(30) NOT NULL,
  `dpt_full_name` varchar(300) NOT NULL,
  `valid_from` datetime NOT NULL,
  `valid_until` datetime DEFAULT NULL,
  PRIMARY KEY (`rid`),
  KEY `fk_dptid` (`dptid`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `faculty_member`
--

DROP TABLE IF EXISTS `faculty_member`;
CREATE TABLE IF NOT EXISTS `faculty_member` (
  `id` varchar(50) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `role` varchar(10) DEFAULT NULL,
  `rank` varchar(30) NOT NULL,
  `phd_year` int(11) DEFAULT NULL,
  `google_scholar_id` varchar(100) DEFAULT NULL,
  `department` varchar(30) NOT NULL,
  `scopus_id` varchar(20) DEFAULT NULL,
  `isValidated` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = not validated / 1 = validated',
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `orcid_id` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_role_id` (`role`),
  KEY `fk_department_id` (`department`),
  KEY `fk_rank_id` (`rank`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `faculty_member_in_report`
--

DROP TABLE IF EXISTS `faculty_member_in_report`;
CREATE TABLE IF NOT EXISTS `faculty_member_in_report` (
  `report_id` int(11) NOT NULL,
  `provider_id` varchar(50) NOT NULL,
  `facultymember_id` varchar(100) NOT NULL,
  `info_metadata` text,
  `metrics_metadata` text,
  PRIMARY KEY (`report_id`,`provider_id`,`facultymember_id`),
  KEY `fk_facultymember_id` (`facultymember_id`),
  KEY `fk_fmir_report_id` (`report_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `pa_user`
--

DROP TABLE IF EXISTS `pa_user`;
CREATE TABLE IF NOT EXISTS `pa_user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `role` varchar(10) NOT NULL,
  `viewFacultyMemberID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `pa_user_role`
--

DROP TABLE IF EXISTS `pa_user_role`;
CREATE TABLE IF NOT EXISTS `pa_user_role` (
  `id` varchar(10) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Άδειασμα δεδομένων του πίνακα `pa_user_role`
--

INSERT INTO `pa_user_role` (`id`, `description`) VALUES
('admin', 'Administrator'),
('fm', 'Faculty Member');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `publication_of_faculty_member_in_report`
--

DROP TABLE IF EXISTS `publication_of_faculty_member_in_report`;
CREATE TABLE IF NOT EXISTS `publication_of_faculty_member_in_report` (
  `facultymember_id` varchar(100) NOT NULL,
  `report_id` int(11) NOT NULL,
  `provider_id` varchar(50) NOT NULL,
  `pub_title` varchar(1000) DEFAULT NULL,
  `pub_authors` varchar(500) DEFAULT NULL,
  `pub_venue` varchar(500) DEFAULT NULL,
  `pub_date` date DEFAULT NULL,
  `pub_doi` varchar(300) DEFAULT NULL,
  `pub_citedby` int(11) DEFAULT NULL,
  `pub_issn` varchar(200) DEFAULT NULL,
  `pub_type` varchar(200) DEFAULT NULL,
  `pub_subtype` varchar(200) DEFAULT NULL,
  `pub_subtype_description` varchar(200) DEFAULT NULL,
  `pub_source_id` varchar(200) DEFAULT NULL,
  `pub_provider_id` varchar(200) NOT NULL,
  `scimagojr_q` varchar(10) DEFAULT NULL,
  KEY `fk_pub_report_id` (`report_id`),
  KEY `fk_pub_fm_id` (`facultymember_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `rank`
--

DROP TABLE IF EXISTS `rank`;
CREATE TABLE IF NOT EXISTS `rank` (
  `rank_id` varchar(30) NOT NULL,
  `rank_full_title` varchar(200) NOT NULL,
  `rank_short_title` varchar(200) NOT NULL,
  `rank_order_id` int(1) NOT NULL,
  PRIMARY KEY (`rank_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Άδειασμα δεδομένων του πίνακα `rank`
--

INSERT INTO `rank` (`rank_id`, `rank_full_title`, `rank_short_title`, `rank_order_id`) VALUES
('assistant_professor', 'Επίκουρος/-η Καθηγητής/-ρια', 'Επικ. Καθ.', 3),
('associate_professor', 'Αναπληρωτής/-ρια Καθηγητής/-ρια', 'Αν. Καθ.', 2),
('edip', 'ΕΔΙΠ', 'ΕΔΙΠ', 5),
('lecturer', 'Λέκτορας', 'Λεκ.', 4),
('no_rank', '-', '-', 6),
('professor', 'Καθηγητής/-ρια', 'Καθ.', 1);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `report`
--

DROP TABLE IF EXISTS `report`;
CREATE TABLE IF NOT EXISTS `report` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `report_title` varchar(200) NOT NULL,
  `report_datetime_created` datetime NOT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `role_id` varchar(30) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `role_order_id` int(1) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Άδειασμα δεδομένων του πίνακα `role`
--

INSERT INTO `role` (`role_id`, `role_name`, `role_order_id`) VALUES
('dep', 'ΔΕΠ', 1),
('edip', 'ΕΔΙΠ', 2);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `school`
--

DROP TABLE IF EXISTS `school`;
CREATE TABLE IF NOT EXISTS `school` (
  `school_id` varchar(30) NOT NULL,
  `school_name` varchar(500) NOT NULL,
  PRIMARY KEY (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `scopus_api_requests_log`
--
DROP TABLE IF EXISTS `scopus_api_requests_log`;
CREATE TABLE IF NOT EXISTS `scopus_api_requests_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(2000) DEFAULT NULL,
  `http_response_status` varchar(100) NOT NULL,
  `response_headers` text NOT NULL,
  `request_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Περιορισμοί για άχρηστους πίνακες
--

--
-- Περιορισμοί για πίνακα `department`
--
ALTER TABLE `department`
  ADD CONSTRAINT `fk_school_id` FOREIGN KEY (`dpt_school_id`) REFERENCES `school` (`school_id`) ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `department_info`
--
ALTER TABLE `department_info`
  ADD CONSTRAINT `fk_dptid` FOREIGN KEY (`dptid`) REFERENCES `department` (`dpt_id`);

--
-- Περιορισμοί για πίνακα `faculty_member`
--
ALTER TABLE `faculty_member`
  ADD CONSTRAINT `fk_department_id` FOREIGN KEY (`department`) REFERENCES `department` (`dpt_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rank_id` FOREIGN KEY (`rank`) REFERENCES `rank` (`rank_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_role_id` FOREIGN KEY (`role`) REFERENCES `role` (`role_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
