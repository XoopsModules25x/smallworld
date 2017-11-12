--  You may not change or alter any portion of this comment or credits
--  of supporting developers from this source code or any supporting source code
--  which is considered copyrighted (c) material of the original comment or credit authors.
--  This program is distributed in the hope that it will be useful,
--  but WITHOUT ANY WARRANTY; without even the implied warranty of
--  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
-- 
--  Smallworld - social network
-- 
--  @copyright       {@link https://xoops.org 2001-2017 XOOPS Project}
--  @license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license 2.0 or later}
--  @package         modules
--  @subpackage      smallworld
--  @since           1.0
--  @author          Culex  - homepage.: http://culex.dk & email.: culex@culex.dk

CREATE TABLE `smallworld_comments` (
  `com_id`    INT(11) NOT NULL AUTO_INCREMENT,
  `comment`   VARCHAR(200)     DEFAULT NULL,
  `msg_id_fk` INT(11)          DEFAULT NULL,
  `uid_fk`    INT(11)          DEFAULT NULL,
  `ip`        VARCHAR(30)      DEFAULT NULL,
  `created`   INT(11)          DEFAULT '1269249260',
  PRIMARY KEY (`com_id`),
  KEY `msg_id_fk` (`msg_id_fk`),
  KEY `uid_fk` (`uid_fk`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;

CREATE TABLE `smallworld_followers` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `me`     TEXT             NOT NULL,
  `you`    TEXT             NOT NULL,
  `status` TEXT             NOT NULL,
  `date`   TEXT             NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;

CREATE TABLE `smallworld_friends` (
  `id`     INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `me`     TEXT             NOT NULL,
  `you`    TEXT             NOT NULL,
  `status` TEXT             NOT NULL,
  `date`   TEXT             NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;

CREATE TABLE `smallworld_images` (
  `id`      INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid`  TEXT             NOT NULL,
  `imgname` TEXT             NOT NULL,
  `imgurl`  TEXT             NOT NULL,
  `time`    TEXT             NOT NULL,
  `desc`    TEXT             NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;

CREATE TABLE `smallworld_messages` (
  `msg_id`  INT(11) NOT NULL AUTO_INCREMENT,
  `message` TEXT,
  `uid_fk`  INT(11)          DEFAULT NULL,
  `priv`    INT(1)  NOT NULL DEFAULT '1',
  `created` INT(11)          DEFAULT '1269249260',
  PRIMARY KEY (`msg_id`),
  KEY `uid_fk` (`uid_fk`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;

CREATE TABLE `smallworld_user` (
  `id`                 INT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userid`             TEXT            NOT NULL,
  `regdate`            INT(36)         NOT NULL,
  `username`           TEXT            NOT NULL,
  `userimage`          TEXT            NOT NULL,
  `realname`           TEXT            NOT NULL,
  `gender`             TEXT            NOT NULL,
  `intingender`        TEXT            NOT NULL,
  `relationship`       TEXT            NOT NULL,
  `partner`            TEXT            NOT NULL,
  `searchrelat`        TEXT            NOT NULL,
  `birthday`           DATE            NOT NULL DEFAULT '0000-00-00',
  `birthplace`         TEXT            NOT NULL,
  `birthplace_lat`     TEXT            NOT NULL,
  `birthplace_lng`     TEXT            NOT NULL,
  `birthplace_country` TEXT            NOT NULL,
  `politic`            TEXT            NOT NULL,
  `religion`           TEXT            NOT NULL,
  `emailtype`          TEXT            NOT NULL,
  `screenname_type`    TEXT            NOT NULL,
  `screenname`         TEXT            NOT NULL,
  `mobile`             TEXT            NOT NULL,
  `phone`              TEXT            NOT NULL,
  `adress`             TEXT            NOT NULL,
  `present_city`       TEXT            NOT NULL,
  `present_lat`        TEXT            NOT NULL,
  `present_lng`        TEXT            NOT NULL,
  `present_country`    TEXT            NOT NULL,
  `website`            TEXT            NOT NULL,
  `interests`          TEXT            NOT NULL,
  `music`              TEXT            NOT NULL,
  `tvshow`             TEXT            NOT NULL,
  `movie`              TEXT            NOT NULL,
  `books`              TEXT            NOT NULL,
  `aboutme`            TEXT            NOT NULL,
  `school_type`        TEXT            NOT NULL,
  `school`             TEXT            NOT NULL,
  `schoolstart`        TEXT            NOT NULL,
  `schoolstop`         TEXT            NOT NULL,
  `employer`           TEXT            NOT NULL,
  `position`           TEXT            NOT NULL,
  `jobstart`           TEXT            NOT NULL,
  `jobstop`            TEXT            NOT NULL,
  `description`        TEXT            NOT NULL,
  `friends`            VARCHAR(255)    NOT NULL DEFAULT '0',
  `followers`          VARCHAR(255)    NOT NULL DEFAULT '0',
  `admin_flag`         VARCHAR(255)    NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;

CREATE TABLE `smallworld_vote` (
  `vote_id` INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `msg_id`  INT(8) UNSIGNED     NOT NULL DEFAULT '0',
  `com_id`  INT(11)             NOT NULL,
  `user_id` INT(11)             NOT NULL DEFAULT '0',
  `owner`   TEXT                NOT NULL,
  `up`      TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  `down`    TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;

CREATE TABLE `smallworld_complaints` (
  `complaint_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `link`         TEXT             NOT NULL,
  `byuser_id`    INT(11)          NOT NULL DEFAULT '0',
  `owner`        INT(11)          NOT NULL DEFAULT '0',
  PRIMARY KEY (`complaint_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;

CREATE TABLE `smallworld_admin` (
  `id`            INT(11) NOT NULL AUTO_INCREMENT,
  `userid`        INT(11) NOT NULL,
  `username`      TEXT    NOT NULL,
  `realname`      TEXT    NOT NULL,
  `userimage`     TEXT    NOT NULL,
  `ip`            TEXT    NOT NULL,
  `complaint`     TEXT    NOT NULL,
  `inspect_start` TEXT    NOT NULL,
  `inspect_stop`  TEXT    NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;

CREATE TABLE `smallworld_settings` (
  `id`     INT(11) NOT NULL AUTO_INCREMENT,
  `userid` INT(11) NOT NULL,
  `value`  TEXT    NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;
