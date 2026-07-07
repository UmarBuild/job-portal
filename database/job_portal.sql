-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2026 at 07:34 AM
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `job_id`, `applicant_id`, `applied_at`, `status`, `visibility`) VALUES
(6, 11, 1, '2026-07-04 23:56:33', 'accepted', 0),
(8, 14, 1, '2026-07-05 01:39:03', 'pending', 1),
(11, 7, 1, '2026-07-06 02:28:17', 'accepted', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
(9, 'Freelancing');

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
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `salary` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `user_id`, `title`, `company`, `location`, `salary`, `description`, `created_at`, `category_id`, `status`) VALUES
(6, 3, 'Accountant', 'TLI  ', 'malir,saudabad ', '200000 ', '   I am Nadeem Ahmad who is really a hardworking honest and an ambitous man    ', '2026-06-05 13:43:44', 6, 'closed'),
(7, 3, 'Adobe Photoshop', 'Abbas Company        ', 'multan     ', '90000   ', '            i am graphic designer         ', '2026-06-05 13:44:58', 2, 'closed'),
(11, 3, 'MUSTAFA', 'AKBAR SAHAB COMPANY', 'SINDH KARACHI MALIR SAUDABAD', '600000', 'I AM MUSTAKE FROM SINDH', '2026-06-05 14:13:47', 1, 'open'),
(14, 2, 'React Js ', 'Amazon Company ', 'London ', '550000', 'Yani Amazon ke liye React.js Developer ki post ka ek shaandar aur professional job description (JD) chahiye! CTC â‚¹5.5 Lakhs Per Annum (ÛŒØ§ Rs. 550,000) ke hisaab se yeh description structured, bada, aur appealing hona chahiye taake ache candidates attract hon.\r\n\r\nYahan ek complete, ready-to-use job description hai:\r\n\r\nJob Title: Associate React.js Developer\r\nCompany: Amazon\r\n\r\nLocation: Remote / Hybrid (As per company policy)\r\n\r\nJob Type: Full-time\r\n\r\nSalary: Rs. 550,000 per annum + Benefits\r\n\r\nAbout Amazon\r\nAt Amazon, our mission is to be Earthâ€™s most customer-centric company. To achieve this, we rely on exceptional talent to build innovative, scalable, and high-performance web applications. We are looking for a passionate and detail-oriented React.js Developer to join our dynamic engineering team and help shape the future of our digital platforms.\r\n\r\nJob Overview\r\nAs an Associate React.js Developer, you will be responsible for designing, developing, and implementing interactive user interfaces for our web applications. You will collaborate closely with UI/UX designers, product managers, and backend engineers to translate visual designs into clean, efficient, and maintainable code. Your goal will be to ensure a seamless user experience, optimizing frontend performance across a variety of web-capable devices and browsers.\r\n\r\nKey Responsibilities & Duties\r\nUI Development: Build reusable, high-quality components and front-end libraries using React.js for future use.\r\n\r\nCollaboration: Work closely with the design team to convert wireframes and high-fidelity mockups into functional, pixel-perfect web pages.\r\n\r\nPerformance Optimization: Optimize components for maximum performance, ensuring fast loading times across a vast array of web-capable devices and browsers.\r\n\r\nState Management: Implement efficient state management workflows using tools like Redux, Context API, or Zustand.\r\n\r\nAPI Integration: Connect front-end interfaces with RESTful APIs and modern backend architectures.\r\n\r\nCode Quality: Write clean, documented, and well-structured JavaScript/TypeScript code, participating actively in peer code reviews.\r\n\r\nTroubleshooting: Debug and resolve frontend issues, cross-browser compatibility bugs, and UI responsiveness errors.\r\n\r\nRequired Skills & Qualifications\r\nTechnical Core: Strong proficiency in JavaScript (ES6+), HTML5, CSS3, and modern frontend methodologies.\r\n\r\nReact Expertise: In-depth understanding of React.js and its core principles (Hooks, Lifecycle methods, Virtual DOM).\r\n\r\nStyling Tools: Experience with modern CSS frameworks like Tailwind CSS, Bootstrap, or Styled Components.\r\n\r\nState & Data: Familiarity with state management libraries (Redux/Context API) and handling asynchronous data fetching.\r\n\r\nBuild Tools: Experience with modern development tools like Vite, Webpack, Babel, and Git/GitHub version control.\r\n\r\nProblem Solving: Strong analytical thinking and debugging skills.\r\n\r\nCommunication: Excellent verbal and written communication skills to collaborate effectively in an agile team environment.\r\n\r\nWhat We Offer\r\nCompetitive Compensation: Annual salary package of Rs. 550,000.\r\n\r\nGrowth Opportunities: Access to world-class learning resources and mentorship from senior Amazon engineers.\r\n\r\nWork Environment: A collaborative, inclusive, and innovation-driven workplace culture.\r\n\r\nPerks: Comprehensive health insurance, paid time off (PTO), and employee discounts.\r\n\r\nHow to Apply\r\nIf you are ready to take your frontend skills to the next level with a global leader, we want to hear from you! Please click the \"Apply Now\" button and submit your updated resume along with a link to your GitHub profile or portfolio website.\r\n\r\n(Amazon is an Equal Opportunity Employer).', '2026-06-07 19:15:52', 4, 'open'),
(15, 2, 'Laravel', 'hammad cmpany', 'karachi', '60000', 'Implementing roles in Laravel is best achieved using the community-standard package Spatie Laravel Permission, which manages many-to-many relationships, caching, and middleware', '2026-07-03 23:31:16', 2, 'open'),
(16, 2, 'PHP', 'Hammad Company', 'Malir', '120000', 'In web development, the PHP role typically refers to back-end engineering, where PHP processes server-side logic, manages database operations, and handles user authentication. It also refers to implementing Role-Based Access Control (RBAC) in PHP apps to define what specific users (e.g., admins, editors, customers) are permitted to do.', '2026-07-03 23:34:37', 3, 'open'),
(17, 3, 'Umar', 'Aptech', 'multan', '50000', 'i am software engineer', '2026-06-05 13:44:58', 5, 'open');

-- --------------------------------------------------------

--
-- Table structure for table `saved_jobs`
--

CREATE TABLE `saved_jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `saved_jobs`
--

INSERT INTO `saved_jobs` (`id`, `user_id`, `job_id`) VALUES
(8, 1, 15),
(9, 1, 6),
(10, 2, 17),
(12, 1, 17),
(13, 1, 7),
(14, 1, 11);

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
  `bio` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `experience` varchar(100) DEFAULT NULL,
  `cv_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `profile_image`, `email`, `password`, `role`, `bio`, `skills`, `experience`, `cv_file`, `created_at`) VALUES
(1, 'Muhammad ali ', '1783395286Screenshot (1).png', 'alihussain@gmail.com', '$2y$10$JQqXfm4TPBPFuM0j1URHvuk6FvnqbJENnAHeyqhgnOz9Yp9BqNAv.', 'applicant', 'i am muhammad ali khan  !', 'Full Stack Developer And Also a freelancer too !', '2 years', '1783395446Application_Class11_Books.docx', '2026-07-07 04:43:22'),
(2, 'Hammad Khalid', '1783129551pngtree-man-employed-smile-png-image_6594014.png', 'hammad@gmail.com', '$2y$10$uxXpFVohQ.Ebc826B0T6f.wkzwa0AdLEV/zrdue4DeEBPetLdN1.a', 'employer', 'I am Hammad Khalid', 'I have learnt react laravel php javascript css html ', '3+ years', '1783129551com.android.vending_Screenshot_2026.04.15_16.49.24.jpeg', '2026-07-04 01:45:51'),
(3, 'Syed Abbas Ansari', '1783300971Screenshot (95).png', 'umar@gmail.com', '$2y$10$MD9svgK9fmxAcsiuhxx.CepCuJJJaLy3wZIh.B93K.JPcuhjN28T.', 'employer', 'i am abbas ansari', 'Adboe Photoshop', '2 years', '1783300971Screenshot (96).png', '2026-07-06 01:22:51'),
(12, 'Muhammad Umar', 'user.png', 'mu10164838@gmail.com', '$2y$10$FWLUpBQabgPRKrp7cTZVRu469TULHhAY8VxrFaHQnBUDObrLahAsu', 'admin', NULL, NULL, NULL, NULL, '2026-07-07 04:02:26'),
(25, 'Hadi', 'user.png', 'tornadoshot28@gmail.com', '$2y$10$AS4/Fn0/agLCxaVrgIj/XuCEF9zW8oVAqPRKbqmgJSNHT3bdxWzpy', 'employer', NULL, NULL, NULL, NULL, '2026-07-07 02:15:46');

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
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
