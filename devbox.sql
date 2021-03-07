-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 07, 2021 at 06:07 PM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `devbox`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `description` mediumtext DEFAULT NULL,
  `visibility` tinyint(4) NOT NULL DEFAULT 1,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `user_id`, `name`, `description`, `visibility`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Git Profile', NULL, 1, 1, '2021-03-05 16:46:05', '2021-03-05 16:46:05'),
(2, 1, 'Github Repo Link', NULL, 1, 1, '2021-03-05 16:46:22', '2021-03-05 16:46:22'),
(3, 1, 'Laravel Queries', NULL, 1, 1, '2021-03-05 16:46:31', '2021-03-05 16:46:31'),
(4, 1, 'Laravel Artisan Commands', NULL, 1, 1, '2021-03-05 16:46:52', '2021-03-05 16:46:52'),
(5, 1, 'Vue Js', NULL, 1, 1, '2021-03-05 16:46:57', '2021-03-05 16:46:57'),
(6, 1, 'MySql Queries', NULL, 1, 1, '2021-03-05 16:47:09', '2021-03-05 16:47:09'),
(7, 1, 'Javascript', NULL, 1, 1, '2021-03-05 16:47:25', '2021-03-05 16:47:25'),
(8, 1, 'jQuery', NULL, 1, 1, '2021-03-05 16:47:38', '2021-03-05 16:47:38'),
(9, 1, 'Youtube Videos', NULL, 1, 1, '2021-03-05 16:48:11', '2021-03-05 16:48:11'),
(10, 1, 'Youtube Profile', NULL, 1, 1, '2021-03-05 16:48:20', '2021-03-05 16:48:20'),
(11, 1, 'CRM - R&D', NULL, 1, 1, '2021-03-05 16:48:53', '2021-03-05 16:48:53'),
(12, 1, 'Cpanel', NULL, 1, 1, '2021-03-05 16:49:26', '2021-03-05 16:49:26');

-- --------------------------------------------------------

--
-- Table structure for table `postbox`
--

DROP TABLE IF EXISTS `postbox`;
CREATE TABLE IF NOT EXISTS `postbox` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `keywords` mediumtext DEFAULT NULL,
  `box_content` longtext DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `visibility` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `postbox`
--

INSERT INTO `postbox` (`id`, `category_id`, `user_id`, `title`, `keywords`, `box_content`, `status`, `visibility`, `created_at`, `updated_at`) VALUES
(3, 7, 1, 'setTimeOut in Javascript', 'settimeout', '&lt;p&gt;&lt;span style=&quot;color: rgb(0, 0, 0);&quot;&gt;setTimeout(&lt;/span&gt;&lt;span style=&quot;color: mediumblue;&quot;&gt;function&lt;/span&gt;&lt;span style=&quot;color: rgb(0, 0, 0);&quot;&gt;(){ alert(&lt;/span&gt;&lt;span style=&quot;color: brown;&quot;&gt;&quot;Hello&quot;&lt;/span&gt;&lt;span style=&quot;color: rgb(0, 0, 0);&quot;&gt;); },&amp;nbsp;&lt;/span&gt;&lt;span style=&quot;color: red;&quot;&gt;3000&lt;/span&gt;&lt;span style=&quot;color: rgb(0, 0, 0);&quot;&gt;);&lt;/span&gt;&lt;/p&gt;', 1, 1, '2021-03-05 19:25:16', '2021-03-05 19:25:16'),
(11, 10, 1, 'Bitfumes', NULL, '&lt;p&gt;&lt;a href=&quot;https://www.youtube.com/channel/UC_hG9fglfmShkwex1KVydHA&quot; rel=&quot;noopener noreferrer&quot; target=&quot;_blank&quot;&gt;https://www.youtube.com/channel/UC_hG9fglfmShkwex1KVydHA&lt;/a&gt;&lt;/p&gt;', 1, 1, '2021-03-07 05:18:45', '2021-03-07 05:19:06'),
(5, 5, 1, 'Vue Browser Back', 'Browser back, vue-router', '&lt;pre class=&quot;ql-syntax&quot; spellcheck=&quot;false&quot;&gt;goBack() {\n      window.history.length &amp;gt; 1 ? this.$router.go(-1) : this.$router.push(&#039;/&#039;)\n}\n&lt;/pre&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;&lt;pre class=&quot;ql-syntax&quot; spellcheck=&quot;false&quot;&gt;// go forward by one record, the same as history.forward()\nrouter.go(1)\n\n// go back by one record, the same as history.back()\nrouter.go(-1)\n\n// go forward by 3 records\nrouter.go(3)\n\n// fails silently if there aren&#039;t that many records.\nrouter.go(-100)\nrouter.go(100)\n&lt;/pre&gt;&lt;p&gt;&lt;br&gt;&lt;/p&gt;', 1, 1, '2021-03-05 20:03:40', '2021-03-06 10:24:55'),
(12, 10, 1, 'Corer\'s Tape', NULL, '&lt;p&gt;&lt;a href=&quot;https://www.youtube.com/channel/UCQI-Ym2rLZx52vEoqlPQMdg&quot; rel=&quot;noopener noreferrer&quot; target=&quot;_blank&quot;&gt;https://www.youtube.com/channel/UCQI-Ym2rLZx52vEoqlPQMdg&lt;/a&gt;&lt;/p&gt;', 1, 1, '2021-03-07 05:19:54', '2021-03-07 05:20:11');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
