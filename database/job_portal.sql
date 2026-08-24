-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 15, 2026 at 07:15 AM
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
-- Database: `job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `applicant_id` int(11) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'pending',
  `visibility` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `applicant_id`, `applied_at`, `status`, `visibility`) VALUES
(6, 11, 1, '2026-07-04 23:56:33', 'accepted', 0),
(8, 14, 1, '2026-07-05 01:39:03', 'pending', 1),
(11, 7, 1, '2026-07-06 02:28:17', 'accepted', 1),
(13, 17, 27, '2026-07-14 08:23:29', 'accepted', 0),
(14, 16, 12, '2026-07-18 12:25:41', 'pending', 1),
(15, 17, 12, '2026-07-18 12:26:53', 'rejected', 0),
(17, 11, 12, '2026-07-18 12:51:58', 'hired', 1),
(18, 16, 1, '2026-07-23 13:03:54', 'pending', 1),
(19, 17, 41, '2026-07-24 11:12:38', 'rejected', 1),
(20, 7, 41, '2026-07-24 11:15:40', 'hired', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Web Development'),
(2, 'Graphic Design'),
(3, 'Mobile App Development'),
(4, 'Digital Marketing'),
(5, 'SEO'),
(6, 'Data Entry'),
(9, 'Freelancing'),
(11, 'Information Technology'),
(12, 'Marketing & Sales'),
(13, 'Design & Creative'),
(14, 'Finance & Accounting'),
(15, 'Human Resources'),
(16, 'Customer Support'),
(17, 'Engineering'),
(18, 'Education & Training');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `submitted_at`) VALUES
(1, 'Ali', 'alihussain@gmail.com', 'its about something ', 'hy', '2026-07-07 05:24:53');

-- --------------------------------------------------------

--
-- Table structure for table `interviews`
--

CREATE TABLE `interviews` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `scheduled_by` int(11) NOT NULL,
  `interview_type` enum('in-person','phone','video') DEFAULT 'video',
  `scheduled_at` datetime NOT NULL,
  `location_or_link` varchar(255) DEFAULT NULL,
  `notes` mediumtext DEFAULT NULL,
  `status` enum('scheduled','confirmed','reschedule_requested','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reschedule_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `interviews`
--

INSERT INTO `interviews` (`id`, `application_id`, `scheduled_by`, `interview_type`, `scheduled_at`, `location_or_link`, `notes`, `status`, `created_at`, `reschedule_reason`) VALUES
(7, 20, 3, 'video', '2026-07-27 20:00:00', 'https://www.zoom.com', 'Here is your interview scheduled for the Adobe Photoshop job on 27 July 2026 at 8:00 PM .', 'completed', '2026-07-25 13:51:04', NULL),
(9, 17, 3, 'in-person', '2026-07-30 08:00:00', 'At Aptech Malir Karachi', 'Be ready, Muhammad Umar, for the Mustafa Job Interview on 30 July 2026 at 8:00 PM', 'completed', '2026-07-25 14:11:10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `description` mediumtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'open',
  `job_type` enum('Full Time','Part Time','Internship','Contract','Remote','Hybrid') NOT NULL DEFAULT 'Full Time',
  `approval_status` varchar(20) DEFAULT 'pending',
  `required_skills` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `user_id`, `title`, `company`, `location`, `salary`, `description`, `created_at`, `category_id`, `status`, `job_type`, `approval_status`, `required_skills`) VALUES
(6, 3, 'Accountant', 'TLI  ', 'malir,saudabad ', '200000 ', '   I am Nadeem Ahmad who is really a hardworking honest and an ambitous man    ', '2026-06-05 13:43:44', 6, 'closed', 'Full Time', 'approved', NULL),
(7, 3, 'Adobe Photoshop', 'Abbas Company', 'karachi', '90000', 'i am graphic designer', '2026-06-05 13:44:58', 2, 'open', 'Full Time', 'approved', 'html,css, javascript,php'),
(14, 2, 'React Js ', 'Amazon Company ', 'London ', '550000', 'Yani Amazon ke liye React.js Developer ki post ka ek shaandar aur professional job description (JD) chahiye! CTC â‚¹5.5 Lakhs Per Annum (ÛŒØ§ Rs. 550,000) ke hisaab se yeh description structured, bada, aur appealing hona chahiye taake ache candidates attract hon.\r\n\r\nYahan ek complete, ready-to-use job description hai:\r\n\r\nJob Title: Associate React.js Developer\r\nCompany: Amazon\r\n\r\nLocation: Remote / Hybrid (As per company policy)\r\n\r\nJob Type: Full-time\r\n\r\nSalary: Rs. 550,000 per annum + Benefits\r\n\r\nAbout Amazon\r\nAt Amazon, our mission is to be Earthâ€™s most customer-centric company. To achieve this, we rely on exceptional talent to build innovative, scalable, and high-performance web applications. We are looking for a passionate and detail-oriented React.js Developer to join our dynamic engineering team and help shape the future of our digital platforms.\r\n\r\nJob Overview\r\nAs an Associate React.js Developer, you will be responsible for designing, developing, and implementing interactive user interfaces for our web applications. You will collaborate closely with UI/UX designers, product managers, and backend engineers to translate visual designs into clean, efficient, and maintainable code. Your goal will be to ensure a seamless user experience, optimizing frontend performance across a variety of web-capable devices and browsers.\r\n\r\nKey Responsibilities & Duties\r\nUI Development: Build reusable, high-quality components and front-end libraries using React.js for future use.\r\n\r\nCollaboration: Work closely with the design team to convert wireframes and high-fidelity mockups into functional, pixel-perfect web pages.\r\n\r\nPerformance Optimization: Optimize components for maximum performance, ensuring fast loading times across a vast array of web-capable devices and browsers.\r\n\r\nState Management: Implement efficient state management workflows using tools like Redux, Context API, or Zustand.\r\n\r\nAPI Integration: Connect front-end interfaces with RESTful APIs and modern backend architectures.\r\n\r\nCode Quality: Write clean, documented, and well-structured JavaScript/TypeScript code, participating actively in peer code reviews.\r\n\r\nTroubleshooting: Debug and resolve frontend issues, cross-browser compatibility bugs, and UI responsiveness errors.\r\n\r\nRequired Skills & Qualifications\r\nTechnical Core: Strong proficiency in JavaScript (ES6+), HTML5, CSS3, and modern frontend methodologies.\r\n\r\nReact Expertise: In-depth understanding of React.js and its core principles (Hooks, Lifecycle methods, Virtual DOM).\r\n\r\nStyling Tools: Experience with modern CSS frameworks like Tailwind CSS, Bootstrap, or Styled Components.\r\n\r\nState & Data: Familiarity with state management libraries (Redux/Context API) and handling asynchronous data fetching.\r\n\r\nBuild Tools: Experience with modern development tools like Vite, Webpack, Babel, and Git/GitHub version control.\r\n\r\nProblem Solving: Strong analytical thinking and debugging skills.\r\n\r\nCommunication: Excellent verbal and written communication skills to collaborate effectively in an agile team environment.\r\n\r\nWhat We Offer\r\nCompetitive Compensation: Annual salary package of Rs. 550,000.\r\n\r\nGrowth Opportunities: Access to world-class learning resources and mentorship from senior Amazon engineers.\r\n\r\nWork Environment: A collaborative, inclusive, and innovation-driven workplace culture.\r\n\r\nPerks: Comprehensive health insurance, paid time off (PTO), and employee discounts.\r\n\r\nHow to Apply\r\nIf you are ready to take your frontend skills to the next level with a global leader, we want to hear from you! Please click the \"Apply Now\" button and submit your updated resume along with a link to your GitHub profile or portfolio website.\r\n\r\n(Amazon is an Equal Opportunity Employer).', '2026-06-07 19:15:52', 4, 'open', 'Full Time', 'approved', NULL),
(15, 2, 'Laravel', 'hammad cmpany', 'karachi', '60000', 'Implementing roles in Laravel is best achieved using the community-standard package Spatie Laravel Permission, which manages many-to-many relationships, caching, and middleware', '2026-07-03 23:31:16', 2, 'open', 'Full Time', 'approved', NULL),
(16, 2, 'PHP', 'Hammad Company', 'Malir', '120000', 'In web development, the PHP role typically refers to back-end engineering, where PHP processes server-side logic, manages database operations, and handles user authentication. It also refers to implementing Role-Based Access Control (RBAC) in PHP apps to define what specific users (e.g., admins, editors, customers) are permitted to do.', '2026-07-03 23:34:37', 3, 'open', 'Full Time', 'approved', NULL),
(17, 3, 'Umar', 'Aptech', 'multan', '50000', 'i am software engineer', '2026-06-05 13:44:58', 5, 'open', 'Full Time', 'approved', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `message` mediumtext DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `message`, `link`, `is_read`, `created_at`) VALUES
(1, 12, 'application_status', 'Great news! Your application for \"MUSTAFA\" was accepted.', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-23 12:41:46'),
(2, 27, 'application_status', 'Great news! Your application for \"Umar\" was accepted.', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-23 12:44:27'),
(3, 12, 'application_status', 'Great news! Your application for \"Finance & Banking\" was accepted.', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-23 12:58:04'),
(4, 3, 'new_applicant', 'New applicant for \"Umar\"', '/job-portal/employer/employer-applications.php', 1, '2026-07-24 11:12:38'),
(5, 3, 'new_applicant', 'New applicant for \"Adobe Photoshop\"', '/job-portal/employer/employer-applications.php', 1, '2026-07-24 11:15:40'),
(6, 41, 'application_status', 'Your application for \"Umar\" was not selected this time.', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-24 11:16:40'),
(7, 41, 'application_status', 'Great news! Your application for \"Adobe Photoshop\" was accepted.', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-24 11:16:48'),
(8, 41, 'interview_scheduled', 'Interview scheduled for \"Adobe Photoshop\" on 27 Jul 2026, 05:00 PM', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-25 13:30:14'),
(9, 41, 'interview_scheduled', 'Interview scheduled for \"Adobe Photoshop\" on 27 Jul 2026, 05:00 PM', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-25 13:32:46'),
(10, 41, 'interview_scheduled', 'Interview scheduled for \"Adobe Photoshop\" on 27 Jul 2026, 05:00 PM', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-25 13:33:19'),
(11, 41, 'interview_scheduled', 'Interview scheduled for \"Adobe Photoshop\" on 27 Jul 2026, 05:00 PM', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-25 13:33:48'),
(12, 41, 'interview_scheduled', 'Interview scheduled for \"Adobe Photoshop\" on 27 Jul 2026, 08:00 PM', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-25 13:51:04'),
(13, 12, 'interview_scheduled', 'Interview scheduled for \"MUSTAFA\" on 30 Jul 2026, 06:00 PM', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-25 13:58:31'),
(14, 12, 'interview_scheduled', 'Interview scheduled for \"MUSTAFA\" on 30 Jul 2026, 08:00 AM', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-25 14:11:10'),
(15, 25, 'new_applicant', 'New applicant for \"UI / UX Designing\"', '/job-portal/employer/employer-applications.php', 1, '2026-07-26 10:07:39'),
(16, 12, 'application_status', 'Great news! Your application for \"UI / UX Designing\" was accepted.', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-26 10:09:22'),
(17, 12, 'interview_scheduled', 'Interview scheduled for \"UI / UX Designing\" on 26 Jul 2026, 05:10 PM', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-26 10:16:36'),
(18, 12, 'interview_scheduled', 'Interview scheduled for \"UI / UX Designing\" on 26 Jul 2026, 05:10 PM', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-26 10:21:40'),
(19, 25, 'interview_reschedule_requested', 'Candidate requested a reschedule for \"UI / UX Designing\"', '/job-portal/employer/employer-applications.php', 1, '2026-07-28 11:37:46'),
(20, 12, 'interview_scheduled', 'Interview scheduled for \"UI / UX Designing\" on 26 Jul 2026, 09:10 PM', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-28 11:39:12'),
(21, 25, 'interview_confirmed', 'Candidate confirmed the interview for \"UI / UX Designing\"', '/job-portal/employer/employer-applications.php', 1, '2026-07-28 11:39:22'),
(22, 12, 'application_status', 'Update on your application for \"UI / UX Designing\"', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-28 11:42:04'),
(23, 12, 'application_status', 'ðŸŽ‰ Congratulations! You\'ve been hired for \"MUSTAFA\"', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-28 11:56:34'),
(24, 12, 'interview_scheduled', 'Interview scheduled for \"Finance & Banking\" on 29 Jul 2026, 07:00 PM', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-29 10:35:19'),
(25, 27, 'interview_reschedule_requested', 'Candidate requested a reschedule for \"Finance & Banking\"', '/job-portal/employer/employer-applications.php', 1, '2026-07-29 10:55:07'),
(26, 12, 'interview_scheduled', 'Interview scheduled for \"Finance & Banking\" on 30 Jul 2026, 09:00 PM', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-29 10:55:58'),
(27, 27, 'interview_confirmed', 'Candidate confirmed the interview for \"Finance & Banking\"', '/job-portal/employer/employer-applications.php', 1, '2026-07-29 10:56:13'),
(28, 12, 'application_status', 'ðŸŽ‰ Congratulations! You\'ve been hired for \"Finance & Banking\"', '/job-portal/applicant/applied-jobs.php', 1, '2026-07-29 10:56:54'),
(29, 12, 'job_pending_review', 'New job \"re\" needs your approval', '/job-portal/admin/manage-jobs.php?status=pending', 1, '2026-07-29 11:54:25'),
(30, 12, 'job_pending_review', 'New job \"re\" needs your approval', '/job-portal/admin/manage-jobs.php?status=pending', 1, '2026-07-29 11:54:31'),
(31, 27, 'job_rejected', 'Your job \"re\" was not approved', '/job-portal/employer/add-manage-jobs.php', 1, '2026-07-29 11:54:44'),
(32, 27, 'job_rejected', 'Your job \"re\" was not approved', '/job-portal/employer/add-manage-jobs.php', 1, '2026-07-29 11:54:57'),
(33, 41, 'application_status', 'ðŸŽ‰ Congratulations! You\'ve been hired for \"Adobe Photoshop\"', '/job-portal/applicant/applied-jobs.php', 0, '2026-08-02 11:08:43');

-- --------------------------------------------------------

--
-- Table structure for table `saved_jobs`
--

CREATE TABLE `saved_jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `saved_jobs`
--

INSERT INTO `saved_jobs` (`id`, `user_id`, `job_id`) VALUES
(8, 1, 15),
(9, 1, 6),
(10, 2, 17),
(14, 1, 11),
(15, 1, 14),
(19, 1, 16),
(21, 1, 17),
(22, 41, 17),
(23, 41, 7);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT 'user.png',
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(55) NOT NULL,
  `bio` mediumtext DEFAULT NULL,
  `skills` mediumtext DEFAULT NULL,
  `experience` varchar(100) DEFAULT NULL,
  `cv_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `company_name` varchar(150) DEFAULT NULL,
  `company_logo` varchar(255) DEFAULT NULL,
  `company_website` varchar(255) DEFAULT NULL,
  `company_email` varchar(150) DEFAULT NULL,
  `company_phone` varchar(30) DEFAULT NULL,
  `company_industry` varchar(100) DEFAULT NULL,
  `company_size` varchar(50) DEFAULT NULL,
  `company_founded_year` year(4) DEFAULT NULL,
  `company_city` varchar(100) DEFAULT NULL,
  `company_address` mediumtext DEFAULT NULL,
  `company_about` mediumtext DEFAULT NULL,
  `company_linkedin` varchar(255) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `profile_image`, `email`, `password`, `role`, `bio`, `skills`, `experience`, `cv_file`, `created_at`, `company_name`, `company_logo`, `company_website`, `company_email`, `company_phone`, `company_industry`, `company_size`, `company_founded_year`, `company_city`, `company_address`, `company_about`, `company_linkedin`, `reset_token`, `reset_token_expiry`) VALUES
(1, 'Muhammad ali ', '1783395286Screenshot (1).png', 'alihussain@gmail.com', '$2y$10$wlo/krosfRctTQEzCHxene6XRdCTreCTPaL9yR9SFFiQDr12aI1J.', 'applicant', 'i am muhammad ali khan  !', 'html,css,javascript', '2 years', '1783395446Application_Class11_Books.docx', '2026-08-04 04:12:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Hammad Khalid', '1783129551pngtree-man-employed-smile-png-image_6594014.png', 'hammad@gmail.com', '$2y$10$uxXpFVohQ.Ebc826B0T6f.wkzwa0AdLEV/zrdue4DeEBPetLdN1.a', 'employer', 'I am Hammad Khalid', 'I have learnt react laravel php javascript css html ', '3+ years', '1783129551com.android.vending_Screenshot_2026.04.15_16.49.24.jpeg', '2026-07-15 09:17:02', 'Sara Company', '', 'https://job-portal.infinityfreeapp.com', 'mu160446@gmail.com', '03345494892', 'high five', '250+ employers', '2007', 'karachi', 'S2/520 Saudabad Malir Karachi', 'This Is Sara Company', 'https://pk.linkedin.com', NULL, NULL),
(3, 'Syed Abbas Ansari', '1783300971Screenshot (95).png', 'umar@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'employer', 'i am abbas ansari', 'Adboe Photoshop', '2 years', '1783300971Screenshot (96).png', '2026-07-25 12:50:57', 'Sara Company', 'company_3_1783939936.png', 'https://job-portal.infinityfreeapp.com', 'mu160446@gmail.com', '03345494892', 'high five', '250+ employers', '2016', 'karachi', 'S2/520 Saudabad Malir Karachi', 'Sara Company is good', 'https://pk.linkedin.com', NULL, NULL),
(12, 'Muhammad Umar', 'user.png', 'mu10164838@gmail.com', '$2y$10$FWLUpBQabgPRKrp7cTZVRu469TULHhAY8VxrFaHQnBUDObrLahAsu', 'admin', NULL, NULL, NULL, NULL, '2026-07-07 04:02:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 'Hadi', 'user.png', 'tornadoshot28@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'employer', NULL, NULL, NULL, NULL, '2026-07-26 10:03:30', 'Hadi Company', '', '', '', '', '', '', '0000', '', '', 'Here is Hadi Company About', '', '427313180ecbe38707269363f9dcf44b664925ae9e94d6558a1bdff095d37574', '2026-07-16 15:37:43'),
(27, 'Ahmed', 'user.png', 'ahmed@gmail.com', '$2y$10$1FJnCpXn97lD7lB3F3OsCOduxNB6JOAwlBtAsFF6d2cJV8wrZDjye', 'employer', NULL, NULL, NULL, NULL, '2026-07-12 08:28:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 'asad', 'user.png', 'asad@gmail.com', '$2y$10$T/4jPltPECRiAm2jghtNZeAAHuL/xTasjfoeGw0hbv7KG/rQe.ajm', 'employer', NULL, NULL, NULL, NULL, '2026-07-12 08:54:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'sir anique sheikh', 'profile291784110630.jpg', 'anique@gmail.com', '$2y$10$3XmyLfSh9y7ktooQqxjQX.mxjMdsjrIc34t7Pb.LU0hTH3FhHt5im', 'applicant', 'Hy i am sir anique sheikh', 'Full Stack Developer And Also a freelancer too', 'Fresher', 'cv291784107112.pdf', '2026-08-14 15:07:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 'umar', 'user.png', 'mu160446@gmail.com', '$2y$10$.XAJH/ae.YXQc2ydl/nrQeJ87fUyVfvMs4Csg2UtRA1w3bTIMmQq2', 'employer', NULL, NULL, NULL, NULL, '2026-07-17 14:22:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'Ahmed Raza', 'user.png', 'ahmed@techventure.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'employer', NULL, NULL, NULL, NULL, '2026-07-24 10:59:45', 'TechVenture Labs', NULL, 'https://techventurelabs.com', 'hr@techventurelabs.com', '021-111222333', NULL, '51-200', '2018', NULL, 'Clifton, Karachi, Pakistan', 'We build scalable software products for startups and enterprises across Pakistan.', 'https://linkedin.com/company/techventurelabs', NULL, NULL),
(32, 'Sara Khan', 'user.png', 'sara@brightmedia.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'employer', NULL, NULL, NULL, NULL, '2026-07-24 10:59:45', 'Bright Media Agency', NULL, 'https://brightmedia.pk', 'contact@brightmedia.pk', '042-333444555', NULL, '11-50', '2020', NULL, 'Gulberg, Lahore, Pakistan', 'A full-service digital marketing agency helping brands grow online.', NULL, NULL, NULL),
(33, 'Bilal Ahmed', 'user.png', 'bilal@financepro.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'employer', NULL, NULL, NULL, NULL, '2026-07-24 10:59:45', 'FinancePro Consultants', NULL, NULL, 'info@financepro.com', '051-555666777', NULL, '11-50', '2015', NULL, 'F-7, Islamabad, Pakistan', 'Financial advisory and accounting services for SMEs.', NULL, NULL, NULL),
(34, 'Ayesha Malik', 'user.png', 'ayesha@designhive.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'employer', NULL, NULL, NULL, NULL, '2026-07-24 10:59:45', 'Design Hive Studio', NULL, 'https://designhive.studio', NULL, NULL, NULL, '1-10', '2021', NULL, 'Karachi, Pakistan', 'Creative design studio specializing in branding and UI/UX.', NULL, NULL, NULL),
(35, 'Usman Tariq', 'user.png', 'usman@buildright.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'employer', NULL, NULL, NULL, NULL, '2026-07-24 10:59:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 'Hina Sheikh', 'user.png', 'hina@peopleplus.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'employer', NULL, NULL, NULL, NULL, '2026-07-24 10:59:45', 'PeoplePlus HR Solutions', NULL, 'https://peopleplus.pk', 'careers@peopleplus.pk', '021-777888999', NULL, '11-50', '2017', NULL, 'DHA, Karachi, Pakistan', 'HR outsourcing and recruitment consultancy.', 'https://linkedin.com/company/peopleplushr', NULL, NULL),
(37, 'Fahad Iqbal', 'user.png', 'fahad@edulearn.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'employer', NULL, NULL, NULL, NULL, '2026-07-24 10:59:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 'Zainab Qureshi', 'user.png', 'zainab@supportdesk.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'employer', NULL, NULL, NULL, NULL, '2026-07-24 10:59:45', 'SupportDesk Global', NULL, 'https://supportdeskglobal.com', 'jobs@supportdeskglobal.com', '042-999000111', NULL, '201-500', '2016', NULL, 'Model Town, Lahore, Pakistan', 'Outsourced customer support and call center services.', NULL, NULL, NULL),
(39, 'Waqas Anjum', 'user.png', 'waqas@buildwell.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'employer', NULL, NULL, NULL, NULL, '2026-07-24 10:59:45', 'BuildWell Engineering', NULL, NULL, NULL, '021-222333444', NULL, '51-200', '2012', NULL, 'Korangi, Karachi, Pakistan', 'Civil and structural engineering consultancy for commercial projects.', NULL, NULL, NULL),
(40, 'Mariam Yousuf', 'user.png', 'mariam@nexttech.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'employer', NULL, NULL, NULL, NULL, '2026-07-24 10:59:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 'Ali Hassan', NULL, 'ali.hassan@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', 'Backend developer passionate about building clean, scalable APIs.', 'PHP, MySQL, JavaScript', '2 years', 'sample_cv_1.pdf', '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 'Fatima Noor', NULL, 'fatima.noor@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', 'Creative designer with a keen eye for detail and brand consistency.', 'Graphic Design, Figma, Adobe XD', '1 years', 'sample_cv_2.pdf', '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 'Hamza Sheikh', NULL, 'hamza.sheikh@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', NULL, NULL, NULL, NULL, '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 'Ayesha Siddiqui', NULL, 'ayesha.s@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', 'Marketing specialist focused on growth strategy and SEO.', 'Digital Marketing, SEO, Content Writing', '3 years', 'sample_cv_3.pdf', '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 'Omar Farooq', NULL, 'omar.farooq@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', 'Recent CS graduate eager to start a career in full-stack development.', 'React, Node.js, MongoDB', 'Fresher', 'sample_cv_4.pdf', '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 'Sana Malik', NULL, 'sana.malik@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', NULL, NULL, NULL, NULL, '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 'Bilal Aslam', NULL, 'bilal.aslam@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', 'Detail-oriented accountant with experience in SME bookkeeping.', 'Accounting, Excel, QuickBooks', '2 years', 'sample_cv_5.pdf', '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 'Mehwish Tariq', NULL, 'mehwish.tariq@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', 'HR professional with a focus on talent acquisition.', 'Recruitment, HR Policies, Onboarding', '3+ years', 'sample_cv_6.pdf', '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 'Zeeshan Baig', NULL, 'zeeshan.baig@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', NULL, NULL, NULL, NULL, '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 'Nida Kamal', NULL, 'nida.kamal@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', 'Friendly and patient customer support agent.', 'Customer Service, Communication, CRM Tools', '1 years', 'sample_cv_7.pdf', '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 'Asad Mahmood', NULL, 'asad.mahmood@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', 'Civil engineer with hands-on site supervision experience.', 'AutoCAD, Structural Analysis, Civil Design', '3+ years', 'sample_cv_8.pdf', '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 'Rabia Yasin', NULL, 'rabia.yasin@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', NULL, NULL, NULL, NULL, '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 'Danish Iqbal', NULL, 'danish.iqbal@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', 'Backend engineer who loves clean architecture and testing.', 'Python, Django, PostgreSQL', '2 years', 'sample_cv_9.pdf', '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 'Komal Yousaf', NULL, 'komal.yousaf@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', 'Passionate educator focused on student engagement.', 'Teaching, Curriculum Design, Public Speaking', '3+ years', 'sample_cv_10.pdf', '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 'Talha Rehman', NULL, 'talha.rehman@gmail.com', '$2b$12$qIQmNX7icfgoadNomOGvd.DLq6X1Xp4SknFAbOZdJKsHTJhZe.3yq', 'applicant', NULL, NULL, NULL, NULL, '2026-07-24 11:00:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `interviews`
--
ALTER TABLE `interviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_application` (`application_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `interviews`
--
ALTER TABLE `interviews`
  ADD CONSTRAINT `interviews_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
