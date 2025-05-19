-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 10:59 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(50) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `first_name`, `last_name`, `email`, `password`, `verification_code`, `is_verified`, `status`) VALUES
(3, 'nathalie', 'nathalie', 'degsayannathalie1420@gmail.com', '$2y$10$ULBfAFCpwp/4hsngpEYaReeMf2T0w8NBOE0qPwY6NkwHwANJZjBgO', '603609', 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(100) DEFAULT NULL,
  `teacher_id` varchar(10) DEFAULT NULL,
  `teacher_name` varchar(100) DEFAULT NULL,
  `student_id` varchar(20) DEFAULT NULL,
  `student_first_name` varchar(50) DEFAULT NULL,
  `student_last_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`class_id`, `class_name`, `teacher_id`, `teacher_name`, `student_id`, `student_first_name`, `student_last_name`) VALUES
(13, 'IM101', '1', 'teacher NATHALIE', '10', 'x1', 'x1'),
(14, 'IM101', '1', 'teacher NATHALIE', '11', 'x2', 'x2'),
(16, 'IM102', '1', 'teacher NATHALIE', '10', 'x1', 'x1'),
(17, 'IM102', '1', 'teacher NATHALIE', '12', 'x3', 'x3'),
(19, 'IT ELEC 101', '1', 'teacher NATHALIE', '15', 'x6', 'x6'),
(20, 'IT ELEC 101', '1', 'teacher NATHALIE', '16', 'x7', 'x7'),
(21, 'IT ELEC 101', '1', 'teacher NATHALIE', '17', 'nathalie', 'hotdog');

-- --------------------------------------------------------

--
-- Table structure for table `class_activities`
--

CREATE TABLE `class_activities` (
  `activity_id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `activity_file` varchar(255) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_activities`
--

INSERT INTO `class_activities` (`activity_id`, `class_name`, `student_id`, `title`, `description`, `activity_file`, `teacher_id`, `created_at`) VALUES
(1, 'IM101', NULL, 'activity 1', 'pass it in ftf', 'HI NATHALIe.docx', 1, '2025-05-15 22:37:01'),
(2, 'IM101', NULL, 'IM101-Midterm Laboratory Exam', 'nstruction: Screen Record your output and upload it in your preferred medium(google drive or youtube) make sure your video is set to public so I could watch your work.', 'HI NATHALIe.docx', 1, '2025-05-15 22:39:33'),
(3, 'IM101', 9, 'sddsdsdfsdfds', 'ffdffffffffffffffffff', 'HI NATHALIe.docx', 1, '2025-05-16 01:46:04'),
(4, 'IM101', 10, 'sdssddsd', 'xzccccccccccccccccccccc', 'HI NATHALIe.docx', 1, '2025-05-16 01:47:44'),
(5, 'IM101', 9, 'dsdaasdasd', 'sdasdsadsadasdadasdadas', 'HI NATHALIe.docx', 1, '2025-05-16 02:51:18'),
(6, 'IT ELEC 101', NULL, 'activity', 'pass it in ftf on thursday', 'PROPOSAL.docx', 1, '2025-05-16 04:20:54');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `contact_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_type` enum('student','teacher') NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`contact_id`, `user_id`, `user_type`, `contact_name`, `contact_email`, `contact_phone`, `relationship`, `created_at`) VALUES
(1, 1, 'teacher', 'rods', 'dasdasdsd@gmail.com', '0936039970', 'nothing', '2025-05-15 07:33:12'),
(3, 9, 'student', 'nathalie', 'nathalie1@gmail.com', '09000000000', 'nathalie', '2025-05-15 10:19:24'),
(4, 9, 'student', 'ark ', 'arkrods@gmail.com', '09000000000', 'HELLO', '2025-05-15 10:20:23');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_number` varchar(50) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `verification_code` varchar(10) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `status` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_number`, `first_name`, `last_name`, `email`, `password`, `phone`, `gender`, `verification_code`, `is_verified`, `status`) VALUES
(10, '2024-00001', 'x1', 'x1', 'x1@gmail.com', '$2y$10$sHWSJw3Q3w1IbqMWVNvWt.9ErtiGdzKxwQoQfm/T5V3LI3O2K.B7S', '09000000001', 'Male', NULL, 0, 'inactive'),
(11, '2024-00002', 'x2', 'x2', 'x2@gmail.com', '$2y$10$IE7/9lpICXXp.Yyrs53pe.SwwzsuZEj506IgIM8T1LkOfnJXIZ/R2', '09000000002', 'Male', NULL, 0, 'inactive'),
(12, '2024-00003', 'x3', 'x3', 'x3@gmail.com', '$2y$10$2Fz869BgqAc3sx1z0stoh.bSz/1BKcUZ2X/8Fnsm1RjAEPL6653Na', '09000000003', 'Male', NULL, 0, 'inactive'),
(13, '2024-00004', 'x4', 'x4', 'x4@gmail.com', '$2y$10$RDOQY09o5oeX/rgiit2DbOoxU63dXpzmh7BhHsYBllhqdCCyucdOe', '09000000004', 'Female', NULL, 0, 'inactive'),
(14, '2024-00005', 'x5', 'x5', 'x5@gmail.com', '$2y$10$BDSANGndfK1Ddm2Fl38vWOfLYqoR81sXXNQU2Y40CraK4dVhJCKZa', '09000000005', 'Female', NULL, 0, 'inactive'),
(15, '2024-00006', 'x6', 'x6', 'x6@gmail.com', '$2y$10$oFND5rJlKe3lJ9LmdijOAOaQcBcFRqVZWQxmssFC1ewgQYh6gr8ue', '09000000006', 'Female', NULL, 0, 'inactive'),
(16, '2024-00007', 'x7', 'x7', 'x7@gmail.com', '$2y$10$a3TiZCnNNev//CbsJytX7OhMaOqXFUppDP0cZo6/L/mHbt4k7Z/lC', '09000000007', 'Female', NULL, 0, 'inactive'),
(17, '2023-03271', 'nathalie', 'degsayan', 'degsayannathalie1420@gmail.com', '$2y$10$45sdTo82rEPicsnYbSxd3OSsCUwwzQvFTE/WLrFN1TSxxJ3qSZxkO', '09111111111', 'Female', '231052', 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `student_submissions`
--

CREATE TABLE `student_submissions` (
  `submission_id` int(11) NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `submitted_file` varchar(255) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(100) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `first_name`, `last_name`, `email`, `password`, `verification_code`, `is_verified`, `status`) VALUES
(1, 'teacher', 'NATHALIE', 'degsayannathalie1420@gmail.com', '$2y$10$yFPhLQGwhGwRN5DB/SPk0.b5B0IXs.qdKvVTaqujClI7s5g5X9OFa', '212490', 1, 'active'),
(2, 'natalie', 'natalie', 'user@example.com', '$2y$10$VmdadM7lbzXHdzsjss/PnOjgv4w8QCAEJKKDImgYpExGMdgmWpMfK', 'bdf501da', 0, 'inactive');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `class_activities`
--
ALTER TABLE `class_activities`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`contact_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `student_submissions`
--
ALTER TABLE `student_submissions`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `activity_id` (`activity_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `class_activities`
--
ALTER TABLE `class_activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `contact_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `student_submissions`
--
ALTER TABLE `student_submissions`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_submissions`
--
ALTER TABLE `student_submissions`
  ADD CONSTRAINT `student_submissions_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `class_activities` (`activity_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
