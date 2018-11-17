# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.20)
# Datenbank: mining
# Erstellt am: 2018-01-21 12:17:21 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Export von Tabelle altfolio
# ------------------------------------------------------------

DROP TABLE IF EXISTS `altfolio`;

CREATE TABLE `altfolio` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `fk_user` (`user_id`),
  CONSTRAINT `user_folio_fk_constraint` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle currency
# ------------------------------------------------------------

DROP TABLE IF EXISTS `currency`;

CREATE TABLE `currency` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `symbol` varchar(6) NOT NULL,
  `icon` varchar(256) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_index` (`symbol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle currency_exchange_rate
# ------------------------------------------------------------

DROP TABLE IF EXISTS `currency_exchange_rate`;

CREATE TABLE `currency_exchange_rate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) unsigned DEFAULT NULL,
  `btc` double unsigned NOT NULL DEFAULT '0',
  `btc_lowest` double unsigned NOT NULL DEFAULT '0',
  `btc_highest` double unsigned NOT NULL DEFAULT '0',
  `usd` double unsigned NOT NULL DEFAULT '0',
  `usd_lowest` double unsigned NOT NULL DEFAULT '0',
  `usd_highest` double unsigned NOT NULL DEFAULT '0',
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_currency` (`currency_id`),
  CONSTRAINT `currency_exchange_fk_constraint` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle gpu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gpu`;

CREATE TABLE `gpu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rig_id` int(11) unsigned DEFAULT NULL,
  `reference` varchar(256) NOT NULL DEFAULT '',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(256) NOT NULL DEFAULT '',
  `bus` int(11) unsigned NOT NULL DEFAULT '0',
  `serial` varchar(256) NOT NULL DEFAULT '',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_rig` (`rig_id`),
  CONSTRAINT `rig_gpu_fk_constraint` FOREIGN KEY (`rig_id`) REFERENCES `rig` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle gpu_telemetry
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gpu_telemetry`;

CREATE TABLE `gpu_telemetry` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `gpu_id` int(11) unsigned DEFAULT NULL,
  `core_temp` double unsigned NOT NULL,
  `core_usage` double unsigned NOT NULL,
  `ram_usage` double unsigned NOT NULL,
  `ram_total` double unsigned NOT NULL,
  `fan_speed` double unsigned NOT NULL,
  `hash_rate` double unsigned NOT NULL,
  `power` double unsigned NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_gpu` (`gpu_id`),
  CONSTRAINT `gpu_telemetry_fk_constraint` FOREIGN KEY (`gpu_id`) REFERENCES `gpu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle rig
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rig`;

CREATE TABLE `rig` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `reference` varchar(32) NOT NULL DEFAULT '',
  `name` varchar(64) NOT NULL DEFAULT '',
  `location` varchar(64) NOT NULL DEFAULT '',
  `price` double NOT NULL DEFAULT '0',
  `os_type` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `fk_user` (`user_id`),
  CONSTRAINT `user_rig_fk_constraint` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle rig_telemetry
# ------------------------------------------------------------

DROP TABLE IF EXISTS `rig_telemetry`;

CREATE TABLE `rig_telemetry` (
  `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
  `rig_id` int(11) unsigned DEFAULT NULL,
  `client_uptime` int(11) unsigned NOT NULL DEFAULT '0',
  `environment_temp` double NOT NULL,
  `cpu_usage` double NOT NULL DEFAULT '0',
  `cpu_temp` double NOT NULL DEFAULT '0',
  `ram_usage` double NOT NULL DEFAULT '0',
  `hash_rate` double NOT NULL DEFAULT '0',
  `power` double NOT NULL DEFAULT '0',
  `watt_hours` double NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_rig` (`rig_id`),
  CONSTRAINT `rig_telemetry_fk_constraint` FOREIGN KEY (`rig_id`) REFERENCES `rig` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle usd_euro
# ------------------------------------------------------------

DROP TABLE IF EXISTS `usd_euro`;

CREATE TABLE `usd_euro` (
  `date` date NOT NULL,
  `rate` double unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `level` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `email` varchar(256) NOT NULL DEFAULT '',
  `username` varchar(64) NOT NULL DEFAULT '',
  `password` binary(60) DEFAULT NULL,
  `encryption_key` blob,
  `app_token` varchar(64) NOT NULL DEFAULT '',
  `rig_key` varchar(64) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle wallet
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wallet`;

CREATE TABLE `wallet` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) unsigned DEFAULT NULL,
  `altfolio_id` int(11) unsigned DEFAULT NULL,
  `address` varchar(64) NOT NULL DEFAULT '',
  `amount` double unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user` (`altfolio_id`),
  KEY `fk_currency` (`currency_id`),
  CONSTRAINT `currency_folio_fk_constraint` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`) ON DELETE CASCADE,
  CONSTRAINT `folio_fk_constraint` FOREIGN KEY (`altfolio_id`) REFERENCES `altfolio` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Export von Tabelle wallet_transaction
# ------------------------------------------------------------

DROP TABLE IF EXISTS `wallet_transaction`;

CREATE TABLE `wallet_transaction` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wallet_id` int(11) unsigned DEFAULT NULL,
  `txid` varchar(128) NOT NULL,
  `amount` double unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `foreign_address` varchar(64) NOT NULL DEFAULT '',
  `datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_wallet` (`wallet_id`),
  CONSTRAINT `wallet_tx_fk_constraint` FOREIGN KEY (`wallet_id`) REFERENCES `wallet` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
