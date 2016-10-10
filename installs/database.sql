SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

CREATE TABLE IF NOT EXISTS `site_category` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE MyISAM, DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `site_list` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL,
	`type` varchar(50) NOT NULL,
	`sort` varchar(50) NOT NULL,
	`position` int(11) NOT NULL,
	`id_menu` int(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE MyISAM, DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `site_log` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`date_time` int(11) NOT NULL,
	`treatment` varchar(2000) NOT NULL,
	`error` varchar(2000) NOT NULL,
	`request` varchar(2000) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE MyISAM, DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `site_log_activite` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`date_time` int(11) NOT NULL,
	`username` varchar(50) NOT NULL,
	`module` varchar(50) DEFAULT NULL,
	`action` varchar(50) DEFAULT NULL,
	`comment` varchar(500) DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE MyISAM, DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `site_menu` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL,
	`icon` varchar(50) NOT NULL,
	`position` int(11) NOT NULL,
	`name_table` varchar(50) NOT NULL,
	`id_category` int(11) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE MyISAM, DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `site_setting` (
	`key` varchar(250) NOT NULL,
	`value` mediumtext NOT NULL,
	PRIMARY KEY (`key`)
) ENGINE MyISAM, DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `site_setting` (`key`, `value`) VALUES
("avatar_height", "128"),
("avatar_weight", "51200"),
("avatar_width", "128"),
("invite", "0"),
("lastadd_max", "6"),
("maintenance", "0"),
("message_home", ""),
("message_maintenance", ""),
("open", "0"),
("registration", "0"),
("title", "Site"),
("version", "0.0.1");

CREATE TABLE IF NOT EXISTS `site_user` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(50) DEFAULT NULL,
	`username` varchar(50) DEFAULT NULL,
	`password` tinytext,
	`email` varchar(100) DEFAULT NULL,
	`date_registration` int(11) DEFAULT NULL,
	`date_lastlogin` int(11) DEFAULT NULL,
	`date_birthday` int(11) DEFAULT NULL,
	`url_website` varchar(100) DEFAULT NULL,
	`country` varchar(100) DEFAULT NULL,
	`avatar` varchar(50) NOT NULL DEFAULT "1.png",
	`theme` varchar(50) NOT NULL DEFAULT "bootstrap",
	`status` enum("0", "1") NOT NULL DEFAULT "0",
	`admin` enum("0", "1") NOT NULL DEFAULT "0",
	`access` enum("0", "1") DEFAULT "0",
	PRIMARY KEY (`id`)
) ENGINE MyISAM, DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

INSERT INTO `site_user` (`id`, `name`, `username`, `password`, `email`, `date_registration`, `date_lastlogin`, `date_birthday`, `url_website`, `country`, `avatar`, `theme`, `status`, `admin`, `access`) VALUES
(1, "(Invité)", "anonymous", "0a92fab3230134cca6eadd9898325b9b2ae67998", "anonymous@anonymous.com", unix_timestamp(), NULL, NULL, NULL, NULL, "1.png", "bootstrap", "0", "0", "1");

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;