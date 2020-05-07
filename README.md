Server Booking System
=====================

 Simple booking system written in php, with mysql backend.

 Users can book servers, delete or edit their existing booking.

 Admin can create, delete or edit all bookings, as well as manage the list of available servers.

 User accounts and authentication are handled by LDAP, admin account is hard coded.

 The database consists of 4 tables: cluster, server, booking and booking_map

Table structure for table `cluster`
=====================================

CREATE TABLE `cluster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `cpu` varchar(32) DEFAULT NULL,
  `cores` varchar(32) DEFAULT NULL,
  `ram` varchar(32) DEFAULT NULL,
  `is_bookable` tinyint(1) DEFAULT '1',
  `comment` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;


Table structure for table `server`
====================================

CREATE TABLE `server` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  `cluster_id` int(11) NOT NULL,
  `os` varchar(32) DEFAULT NULL,
  `is_bookable` tinyint(1) DEFAULT '1',
  `comment` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `serverNameIndex` (`name`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=latin1;


 Table structure for table `booking`
===================================

CREATE TABLE `booking` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(32) NOT NULL,
  `begin` datetime NOT NULL,
  `end` datetime NOT NULL,
  `comment` varchar(128) DEFAULT NULL,
  `status` int(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `userIndex` (`user`) USING HASH,
  KEY `beginIndex` (`begin`) USING BTREE,
  KEY `endIndex` (`end`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=904 DEFAULT CHARSET=latin1;


 Table structure for table `booking_map`
==========================================

CREATE TABLE `booking_map` (
  `booking_id` int(11) NOT NULL,
  `server_id` int(11) NOT NULL,
  `status` int(1) DEFAULT '1',
  PRIMARY KEY (`booking_id`,`server_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


