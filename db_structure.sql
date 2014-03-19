/*
 Navicat MySQL Data Transfer

 Source Server         : localhost
 Source Server Version : 50144
 Source Host           : localhost
 Source Database       : xioco

 Target Server Version : 50144
 File Encoding         : utf-8

 Date: 03/20/2014 00:26:59 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `comments`
-- ----------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `comment` longtext,
  `noteid` int(11) DEFAULT NULL,
  `created` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `news`
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(150) NOT NULL,
  `author` int(11) NOT NULL,
  `creationdate` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `notes`
-- ----------------------------
DROP TABLE IF EXISTS `notes`;
CREATE TABLE `notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `targetid` int(11) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `subject` text,
  `descr` longtext,
  `author` int(11) DEFAULT NULL,
  `creationdate` int(11) DEFAULT NULL,
  `editdate` int(11) DEFAULT NULL,
  `editauthor` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `notifications`
-- ----------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notificationtype` tinyint(4) DEFAULT NULL,
  `notificationdata` longtext,
  `creationdate` int(11) DEFAULT NULL,
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `connectednote` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `passresets`
-- ----------------------------
DROP TABLE IF EXISTS `passresets`;
CREATE TABLE `passresets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) DEFAULT NULL,
  `hash` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `refs`
-- ----------------------------
DROP TABLE IF EXISTS `refs`;
CREATE TABLE `refs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `targetuser` int(11) DEFAULT NULL,
  `userstring` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
--  Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(60) DEFAULT NULL,
  `password` text,
  `passwordstatus` tinyint(4) NOT NULL,
  `userrank` tinyint(4) DEFAULT NULL,
  `salt` text,
  `email` varchar(100) DEFAULT NULL,
  `lastip` varchar(50) DEFAULT NULL,
  `lastactive` varchar(20) DEFAULT NULL,
  `creationdate` int(11) NOT NULL,
  `unreadnotifications` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

SET FOREIGN_KEY_CHECKS = 1;
