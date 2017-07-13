/**
* This file is part of the ExtendedReg distribution. 
* Detailed copyright and licensing information can be found
* in the gpl-3.0.txt file which should be included in the distribution.
* 
* @version		2.11
* @copyright	Copyright (C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
* @license		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3 or later
* @link			http://jvitals.com
* @since			File available since initial release
*/

CREATE TABLE IF NOT EXISTS `#__extendedreg_settings` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`optname` varchar(255) NOT NULL,
	`value` text NOT NULL,
	`description` varchar(255),
	`group` varchar(255) NOT NULL DEFAULT 'default',
	`ord` int(11) NOT NULL DEFAULT 1,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__extendedreg_addons` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`file_name` varchar(255) NOT NULL,
	`description` tinytext,
	`type` enum('field','captcha','validation','integration','feature') NOT NULL DEFAULT 'field',
	`published` enum('0','1') NOT NULL DEFAULT '0',
	`author` varchar(255) NOT NULL,
	`author_email` varchar(255) NULL,
	`author_url` varchar(255) NULL,
	`license` varchar(50),
	`version` varchar(50) NOT NULL DEFAULT '',
	UNIQUE (`type`,`file_name`),
	PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__extendedreg_fields_groups` (
	`grpid` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`description` varchar(255) NOT NULL,
	PRIMARY KEY (`grpid`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__extendedreg_fields` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL DEFAULT '',
	`name` varchar(50) NOT NULL DEFAULT '' UNIQUE,
	`type` varchar(255) NOT NULL DEFAULT '',
	`required` enum('0','1') NOT NULL DEFAULT '0',
	`published` enum('0','1') NOT NULL DEFAULT '0',
	`editable` enum('0','1') NOT NULL DEFAULT '1',
	`exportable` enum('0','1') NOT NULL DEFAULT '1',
	`params` text,
	`description` mediumtext,
	`grpid` int(11) unsigned NOT NULL DEFAULT 1,
	`ord` int(11) NOT NULL DEFAULT 1,
	`custom_sql` text NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__extendedreg_fields_values` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`field_id` int(11) unsigned NOT NULL REFERENCES `#__extendedreg_fields`(`id`),
	`val` varchar(255) NOT NULL DEFAULT '',
	`ord` int(4) NOT NULL DEFAULT 0,
	UNIQUE (`field_id`, `val`),
	PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__extendedreg_forms` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`description` varchar(255),
	`isdefault` enum('0','1') NOT NULL DEFAULT '0',
	`published` enum('0','1') NOT NULL DEFAULT '0',
	`show_terms` enum('0','1') NOT NULL DEFAULT '0',
	`terms_switcher` enum('0','1') NOT NULL DEFAULT '0',
	`terms_article_id` int(11) NOT NULL DEFAULT '0',
	`terms_value` text,
	`show_age` enum('0','1') NOT NULL DEFAULT '0',
	`age_value` tinyint(3),
	`groups` varchar(100),
	`layout` text,
	`mailfrom` varchar(100) NOT NULL DEFAULT '',
	`admin_mails` tinytext,
	`form_style_width` varchar(10) NOT NULL DEFAULT '100%',
	`form_style_align` enum('align_margin','align_center','align_left','align_right','align_all_left','align_all_right','align_all_center') NOT NULL DEFAULT 'align_left',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__extendedreg_users` (
	`user_id` int(11) unsigned NOT NULL REFERENCES `#__users`(`id`),
	`acceptedterms` enum('0','1') NOT NULL DEFAULT '0',
	`approve` enum('0','1') NOT NULL DEFAULT '0',
	`overage` enum('0','1') NOT NULL DEFAULT '0',
	`ip_addr` varchar(50) NOT NULL DEFAULT '',
	`form_id` int(11) NULL REFERENCES `#__extendedreg_forms`(`id`),
	`notes` text,
	`last_activation_request` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`approve_hash` varchar(32),
	`terminate_hash` varchar(32),
	`last_pass_change` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`user_id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__extendedreg_stats` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`ip_addr` varchar(50) NOT NULL DEFAULT '',
	`port` varchar(8) NOT NULL DEFAULT '',
	`proxy` enum('0','1') NOT NULL DEFAULT '0',
	`user_id` int(11) unsigned NOT NULL REFERENCES `#__users`(`id`),
	`tstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`action` enum('login', 'logout','user_register','profile_edit') NOT NULL DEFAULT 'login',
	PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__extendedreg_login_attempts` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`ip_addr` varchar(50) NOT NULL DEFAULT '',
	`username` varchar(150) NOT NULL DEFAULT '',
	`tstamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';

CREATE TABLE IF NOT EXISTS `#__extendedreg_blocks` (
	`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`block_item` varchar(255) NOT NULL DEFAULT '' UNIQUE,
	`blocked_until` int(11) DEFAULT 0,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';
