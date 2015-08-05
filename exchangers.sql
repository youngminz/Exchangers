-- phpMyAdmin SQL Dump
-- version 4.4.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 15-08-05 12:44
-- 서버 버전: 5.5.44-MariaDB-1ubuntu0.14.04.1
-- PHP 버전: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 데이터베이스: `exchangers`
--
CREATE DATABASE IF NOT EXISTS `exchangers` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `exchangers`;

-- --------------------------------------------------------

--
-- 테이블 구조 `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `category_code` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `exchange_article`
--

DROP TABLE IF EXISTS `exchange_article`;
CREATE TABLE IF NOT EXISTS `exchange_article` (
  `ID` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `board_title` varchar(1000) DEFAULT NULL,
  `category` varchar(30) DEFAULT NULL,
  `lang_from` varchar(5) DEFAULT NULL,
  `lang_to` varchar(5) DEFAULT NULL,
  `contents` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `board_hit` bigint(20) NOT NULL DEFAULT '0',
  `vote_up` int(11) NOT NULL DEFAULT '0',
  `vote_down` int(11) NOT NULL DEFAULT '0',
  `author` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `exchange_comment`
--

DROP TABLE IF EXISTS `exchange_comment`;
CREATE TABLE IF NOT EXISTS `exchange_comment` (
  `ID` bigint(20) unsigned NOT NULL,
  `content` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` bigint(20) unsigned NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `parent_article` bigint(20) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `language`
--

DROP TABLE IF EXISTS `language`;
CREATE TABLE IF NOT EXISTS `language` (
  `lang_code` varchar(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `ui_language`
--

DROP TABLE IF EXISTS `ui_language`;
CREATE TABLE IF NOT EXISTS `ui_language` (
  `lang_code` varchar(5) NOT NULL,
  `english` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 테이블 구조 `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `ID` bigint(20) unsigned NOT NULL,
  `user_id` varchar(100) NOT NULL,
  `user_pass` varchar(128) NOT NULL,
  `user_nickname` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_point` int(11) NOT NULL DEFAULT '0',
  `user_reputation` int(11) NOT NULL DEFAULT '0',
  `enabled` tinyint(4) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 덤프된 테이블의 인덱스
--

--
-- 테이블의 인덱스 `category`
--
ALTER TABLE `category`
  ADD UNIQUE KEY `category_code` (`category_code`);

--
-- 테이블의 인덱스 `exchange_article`
--
ALTER TABLE `exchange_article`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `author` (`author`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `lang_from` (`lang_from`),
  ADD KEY `lang_to` (`lang_to`),
  ADD KEY `category` (`category`);

--
-- 테이블의 인덱스 `exchange_comment`
--
ALTER TABLE `exchange_comment`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `parent_article` (`parent_article`),
  ADD KEY `author` (`author`);

--
-- 테이블의 인덱스 `language`
--
ALTER TABLE `language`
  ADD UNIQUE KEY `lang_code` (`lang_code`);

--
-- 테이블의 인덱스 `ui_language`
--
ALTER TABLE `ui_language`
  ADD UNIQUE KEY `lang_code` (`lang_code`);

--
-- 테이블의 인덱스 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- 덤프된 테이블의 AUTO_INCREMENT
--

--
-- 테이블의 AUTO_INCREMENT `exchange_article`
--
ALTER TABLE `exchange_article`
  MODIFY `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `exchange_comment`
--
ALTER TABLE `exchange_comment`
  MODIFY `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- 테이블의 AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- 덤프된 테이블의 제약사항
--

--
-- 테이블의 제약사항 `exchange_article`
--
ALTER TABLE `exchange_article`
  ADD CONSTRAINT `exchange_article_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exchange_article_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `exchange_article` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exchange_article_ibfk_3` FOREIGN KEY (`lang_from`) REFERENCES `language` (`lang_code`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exchange_article_ibfk_4` FOREIGN KEY (`lang_to`) REFERENCES `language` (`lang_code`) ON UPDATE CASCADE;

--
-- 테이블의 제약사항 `exchange_comment`
--
ALTER TABLE `exchange_comment`
  ADD CONSTRAINT `exchange_comment_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`ID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `exchange_comment_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `exchange_comment` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exchange_comment_ibfk_3` FOREIGN KEY (`parent_article`) REFERENCES `exchange_article` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
