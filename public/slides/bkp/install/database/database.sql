

-- Standalone Slider Revolution DB Structure


DROP TABLE IF EXISTS `revslider_css`;
CREATE TABLE `revslider_css` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `handle` text NOT NULL,
  `settings` LONGTEXT,
  `hover` LONGTEXT,
  `params` text NOT NULL,
  `advanced` LONGTEXT,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `revslider_layer_animations`;
CREATE TABLE `revslider_layer_animations` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `handle` text NOT NULL,
  `params` text NOT NULL,
  `settings` text,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `revslider_navigations`;
CREATE TABLE `revslider_navigations` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `handle` varchar(191) NOT NULL,
  `css` LONGTEXT NOT NULL,
  `markup` LONGTEXT NOT NULL,
  `settings` LONGTEXT,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `revslider_sliders`;
CREATE TABLE `revslider_sliders` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `title` tinytext NOT NULL,
  `alias` tinytext,
  `params` LONGTEXT NOT NULL,
  `settings` text NOT NULL DEFAULT '',
  `type` varchar(191) NOT NULL DEFAULT '',
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `revslider_slides`;
CREATE TABLE `revslider_slides` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `slider_id` int(9) NOT NULL,
  `slide_order` int(11) NOT NULL,
  `params` LONGTEXT NOT NULL,
  `layers` LONGTEXT NOT NULL,
  `settings` text NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `revslider_static_slides`;
CREATE TABLE `revslider_static_slides` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `slider_id` int(9) NOT NULL,
  `params` LONGTEXT NOT NULL,
  `layers` LONGTEXT NOT NULL,
  `settings` text NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `revslider_user`;
CREATE TABLE `revslider_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `join_date` timestamp NULL DEFAULT NULL,
  `last_visit` timestamp NULL DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(120) NOT NULL,
  `salt` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `revslider_options`;
CREATE TABLE `revslider_options` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `handle` varchar(100) NOT NULL,
  `option` LONGTEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `revslider_images`;
CREATE TABLE `revslider_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `revslider_transients`;
CREATE TABLE `revslider_transients` (
  `id` int(9) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `handle` varchar(200) NOT NULL,
  `expires` timestamp NULL,
  `value` LONGTEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `revslider_sessions`;
CREATE TABLE `revslider_sessions` (
	session_id varchar(40) DEFAULT '0' NOT NULL,
	ip_address varchar(45) DEFAULT '0' NOT NULL,
	user_agent varchar(120) NOT NULL,
	last_activity int(10) unsigned DEFAULT 0 NOT NULL,
	user_data mediumtext NOT NULL,
	PRIMARY KEY (session_id),
	KEY `last_activity_idx` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


