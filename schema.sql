-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 20, 2016 at 09:07 PM
-- Server version: 5.5.31-0ubuntu0.13.04.1
-- PHP Version: 5.4.9-4ubuntu2.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wiki`
--

-- --------------------------------------------------------

--
-- Table structure for table `content_piece`
--

CREATE TABLE IF NOT EXISTS `content_piece` (
  `unique_id` int(11) NOT NULL AUTO_INCREMENT,
  `cubogroup` int(11) NOT NULL,
  `section_ch_id` int(11) NOT NULL DEFAULT '0',
  `origin` int(11) NOT NULL DEFAULT '-1',
  `conflictflag` int(11) NOT NULL DEFAULT '0',
  `content` longtext NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0',
  `msg` text NOT NULL,
  `approval` text NOT NULL,
  `created_by` int(11) NOT NULL DEFAULT '0',
  `date_modified` date NOT NULL,
  `time_modified` bigint(20) NOT NULL,
  `ip_of_modifier` varchar(300) NOT NULL,
  PRIMARY KEY (`unique_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8493 ;

-- --------------------------------------------------------

--
-- Table structure for table `cubes`
--

CREATE TABLE IF NOT EXISTS `cubes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL DEFAULT '0',
  `contentid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1683 ;

-- --------------------------------------------------------

--
-- Table structure for table `discussion`
--

CREATE TABLE IF NOT EXISTS `discussion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL DEFAULT '-1',
  `groupid` int(11) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `childtime` bigint(20) NOT NULL,
  `cubeid` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1122 ;

-- --------------------------------------------------------

--
-- Table structure for table `file_uploads`
--

CREATE TABLE IF NOT EXISTS `file_uploads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `display_name` varchar(100) NOT NULL COMMENT 'Original filename from user''s HDD',
  `filename` varchar(45) NOT NULL COMMENT 'Filename on server HDD',
  `size` int(10) unsigned NOT NULL COMMENT 'Filesize in bytes',
  `width` int(10) unsigned NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `whence` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'When the upload occurred',
  PRIMARY KEY (`id`),
  KEY `idxUserId` (`userId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=878 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `type` int(2) NOT NULL COMMENT '0 is public, 1 is private',
  `access_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 = students need specific access, 1 = students can edit any section',
  `uid` int(11) NOT NULL COMMENT 'uid of creator',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=114 ;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `messages_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent` int(11) NOT NULL DEFAULT '-1',
  `read` enum('read','unread') NOT NULL DEFAULT 'unread',
  `from_user_id` int(10) unsigned NOT NULL,
  `to_user_id` int(10) unsigned NOT NULL,
  `msg` text NOT NULL,
  `whence` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`messages_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------

--
-- Table structure for table `registered_groups`
--

CREATE TABLE IF NOT EXISTS `registered_groups` (
  `userId` int(10) unsigned NOT NULL,
  `groupId` int(10) unsigned NOT NULL,
  `role` enum('admin','instructor','student') NOT NULL DEFAULT 'student',
  `whence` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userId`,`groupId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='info about user registered in WHAT group/book';

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE IF NOT EXISTS `section` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cubogroup` int(11) NOT NULL,
  `parent` int(11) NOT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `description` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1897 ;

-- --------------------------------------------------------

--
-- Table structure for table `section_assignments`
--

CREATE TABLE IF NOT EXISTS `section_assignments` (
  `user_id` int(10) unsigned NOT NULL COMMENT 'Primary key of `users`',
  `section_id` int(10) unsigned NOT NULL COMMENT 'Primary key of `section`',
  PRIMARY KEY (`user_id`,`section_id`),
  KEY `user_id_idx` (`user_id`),
  KEY `section_id_idx` (`section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tos_entries`
--

CREATE TABLE IF NOT EXISTS `tos_entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cubocubeID` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(500) NOT NULL,
  `datetime` int(10) unsigned NOT NULL,
  `decision` enum('agree','disagree') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cubocubeID` (`cubocubeID`,`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=207 ;

-- --------------------------------------------------------

--
-- Table structure for table `tos_permissions`
--

CREATE TABLE IF NOT EXISTS `tos_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cubocubeID` int(10) unsigned NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(250) NOT NULL,
  `verified` int(1) NOT NULL DEFAULT '0',
  `beta` smallint(6) NOT NULL,
  `password` varchar(500) NOT NULL,
  `salt` varchar(50) NOT NULL,
  `first_name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `IP` varchar(300) NOT NULL,
  `role` int(11) NOT NULL DEFAULT '2',
  `studentid` int(11) NOT NULL DEFAULT '0',
  `assigned_to` varchar(1000) NOT NULL DEFAULT ',' COMMENT 'Example: '',1,48,''',
  `whence` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=244 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `file_uploads`
--
ALTER TABLE `file_uploads`
  ADD CONSTRAINT `file_uploads_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `section_assignments`
--
ALTER TABLE `section_assignments`
  ADD CONSTRAINT `section_assignments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `section_assignments_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
