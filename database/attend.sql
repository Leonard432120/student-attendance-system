-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 29, 2026 at 03:04 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attend`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `action`, `created_at`) VALUES
(24, 1, 'Updated class ID: 3', '2026-03-19 22:10:59'),
(23, 1, 'Updated class ID: 1', '2026-03-19 22:10:47'),
(22, 1, 'Updated class ID: 1', '2026-03-19 22:10:38'),
(21, 1, 'Deleted teacher: Leonardponje mlungu (leonardponjemlungu@gmail.com)', '2026-03-19 21:56:56'),
(20, 1, 'Updated teacher: Leonardponje mlungu (14)', '2026-03-19 21:56:31'),
(19, 1, 'Added teacher: Leonardponje mlungu (15)', '2026-03-19 21:53:58'),
(18, 1, 'Added student: Leonard (Admission No: 70)', '2026-03-19 21:47:31'),
(17, 1, 'Added student: Leonard (Admission No: 10)', '2026-03-19 21:46:46'),
(16, 1, 'Edited student ID 19', '2026-03-19 13:09:17'),
(25, 1, 'Updated class ID: 4', '2026-03-19 22:11:07'),
(26, 1, 'Updated class ID: 2', '2026-03-19 22:11:16'),
(27, 1, 'Assigned teacher ID 8 to class Form 1', '2026-03-19 23:02:53'),
(28, 1, 'Added student: Muyira (Admission No: 90)', '2026-03-21 09:56:25'),
(29, 1, 'Assigned teacher to class Form 1', '2026-03-21 11:08:13'),
(30, 1, 'Added student: PONJE (Admission No: 100)', '2026-03-25 07:30:11'),
(31, 1, 'Added student: PONJEKKKKK (Admission No: 100)', '2026-03-25 07:32:46'),
(32, 1, 'Added student: PONJEKKKKK (Admission No: 100)', '2026-03-25 07:58:20'),
(33, 1, 'Added student: PONJEuu (Admission No: 1001)', '2026-03-25 08:12:35'),
(34, 1, 'Added student: hiee (Admission No: 1002)', '2026-03-25 08:18:10'),
(35, 1, 'Added student: PONJE (Admission No: 1)', '2026-03-25 08:48:30'),
(36, 1, 'Added student: PONJE (Admission: 90)', '2026-03-28 21:44:50'),
(37, 1, 'Added student: PONJE (Admission: 91)', '2026-03-28 21:51:25'),
(38, 1, 'Added teacher: Leonardponje mlungu (10)', '2026-03-28 21:56:12'),
(39, 1, 'Assigned teacher to subject English in class Form 1', '2026-03-28 22:19:46'),
(40, 1, 'Assigned teacher to subject Mathematics in class Form 1', '2026-03-28 22:20:07'),
(41, 1, 'Assigned teacher to subject English in class Form 1', '2026-03-28 22:21:54'),
(42, 1, 'Assigned teacher to subject Mathematics in class Form 1', '2026-03-28 22:22:01'),
(43, 1, 'Assigned teacher to class Form 2', '2026-03-28 22:22:48'),
(44, 1, 'Assigned teacher to subject Mathematics in class Form 2', '2026-03-28 22:25:50'),
(45, 1, 'Assigned teacher to subject English in class Form 1', '2026-03-28 23:14:52'),
(46, 1, 'Assigned teacher to subject English in class Form 1', '2026-03-28 23:16:54'),
(47, 1, 'Assigned teacher to subject English in class Form 1', '2026-03-28 23:17:25');

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

DROP TABLE IF EXISTS `assessments`;
CREATE TABLE IF NOT EXISTS `assessments` (
  `assessment_id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `test_mark` int DEFAULT '0',
  `assignment_mark` int DEFAULT '0',
  `exam_mark` int DEFAULT '0',
  PRIMARY KEY (`assessment_id`),
  KEY `student_id` (`student_id`),
  KEY `subject_id` (`subject_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assessments`
--

INSERT INTO `assessments` (`assessment_id`, `student_id`, `subject_id`, `test_mark`, `assignment_mark`, `exam_mark`) VALUES
(3, 27, 1, 10, 13, 60);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE IF NOT EXISTS `attendance` (
  `attendance_id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `class_id` int NOT NULL,
  `status` enum('Present','Absent') COLLATE utf8mb4_unicode_ci NOT NULL,
  `attendance_date` date NOT NULL,
  PRIMARY KEY (`attendance_id`),
  UNIQUE KEY `unique_attendance` (`student_id`,`class_id`,`attendance_date`),
  KEY `class_id` (`class_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `student_id`, `class_id`, `status`, `attendance_date`) VALUES
(1, 1, 1, 'Absent', '2026-03-17'),
(2, 1, 1, 'Absent', '2026-03-15'),
(3, 27, 1, 'Present', '2026-03-25'),
(4, 27, 1, 'Present', '2026-03-29');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `class_id` int NOT NULL AUTO_INCREMENT,
  `class_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_teacher` int DEFAULT NULL,
  PRIMARY KEY (`class_id`),
  KEY `class_teacher` (`class_teacher`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`, `class_teacher`) VALUES
(1, 'Form 1', 9),
(2, 'Form 4', NULL),
(3, 'Form 2', 9),
(4, 'Form 3', NULL),
(5, 'Chichewa', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `class_assignments`
--

DROP TABLE IF EXISTS `class_assignments`;
CREATE TABLE IF NOT EXISTS `class_assignments` (
  `assignment_id` int NOT NULL AUTO_INCREMENT,
  `teacher_id` int NOT NULL,
  `class_id` int NOT NULL,
  `subject_id` int DEFAULT NULL,
  `assigned_date` date DEFAULT (curdate()),
  PRIMARY KEY (`assignment_id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `class_id` (`class_id`),
  KEY `subject_id` (`subject_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_assignments`
--

INSERT INTO `class_assignments` (`assignment_id`, `teacher_id`, `class_id`, `subject_id`, `assigned_date`) VALUES
(1, 4, 1, 1, '2026-03-14');

-- --------------------------------------------------------

--
-- Table structure for table `class_subjects`
--

DROP TABLE IF EXISTS `class_subjects`;
CREATE TABLE IF NOT EXISTS `class_subjects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_id` int NOT NULL,
  `subject_id` int NOT NULL,
  `teacher_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `class_id` (`class_id`),
  KEY `subject_id` (`subject_id`),
  KEY `teacher_id` (`teacher_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_subjects`
--

INSERT INTO `class_subjects` (`id`, `class_id`, `subject_id`, `teacher_id`) VALUES
(1, 1, 1, 8),
(2, 1, 2, 9),
(3, 3, 3, 9);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `student_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `admission_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `gender` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `admission_number` (`admission_number`),
  KEY `user_id` (`user_id`),
  KEY `class_id` (`class_id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `admission_number`, `class_id`, `gender`, `date_of_birth`, `phone`) VALUES
(29, 43, '91', 2, NULL, NULL, ''),
(28, 41, '90', 3, NULL, NULL, NULL),
(27, 39, '1', 1, NULL, NULL, '0984487611');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `subject_id` int NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` int DEFAULT NULL,
  PRIMARY KEY (`subject_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_name`, `class_id`) VALUES
(1, 'English', 1),
(2, 'Mathematics', 1),
(3, 'Mathematics', 3);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
CREATE TABLE IF NOT EXISTS `teachers` (
  `teacher_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `employee_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`teacher_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `user_id`, `employee_number`) VALUES
(2, 4, '12'),
(3, 26, '12'),
(8, 31, '11'),
(9, 32, '10'),
(11, 44, '10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','teacher','student') COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.png',
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `username`, `password`, `role`, `email`, `phone`, `created_at`, `profile_image`, `last_login`) VALUES
(45, 'Mlungu', 'MlunguAdmin', '$2y$10$CwTycUXWue0Thq9StjUM0uJ8D5YdYkB4xOQbzfPwM3dq6M3kSxI7e', 'admin', 'leonardponjemlungu@gmail.com', '0984487611', '2026-03-29 02:53:44', 'default.png', NULL),
(43, 'PONJE', 'Ponje john mlu', '$2y$10$89dW4U2ZvaceNLtCnqVf7u2xq0RxDGZ8rmI0r4SP2gdFh5e8Zg0EC', 'student', 'leonardponjemlungu@gmail.com', '0984487611', '2026-03-28 21:51:25', '1774752348_480659117_635476765639415_7425634803651385261_n.jpg', '2026-03-29 04:45:14'),
(32, 'Leonard ponje', 'Ponje2', '$2y$10$G9IjmCKMrk976KdEOjPohudG30DJxCPaz.GpjtHbVQH1SUjnMdmCe', 'teacher', 'leonardponjemlungu@gmail.com', '0899520423', '2026-03-19 21:38:23', 'default.png', '2026-03-29 04:47:29'),
(1, 'PONJE ADMIN', 'Padmin', '$2y$10$HNJODCdqUaL89XoJ4UIyyOw8Lz1wQxkwSkQvlN1AzVTRR289h7R6.', 'admin', 'leonardponjemlungu@gmail.com', '0984487611', '2026-03-29 03:00:26', 'default.png', '2026-03-29 05:02:55'),
(44, 'Leonardponje mlungu', 'Ponje leonard', '$2y$10$G9IjmCKMrk976KdEOjPohudG30DJxCPaz.GpjtHbVQH1SUjnMdmCe', 'teacher', 'leonardponjemlungu@gmail.com', '0899520423', '2026-03-28 21:56:12', 'teacher_69c84e7c36cc44.88536482.jpg', NULL),
(42, 'PONJE', 'Ponje joh', '$2y$10$G9IjmCKMrk976KdEOjPohudG30DJxCPaz.GpjtHbVQH1SUjnMdmCe', 'student', 'leonardponjemlungu@gmail.com', '0984487611', '2026-03-28 21:45:47', 'default.png', '2026-03-29 02:39:33'),
(41, 'PONJE', 'Ponje john', '$2y$10$G9IjmCKMrk976KdEOjPohudG30DJxCPaz.GpjtHbVQH1SUjnMdmCe', 'student', 'leonardponjemlungu@gmail.com', '0984487611', '2026-03-28 21:44:50', 'student_69c84bd2d8d0d0.09106302.jpg', '2026-03-29 02:21:05'),
(40, 'PONJE John', 'Ponj', '$2y$10$G9IjmCKMrk976KdEOjPohudG30DJxCPaz.GpjtHbVQH1SUjnMdmCe', 'student', 'leonardponjemlungu@gmail.com', '0984487611', '2026-03-28 21:43:37', 'student_69c84b8937db88.54898304.jpg', NULL),
(31, 'Leonard', 'Leonard1', '$2y$10$G9IjmCKMrk976KdEOjPohudG30DJxCPaz.GpjtHbVQH1SUjnMdmCe', 'teacher', 'leonardponjemlungu@gmail.com', '0899520423', '2026-03-19 21:36:41', 'default.png', NULL),
(39, 'PONJE', 'Ponje1', '$2y$10$sgRqFfSq6hba9qFPJBk86.PeUPjwFQnJAuaUtUSu7bfFnwLMDqXA.', 'student', 'leonardmlungupro@gmail.com', '0984487611', '2026-03-25 08:48:27', '1774746138_ponje.jpg', '2026-03-29 02:40:27');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
