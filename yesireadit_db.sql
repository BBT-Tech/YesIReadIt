-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 2017-10-14 21:02:13
-- 服务器版本： 5.5.56-log
-- PHP Version: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yesireadit_backup`
--

-- --------------------------------------------------------

--
-- 表的结构 `group_info`
--

CREATE TABLE `group_info` (
  `group_id` int(11) NOT NULL,
  `group_name` tinytext NOT NULL,
  `group_desc` text NOT NULL,
  `people_count` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `notice_group_relation`
--

CREATE TABLE `notice_group_relation` (
  `notice_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `bind_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `notice_info`
--

CREATE TABLE `notice_info` (
  `notice_id` int(11) NOT NULL,
  `pub_user_id` int(11) NOT NULL,
  `notice_title` tinytext NOT NULL,
  `notice_content` text NOT NULL,
  `notice_pub_time` datetime NOT NULL,
  `notice_end_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `session_info`
--

CREATE TABLE `session_info` (
  `session_id` int(10) UNSIGNED NOT NULL,
  `session_pass` char(32) NOT NULL,
  `session_info` text NOT NULL,
  `session_start_time` datetime NOT NULL,
  `session_last_time` datetime NOT NULL,
  `session_end_time` datetime NOT NULL,
  `session_status` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `user_group_privilege`
--

CREATE TABLE `user_group_privilege` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `bind_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `user_group_relation`
--

CREATE TABLE `user_group_relation` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `bind_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `user_info`
--

CREATE TABLE `user_info` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `user_name` char(64) NOT NULL,
  `user_email` char(64) NOT NULL,
  `user_avatar` text NOT NULL,
  `user_nickname` char(64) NOT NULL,
  `user_password` char(32) NOT NULL,
  `encuss_userid` int(10) UNSIGNED NOT NULL,
  `encuss_token_id` int(10) UNSIGNED NOT NULL,
  `encuss_token_key` char(32) NOT NULL,
  `reg_time` datetime NOT NULL,
  `reg_ip` char(16) NOT NULL DEFAULT '',
  `login_ip` char(16) NOT NULL DEFAULT '',
  `login_time` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- 表的结构 `user_notice_status`
--

CREATE TABLE `user_notice_status` (
  `user_id` int(11) NOT NULL,
  `notice_id` int(11) NOT NULL,
  `answer_info` tinytext NOT NULL,
  `answer_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `group_info`
--
ALTER TABLE `group_info`
  ADD PRIMARY KEY (`group_id`);
ALTER TABLE `group_info` ADD FULLTEXT KEY `group_name` (`group_name`);

--
-- Indexes for table `notice_group_relation`
--
ALTER TABLE `notice_group_relation`
  ADD PRIMARY KEY (`notice_id`,`group_id`) USING BTREE;

--
-- Indexes for table `notice_info`
--
ALTER TABLE `notice_info`
  ADD PRIMARY KEY (`notice_id`),
  ADD KEY `pub_user_id` (`pub_user_id`),
  ADD KEY `notice_pub_time` (`notice_pub_time`),
  ADD KEY `notice_end_time` (`notice_end_time`);

--
-- Indexes for table `session_info`
--
ALTER TABLE `session_info`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `user_group_privilege`
--
ALTER TABLE `user_group_privilege`
  ADD PRIMARY KEY (`user_id`,`group_id`);

--
-- Indexes for table `user_group_relation`
--
ALTER TABLE `user_group_relation`
  ADD PRIMARY KEY (`user_id`,`group_id`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_name_2` (`user_name`),
  ADD KEY `user_name` (`user_name`),
  ADD KEY `user_email` (`user_email`),
  ADD KEY `encuss_userid` (`encuss_userid`);

--
-- Indexes for table `user_notice_status`
--
ALTER TABLE `user_notice_status`
  ADD PRIMARY KEY (`user_id`,`notice_id`),
  ADD KEY `answer_time` (`answer_time`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `group_info`
--
ALTER TABLE `group_info`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `notice_info`
--
ALTER TABLE `notice_info`
  MODIFY `notice_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `session_info`
--
ALTER TABLE `session_info`
  MODIFY `session_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `user_info`
--
ALTER TABLE `user_info`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
