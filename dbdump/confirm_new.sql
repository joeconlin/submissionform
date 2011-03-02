-- phpMyAdmin SQL Dump
-- version 2.8.2.4
-- http://www.phpmyadmin.net
-- 
-- Host: localhost:3306
-- Generation Time: Mar 02, 2011 at 06:27 PM
-- Server version: 5.1.52
-- PHP Version: 5.2.6
-- 
-- Database: `confirm_new`
-- 
CREATE DATABASE `confirm_new` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `confirm_new`;

-- --------------------------------------------------------

-- 
-- Table structure for table `confirm`
-- 

CREATE TABLE `confirm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` varchar(128) NOT NULL DEFAULT '',
  `key` varchar(128) NOT NULL DEFAULT '',
  `email` varchar(250) DEFAULT NULL,
  `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8 AUTO_INCREMENT=76 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(128) NOT NULL DEFAULT '',
  `email` varchar(250) NOT NULL DEFAULT '',
  `active` binary(1) NOT NULL DEFAULT '0',
  `message` text,
  `user_ip` varchar(50) DEFAULT NULL,
  `user_agent` varchar(100) DEFAULT NULL,
  `user_referrer` varchar(100) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `file_location` varchar(200) DEFAULT NULL,
  `time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=utf8 AUTO_INCREMENT=60 ;
