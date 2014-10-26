CREATE TABLE `users` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` varchar(100) NOT NULL,
  `user_pass` varchar(128) NOT NULL,
  `user_nickname` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_regdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_point` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `exchange_article` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `board_title` varchar(1000) NOT NULL,
  `category` varchar(30) NOT NULL,
  `lang_from` varchar(5) DEFAULT NULL,
  `lang_to` varchar(5) DEFAULT NULL,
  `contents` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `board_hit` bigint(20) NOT NULL DEFAULT '0',
  `vote_up` int(11) NOT NULL DEFAULT '0',
  `vote_down` int(11) NOT NULL DEFAULT '0',
  `author` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `author` (`author`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `exchange_article_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `exchange_article_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `exchange_article` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `exchange_comment` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `author` bigint(20) unsigned NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `parent_article` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `parent_id` (`parent_id`),
  KEY `parent_article` (`parent_article`),
  KEY `author` (`author`),
  CONSTRAINT `exchange_comment_ibfk_1` FOREIGN KEY (`author`) REFERENCES `users` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `exchange_comment_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `exchange_comment` (`ID`) ON UPDATE CASCADE,
  CONSTRAINT `exchange_comment_ibfk_3` FOREIGN KEY (`parent_article`) REFERENCES `exchange_article` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
