
# SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
# SET time_zone = "+00:00";


CREATE DATABASE IF NOT EXISTS `lms` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `lms`;

-- Table structure for table `lms_admin`
--

CREATE TABLE `lms_admin` (
  `admin_id` int(11) NOT NULL,
  `admin_email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `admin_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lms_admin`
--

INSERT INTO `lms_admin` (`admin_id`, `admin_email`, `admin_password`) VALUES
(1, 'johnsmith1@gmail.com', '$2y$10$3BXNIMDEzxBj9wH6.dWkWuP2prxk4qt3XMJ4hLiIXEyItyZO5QiSy'); # password

-- --------------------------------------------------------

--
-- Table structure for table `lms_book`
--

CREATE TABLE `lms_setting` (
  `setting_id` int(11) NOT NULL,
  `library_total_book_issue_day` int(5) NOT NULL,
  `library_issue_total_book_per_user` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `lms_setting` (`setting_id`, `library_total_book_issue_day`, `library_issue_total_book_per_user`) VALUES
(1, 10, 2);


CREATE TABLE `lms_book` (
  `book_id` int(11) NOT NULL,
  `book_category` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `book_author` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `book_name` text COLLATE utf8_unicode_ci NOT NULL,
  `book_isbn_number` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `book_no_of_copy` int(5) NOT NULL,
  `book_status` enum('Enable','Disable') COLLATE utf8_unicode_ci NOT NULL,
  `book_added_on` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `book_updated_on` varchar(30) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lms_book`
--

INSERT INTO `lms_book` (`book_id`, `book_category`, `book_author`, `book_name`, `book_isbn_number`, `book_no_of_copy`, `book_status`, `book_added_on`, `book_updated_on`) VALUES
(1, 'Programming Skill', 'Alan Forbes', 'The Joy of PHP Programming', '978152279214', 5, 'Enable', '2021-11-11 17:32:33', '2021-11-11 18:19:21'),
(2, 'Programming Skill', 'Tom Butler', 'PHP and MySQL Novice to Ninja', '852369852123', 5, 'Enable', '2021-11-12 12:56:23', '2021-12-28 17:59:06'),
(3, 'Programming Skill', 'Lynn Beighley', 'Head First PHP and MySQL', '7539518526963', 5, 'Enable', '2021-11-12 12:57:04', ''),
(4, 'Programming Skill', 'Vikram Vaswani','PHP A Beginners Guide', '74114774147', 5, 'Enable','2021-11-12 12:57:47', ''),
(5, 'Programming Skill', 'Daginn Reiersol', 'PHP In Action Objects Design Agility', '85225885258', 5, 'Enable','2021-11-12 12:58:34', ''),
(6, 'Programming Skill', 'Joel Murach', 'Murachs PHP and MySQL', '8585858596632', 5, 'Enable','2021-11-12 13:00:15', ''),
(7, 'Programming Skill', 'Robin Nixon', 'Learning PHP MySQL JavaScript and CSS Creating Dynamic Websites', '753852963258', 5, 'Enable', '2021-11-12 13:01:10', '2021-11-12 13:02:16'),
(8, 'Programming Skill', 'Kevin Tatroe', 'Programming PHP Creating Dynamic Web Pages', '969335785842', 5, 'Enable','2021-11-12 13:01:57', ''),
(9, 'Programming Skill', 'Bruce Berke', 'PHP Programming and MySQL Database for Web Development', '963369852258', 5, 'Enable', '2021-11-12 13:02:48', '2021-11-17 10:58:27'),
(10, 'Programming Skill', 'Brett McLaughlin', 'PHP MySQL The Missing Manual', '85478569856', 5, 'Enable', '2021-11-12 13:03:51', '2021-11-14 17:07:04'),
(11, 'Programming Skill', 'Sanjib Sinha', 'Beginning Laravel A beginners guide', '856325774562', 5, 'Enable','2021-11-12 13:04:39', ''),
(12, 'Programming Skill', 'Brian Messenlehner', 'Building Web Apps with WordPress', '96325741258', 5, 'Enable','2021-11-12 13:05:18', ''),
(13, 'Programming Skill', 'Dayle Rees', 'The Laravel Framework Version 5 For Beginners', '336985696363', 5, 'Enable','2021-11-12 13:05:56', ''),
(14, 'Programming Skill', 'Carlos Buenosvinos', 'Domain Driven Design in PHP', '852258963475', 5, 'Enable','2021-11-12 13:06:35', '2021-12-11 10:36:01'),
(15, 'Programming', 'Bruce Berke', 'Learn PHP The Complete Beginners Guide to Learn PHP Programming', '744785963520', 5, 'Enable','2021-11-12 13:07:27', '2021-12-09 18:37:14'),
(16, 'Database Management', 'Laura Thompson', 'PHP and MySQL Web Development', '753951852123', 1, 'Enable','2021-11-17 10:43:19', '2021-11-17 11:03:05'),
(17, 'Web Development', 'Mark Myers', 'A Smarter Way to Learn JavaScript', '852369753951', 1, 'Enable','2021-12-08 18:48:11', '2021-12-28 18:03:30');

-- --------------------------------------------------------

--
-- Table structure for table `lms_issue_book`
--

CREATE TABLE `lms_issue_book` (
  `issue_book_id` int(11) NOT NULL,
  `book_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `issue_date_time` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `expected_return_date` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `return_date_time` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `book_issue_status` enum('Issue','Return','Not Return') COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lms_issue_book`
--

INSERT INTO `lms_issue_book` (`issue_book_id`, `book_id`, `user_id`, `issue_date_time`, `expected_return_date`, `return_date_time`, `book_issue_status`) VALUES
(4, '856325774562', 'U37570190', '2021-11-13 15:57:29', '2021-11-23 15:57:29', '2021-11-14 16:51:42', 'Return'),
(5, '856325774562', 'U37570190', '2021-11-14 17:04:13', '2021-11-24 17:04:13', '2021-11-14 17:05:47',  'Return'),
(6, '85478569856', 'U37570190', '2021-11-14 17:07:04', '2021-11-24 17:07:04', '2021-11-14 17:07:55', 'Return'),
(7, '753951852123', 'U52357788', '2021-11-17 11:03:04', '2021-11-27 11:03:04', '2021-11-17 11:05:29', 'Return'),
(8, '852369852123', 'U59564819', '2021-12-28 17:59:06', '2022-01-07 17:59:06', '2022-01-03 12:44:15', 'Return'),
(9, '852369753951', 'U59564819', '2021-12-28 18:03:30', '2022-01-07 18:03:30', '2022-01-03 12:43:28', 'Return');

-- --------------------------------------------------------

-- --------------------------------------------------------
-- --------------------------------------------------------

--
-- Table structure for table `lms_user`
--

CREATE TABLE `lms_user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `user_address` text COLLATE utf8_unicode_ci NOT NULL,
  `user_contact_no` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `user_email_address` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `user_password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_unique_id` varchar(30) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `lms_user`
--

INSERT INTO `lms_user` (`user_id`, `user_name`, `user_address`, `user_contact_no`, `user_email_address`, `user_password`,  `user_unique_id`) VALUES
(3, 'Paul Blake', '4016 Goldie Lane Cincinnati, OH 45202', '7539518520', 'paulblake@gmail.com', '$2y$10$YYkYS8kN8i/rWAc.iBbiUu3b.5w5xTxTg4GgCWMRhSQhvkZxVWblK',  'U39573214'),
(4, 'Aaron Lawler', '1616 Broadway Avenue Chattanooga, TN 37421', '8569856321', 'aaronlawler@live.com', '$2y$10$iRrikGm2NVZlFkPmabvSie2E6iLWideSiUPxQxEBgIT22BI6Fp3E2',  'U37570190'),
(5, 'Kathleen Forrest', '4545 Limer Street Greensboro, GA 30642', '85214796930', 'kathleen@hotmail.com', '$2y$10$VjWy324JIV1Ju9KmJVVgFuqrjjDb.irpSFo0g/ZPAg50l2eslq14a', 'U24567871'),
(6, 'Carol Maney', '2703 Deer Haven Drive Greenville, SC 29607', '8521479630', 'web-tutorial1@programmer.net', '$2y$10$8eJgxVxLTF1VS180XfDj1uc9nPd1gu8/it3PAt/zJuQMHNZwwBFNS', 'U52357788'),
(10, 'Kevin Peterson', '1889 Single Street Waltham, MA 02154', '8523698520', 'web-tutorial@programmer.net', '$2y$10$38OANawS7oT9xhpsOQrQwOU9PqGMSCWxWDCzn8fZuobXdmOT0mOqu', 'U59564819');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lms_admin`
--
ALTER TABLE `lms_admin`
  ADD PRIMARY KEY (`admin_id`);



--
-- Indexes for table `lms_book`
--
ALTER TABLE `lms_book`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `lms_issue_book`
--
ALTER TABLE `lms_issue_book`
  ADD PRIMARY KEY (`issue_book_id`);





--
-- Indexes for table `lms_user`
--
ALTER TABLE `lms_user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lms_admin`
--
ALTER TABLE `lms_admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


--
-- AUTO_INCREMENT for table `lms_book`
--
ALTER TABLE `lms_book`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `lms_issue_book`
--
ALTER TABLE `lms_issue_book`
  MODIFY `issue_book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;



--
-- AUTO_INCREMENT for table `lms_user`
--
ALTER TABLE `lms_user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

# /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
# /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
# /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
