-- phpMyAdmin SQL Dump
-- version 3.2.5
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2012 at 05:47 PM
-- Server version: 5.1.43
-- PHP Version: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `reg8log`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_logins`
--

CREATE TABLE IF NOT EXISTS `failed_logins` (
  `username` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `username_exists` tinyint(1) DEFAULT NULL,
  `attempts` binary(40) NOT NULL,
  `pos` tinyint(4) NOT NULL COMMENT 'new attempt''s time insert position in the attempts field',
  `last_attempt` int(11) unsigned NOT NULL,
  PRIMARY KEY (`username`),
  KEY `username_exists` (`username_exists`),
  KEY `last_attempt` (`last_attempt`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
