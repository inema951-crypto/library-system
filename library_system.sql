-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 06, 2025 at 12:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `author` varchar(100) NOT NULL,
  `genre` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `cover_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `genre`, `year`, `cover_image`) VALUES
(1, 'Introduction to Algorithms', 'Thomas H. Cormen', 'Computer Science', 2009, 'algorithms.jpg'),
(2, 'Clean Code', 'Robert C. Martin', 'Programming', 2009, 'cleancode.jpg'),
(3, 'Design Patterns', 'Erich Gamma', 'Computer Science', 1994, 'designpatterns.jpg'),
(4, 'The Pragmatic Programmer', 'Andrew Hunt', 'Programming', 1999, 'pragmatic.jpg'),
(5, 'Database System Concepts', 'Abraham Silberschatz', 'Database', 2010, 'database.jpg'),
(6, 'Rich & Poor Dad', 'john Remy', 'Motivation  & improvement ', 2008, 'book_6890932cdf1da9.87012072.png');

-- --------------------------------------------------------

--
-- Table structure for table `book_requests`
--

CREATE TABLE `book_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `book_title` varchar(100) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `rejection_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `book_requests`
--

INSERT INTO `book_requests` (`id`, `user_id`, `book_title`, `status`, `rejection_reason`) VALUES
(2, 2, 'Introduction to Algorithms', 'Approved', NULL),
(3, 4, 'Introduction to Algorithms', 'Approved', NULL),
(4, 5, 'Introduction to Algorithms', 'Approved', NULL),
(5, 1, 'Introduction to Algorithms', 'Rejected', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role` enum('student','admin') DEFAULT 'student',
  `student_id` varchar(20) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `department` varchar(50) NOT NULL,
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `student_id`, `full_name`, `email`, `password`, `department`, `year`) VALUES
(1, 'admin', 'ADM001', 'Remy', 'kcii@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Business', 2),
(2, 'admin', '27979', 'Library Admin User', 'admin@auca.ac.rw', '$2y$10$kj7CjM/sNEiAwLzBT0jLReWP3QWDC04EZ1rWlEslfwx3KhU6c/REm', 'Education', 2),
(4, 'student', '27975', 'Niyigaba', 'jeanremyniyigaba@gmail.com', '$2y$10$6BaYlSZLmksmqFwzO4/bWO1HxCdkYNkHnKYiqm4kWMI0D12IX4BSy', 'Business', 3),
(5, 'student', '27977', 'Niyigaba jean remy', 'jeanremyniyigaba894@gmail.com', '$2y$10$4IUYt9iFFuH773MIrjF98OL2BjhmHXFk5SW1KlJY2D55jn7Lf1ATG', 'Theology', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `book_requests`
--
ALTER TABLE `book_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `book_requests`
--
ALTER TABLE `book_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book_requests`
--
ALTER TABLE `book_requests`
  ADD CONSTRAINT `book_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
