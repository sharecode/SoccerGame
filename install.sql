# Host: 127.0.0.1  (Version 5.5.54)
# Date: 2017-05-09 14:51:00
# Generator: MySQL-Front 6.0  (Build 1.108)


#
# Structure for table "admin"
#

DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `deep` tinyint(3) unsigned NOT NULL DEFAULT '15'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Data for table "admin"
#


#
# Structure for table "avatar_blob"
#

DROP TABLE IF EXISTS `avatar_blob`;
CREATE TABLE `avatar_blob` (
  `owner` int(11) unsigned NOT NULL DEFAULT '0',
  `data_blob` mediumblob,
  `img_type` tinyint(3) unsigned DEFAULT NULL,
  KEY `avatar_owner` (`owner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Data for table "avatar_blob"
#


#
# Structure for table "bill"
#

DROP TABLE IF EXISTS `bill`;
CREATE TABLE `bill` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `bill_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `mday` int(11) unsigned NOT NULL DEFAULT '0',
  `mmonth` int(11) unsigned NOT NULL DEFAULT '0',
  `match_id` int(11) unsigned NOT NULL DEFAULT '0',
  `g_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `g_handicap` float(4,2) NOT NULL DEFAULT '0.00',
  `g_team` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `g_odds` float(4,2) unsigned NOT NULL DEFAULT '0.00',
  `stake` int(11) unsigned NOT NULL DEFAULT '1',
  `g_result` float(2,1) NOT NULL DEFAULT '0.0',
  `is_check` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `g_time` int(11) unsigned NOT NULL DEFAULT '0',
  `g_ip` varchar(255) DEFAULT NULL,
  `stake_result` float(9,5) NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`id`),
  KEY `owner_bill` (`owner`),
  KEY `match_id_bill` (`match_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

#
# Data for table "bill"
#


#
# Structure for table "bill_tded"
#

DROP TABLE IF EXISTS `bill_tded`;
CREATE TABLE `bill_tded` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bill_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `owner` int(11) NOT NULL,
  `mday` int(11) unsigned NOT NULL DEFAULT '0',
  `mmonth` int(11) unsigned NOT NULL DEFAULT '0',
  `match_id` int(11) unsigned NOT NULL DEFAULT '0',
  `g_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `g_handicap` float(4,2) NOT NULL DEFAULT '0.00',
  `g_team` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `g_odds` float(4,2) unsigned NOT NULL DEFAULT '0.00',
  `stake` int(11) unsigned NOT NULL DEFAULT '1',
  `g_result` float(2,1) NOT NULL DEFAULT '0.0',
  `is_check` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `g_time` int(11) unsigned NOT NULL DEFAULT '0',
  `g_ip` varchar(255) DEFAULT NULL,
  `stake_result` float(9,5) NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`id`),
  KEY `match_id_bill` (`match_id`),
  KEY `owner_bill` (`owner`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "bill_tded"
#


#
# Structure for table "dom_buffer"
#

DROP TABLE IF EXISTS `dom_buffer`;
CREATE TABLE `dom_buffer` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mday` int(11) unsigned NOT NULL DEFAULT '0',
  `mkick` int(11) unsigned NOT NULL DEFAULT '0',
  `league` varchar(255) DEFAULT NULL,
  `home_en` varchar(255) DEFAULT NULL,
  `away_en` varchar(255) DEFAULT NULL,
  `nfield` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `t1_v1` float(4,2) NOT NULL DEFAULT '0.00',
  `t1_v2` float(4,2) NOT NULL DEFAULT '0.00',
  `t1_v3` float(4,2) NOT NULL DEFAULT '0.00',
  `t2_v1` float(4,2) NOT NULL DEFAULT '0.00',
  `t2_v2` float(4,2) NOT NULL DEFAULT '0.00',
  `t2_v3` float(4,2) NOT NULL DEFAULT '0.00',
  `t3_v1` float(4,2) NOT NULL DEFAULT '0.00',
  `t3_v2` float(4,2) NOT NULL DEFAULT '0.00',
  `t3_v3` float(4,2) NOT NULL DEFAULT '0.00',
  `t4_v1` float(4,2) NOT NULL DEFAULT '0.00',
  `t4_v2` float(4,2) NOT NULL DEFAULT '0.00',
  `t4_v3` float(4,2) NOT NULL DEFAULT '0.00',
  `t5_v1` float(4,2) NOT NULL DEFAULT '0.00',
  `t5_v2` float(4,2) NOT NULL DEFAULT '0.00',
  `t5_v3` float(4,2) NOT NULL DEFAULT '0.00',
  `t6_v1` float(4,2) NOT NULL DEFAULT '0.00',
  `t6_v2` float(4,2) NOT NULL DEFAULT '0.00',
  `t6_v3` float(4,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1894 DEFAULT CHARSET=utf8;

#
# Data for table "dom_buffer"
#


#
# Structure for table "league"
#

DROP TABLE IF EXISTS `league`;
CREATE TABLE `league` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `en_name` varchar(512) DEFAULT NULL,
  `th_name` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=298 DEFAULT CHARSET=utf8;

#
# Data for table "league"
#


#
# Structure for table "market"
#

DROP TABLE IF EXISTS `market`;
CREATE TABLE `market` (
  `match_id` int(11) unsigned DEFAULT NULL,
  `market_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `value1` float(4,2) NOT NULL DEFAULT '0.00',
  `value2` float(4,2) NOT NULL DEFAULT '0.00',
  `value3` float(4,2) NOT NULL DEFAULT '0.00',
  KEY `m_m_id` (`match_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Data for table "market"
#


#
# Structure for table "matchday"
#

DROP TABLE IF EXISTS `matchday`;
CREATE TABLE `matchday` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mday` int(11) unsigned NOT NULL DEFAULT '0',
  `league` int(11) unsigned NOT NULL DEFAULT '0',
  `mkick` int(11) unsigned NOT NULL DEFAULT '0',
  `nfield` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `home_team` int(11) unsigned NOT NULL DEFAULT '0',
  `away_team` int(11) unsigned NOT NULL DEFAULT '0',
  `advan` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `handicap` float(4,2) unsigned DEFAULT '0.00',
  `fh_h_score` tinyint(3) NOT NULL DEFAULT '0',
  `fh_a_score` tinyint(3) NOT NULL DEFAULT '0',
  `ft_h_score` tinyint(3) NOT NULL DEFAULT '0',
  `ft_a_score` tinyint(3) NOT NULL DEFAULT '0',
  `m_finish` tinyint(3) NOT NULL DEFAULT '0',
  `m_delay` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `match_day` (`mday`)
) ENGINE=MyISAM AUTO_INCREMENT=737 DEFAULT CHARSET=utf8;

#
# Data for table "matchday"
#


#
# Structure for table "member_"
#

DROP TABLE IF EXISTS `member_`;
CREATE TABLE `member_` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `facebook_id` bigint(20) unsigned DEFAULT '0',
  `user` varchar(64) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `name` varchar(510) DEFAULT NULL,
  `aliasname` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(510) DEFAULT NULL,
  `address` text,
  `credit` float(10,2) unsigned NOT NULL DEFAULT '0.00',
  `bank` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bank_number` varchar(255) DEFAULT NULL,
  `reg_time` int(11) unsigned DEFAULT NULL,
  `reg_ip` varchar(255) DEFAULT NULL,
  `login_serial` varchar(255) DEFAULT NULL,
  `login_count` int(11) unsigned NOT NULL DEFAULT '0',
  `last_login` int(11) unsigned NOT NULL DEFAULT '0',
  `score` float(7,5) NOT NULL DEFAULT '0.00000',
  `score_mix` float(7,5) NOT NULL DEFAULT '0.00000',
  `sum_score` float(10,5) NOT NULL DEFAULT '0.00000',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`user`),
  KEY `score_sort` (`score`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

#
# Data for table "member_"
#


#
# Structure for table "mixparlay"
#

DROP TABLE IF EXISTS `mixparlay`;
CREATE TABLE `mixparlay` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bill` int(11) unsigned NOT NULL DEFAULT '0',
  `owner` int(11) NOT NULL,
  `match_id` int(11) unsigned NOT NULL DEFAULT '0',
  `g_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `g_handicap` float(4,2) NOT NULL DEFAULT '0.00',
  `g_team` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `g_odds` float(4,2) unsigned NOT NULL DEFAULT '0.00',
  `g_result` float(2,1) NOT NULL DEFAULT '0.0',
  `is_check` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `stake_result` float(9,5) DEFAULT '0.00000',
  PRIMARY KEY (`id`),
  KEY `match_id_bill` (`match_id`),
  KEY `owner_bill` (`owner`),
  KEY `bill_mix` (`bill`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "mixparlay"
#


#
# Structure for table "out_market"
#

DROP TABLE IF EXISTS `out_market`;
CREATE TABLE `out_market` (
  `match_id` int(11) unsigned DEFAULT NULL,
  `market_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `value1` float(4,2) NOT NULL DEFAULT '0.00',
  `value2` float(4,2) NOT NULL DEFAULT '0.00',
  `value3` float(4,2) NOT NULL DEFAULT '0.00',
  KEY `m_m_id` (`match_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

#
# Data for table "out_market"
#


#
# Structure for table "session"
#

DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `id` varchar(24) NOT NULL DEFAULT '',
  `data` varchar(20480) DEFAULT '',
  `ip_owner` varchar(255) DEFAULT NULL,
  `expire` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Data for table "session"
#


#
# Structure for table "team"
#

DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `en_name` varchar(512) DEFAULT NULL,
  `th_name` varchar(512) DEFAULT NULL,
  `logo` mediumblob,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1201 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

#
# Data for table "team"
#


#
# Structure for table "team_logo"
#

DROP TABLE IF EXISTS `team_logo`;
CREATE TABLE `team_logo` (
  `id` int(11) unsigned NOT NULL DEFAULT '0',
  `blob_data` mediumblob,
  `img_type` smallint(5) unsigned NOT NULL DEFAULT '0',
  `img_x` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `img_y` mediumint(8) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `team_logo_id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Data for table "team_logo"
#


#
# Structure for table "zean"
#

DROP TABLE IF EXISTS `zean`;
CREATE TABLE `zean` (
  `id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

#
# Data for table "zean"
#

