CREATE TABLE `smallworld_admin` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) DEFAULT NULL,
  `username` text DEFAULT NULL,
  `realname` text DEFAULT NULL,
  `userimage` text DEFAULT NULL,
  `ip` text DEFAULT NULL,
  `complaint` text DEFAULT NULL,
  `inspect_start` text DEFAULT NULL,
  `inspect_stop` text DEFAULT NULL,
  
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `smallworld_comments` (
  `com_id` int(11) NOT NULL auto_increment,
  `comment` varchar(200) DEFAULT NULL,
  `msg_id_fk` int(11) DEFAULT NULL,
  `uid_fk` int(11) DEFAULT NULL,
  `ip` varchar(30) DEFAULT NULL,
  `created` int(11) DEFAULT 1269249260,
  PRIMARY KEY (`com_id`),
  KEY `msg_id_fk` (`msg_id_fk`),
  KEY `uid_fk` (`uid_fk`)
) ENGINE=MyISAM;

CREATE TABLE `smallworld_complaints` (
  `complaint_id` int(11) UNSIGNED NOT NULL auto_increment,
  `link` text DEFAULT NULL,
  `byuser_id` int(11) DEFAULT 0,
  `owner` int(11) DEFAULT 0,
  PRIMARY KEY (`complaint_id`)
) ENGINE=MyISAM;

CREATE TABLE `smallworld_followers` (
  `id` int(10) UNSIGNED NOT NULL auto_increment,
  `me` text DEFAULT NULL,
  `you` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `date` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `smallworld_friends` (
  `id` int(10) UNSIGNED NOT NULL auto_increment,
  `me` text DEFAULT NULL,
  `you` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `date` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `smallworld_images` (
  `id` int(10) UNSIGNED NOT NULL auto_increment,
  `userid` text DEFAULT NULL,
  `imgname` text DEFAULT NULL,
  `imgurl` text DEFAULT NULL,
  `time` text DEFAULT NULL,
  `desc` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `smallworld_messages` (
  `msg_id` int(11) NOT NULL auto_increment,
  `message` text DEFAULT NULL,
  `uid_fk` int(11) DEFAULT NULL,
  `priv` int(1) NOT NULL DEFAULT 1,
  `created` int(11) DEFAULT 1269249260,
  PRIMARY KEY (`msg_id`),
  KEY `uid_fk` (`uid_fk`)
) ENGINE=MyISAM;

CREATE TABLE `smallworld_settings` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) DEFAULT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `smallworld_user` (
  `id` int(5) UNSIGNED NOT NULL auto_increment,
  `userid` text NOT NULL,
  `regdate` int(36) NOT NULL,
  `username` text NOT NULL,
  `userimage` text DEFAULT NULL,
  `realname` text DEFAULT NULL,
  `gender` text DEFAULT NULL,
  `intingender` text DEFAULT NULL,
  `relationship` text DEFAULT NULL,
  `partner` text DEFAULT NULL,
  `searchrelat` text DEFAULT NULL,
  `birthday` date DEFAULT '0000-00-00',
  `birthplace` text DEFAULT NULL,
  `birthplace_lat` text DEFAULT NULL,
  `birthplace_lng` text DEFAULT NULL,
  `birthplace_country` text DEFAULT NULL,
  `politic` text DEFAULT NULL,
  `religion` text DEFAULT NULL,
  `emailtype` text DEFAULT NULL,
  `screenname_type` text DEFAULT NULL,
  `screenname` text DEFAULT NULL,
  `mobile` text DEFAULT NULL,
  `phone` text DEFAULT NULL,
  `adress` text DEFAULT NULL,
  `present_city` text DEFAULT NULL,
  `present_lat` text DEFAULT NULL,
  `present_lng` text DEFAULT NULL,
  `present_country` text DEFAULT NULL,
  `website` text DEFAULT NULL,
  `interests` text DEFAULT NULL,
  `music` text DEFAULT NULL,
  `tvshow` text DEFAULT NULL,
  `movie` text DEFAULT NULL,
  `books` text DEFAULT NULL,
  `aboutme` text DEFAULT NULL,
  `school_type` text DEFAULT NULL,
  `school` text DEFAULT NULL,
  `schoolstart` text DEFAULT NULL,
  `schoolstop` text DEFAULT NULL,
  `employer` text DEFAULT NULL,
  `position` text DEFAULT NULL,
  `jobstart` text DEFAULT NULL,
  `jobstop` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `friends` varchar(255) DEFAULT '0',
  `followers` varchar(255) DEFAULT '0',
  `admin_flag` varchar(255) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

CREATE TABLE `smallworld_vote` (
  `vote_id` int(11) UNSIGNED NOT NULL auto_increment,
  `msg_id` int(8) UNSIGNED DEFAULT 0,
  `com_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT 0,
  `owner` text DEFAULT NULL,
  `up` tinyint(3) UNSIGNED DEFAULT 0,
  `down` tinyint(3) UNSIGNED DEFAULT 0,
  PRIMARY KEY (`vote_id`)
) ENGINE=MyISAM;



	
