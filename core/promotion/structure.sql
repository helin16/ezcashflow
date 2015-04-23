-- Setting Up Database
DROP TABLE IF EXISTS `accountentry`;
CREATE TABLE `accountentry` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`isSumAcc` bool NOT NULL DEFAULT 0,
	`accountNo` int(20) unsigned NOT NULL DEFAULT 0,
	`typeId` int(10) unsigned NOT NULL DEFAULT 0,
	`organizationId` int(10) unsigned NOT NULL DEFAULT 0,
	`initValue` double(10,4) NOT NULL DEFAULT 0,
	`rootId` int(10) unsigned NULL DEFAULT NULL,
	`parentId` int(10) unsigned NULL DEFAULT NULL,
	`path` varchar(255) NOT NULL DEFAULT '',
	`description` varchar(255) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`typeId`)
	,INDEX (`organizationId`)
	,INDEX (`rootId`)
	,INDEX (`parentId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,INDEX (`name`)
	,INDEX (`initValue`)
	,INDEX (`path`)
	,INDEX (`accountNo`)
	,INDEX (`isSumAcc`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `accounttype`;
CREATE TABLE `accounttype` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(10) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `transaction`;
CREATE TABLE `transaction` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`organizationId` int(10) unsigned NOT NULL DEFAULT 0,
	`groupId` varchar(32) NOT NULL DEFAULT '',
	`logDate` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`logById` int(10) unsigned NULL DEFAULT NULL,
	`accountEntryId` int(10) unsigned NOT NULL DEFAULT 0,
	`credit` double(10,4) NULL DEFAULT 0,
	`debit` double(10,4) NULL DEFAULT 0,
	`description` varchar(255) NOT NULL DEFAULT '',
	`value` double(10,4) NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`organizationId`)
	,INDEX (`logById`)
	,INDEX (`accountEntryId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,INDEX (`logDate`)
	,INDEX (`groupId`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `asset`;
CREATE TABLE `asset` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`filename` varchar(100) NOT NULL DEFAULT '',
	`mimeType` varchar(50) NOT NULL DEFAULT '',
	`contentId` int(10) unsigned NOT NULL DEFAULT 0,
	`skey` varchar(32) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`contentId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,UNIQUE INDEX (`skey`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `attachment`;
CREATE TABLE `attachment` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`entityId` int(10) unsigned NOT NULL DEFAULT 0,
	`EntityName` varchar(100) NOT NULL DEFAULT '',
	`assetId` int(10) unsigned NOT NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`assetId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,INDEX (`entityId`)
	,INDEX (`EntityName`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `content`;
CREATE TABLE `content` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`content` longtext NOT NULL ,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `property`;
CREATE TABLE `property` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(200) NOT NULL DEFAULT '',
	`description` varchar(255) NOT NULL DEFAULT '',
	`organizationId` int(10) unsigned NOT NULL DEFAULT 0,
	`setupAccId` int(10) unsigned NULL DEFAULT NULL,
	`incomeAccId` int(10) unsigned NULL DEFAULT NULL,
	`expenseAccId` int(10) unsigned NULL DEFAULT NULL,
	`boughtPrice` double(10,4) unsigned NOT NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`organizationId`)
	,INDEX (`setupAccId`)
	,INDEX (`incomeAccId`)
	,INDEX (`expenseAccId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `confirmation`;
CREATE TABLE `confirmation` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`type` varchar(20) NOT NULL DEFAULT '',
	`entityId` int(10) unsigned NOT NULL DEFAULT 0,
	`entityName` varchar(100) NOT NULL DEFAULT '',
	`comments` varchar(255) NOT NULL DEFAULT '',
	`expiryTime` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`skey` varchar(32) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,INDEX (`entityId`)
	,INDEX (`entityName`)
	,INDEX (`type`)
	,UNIQUE INDEX (`skey`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`transId` varchar(32) NOT NULL DEFAULT '',
	`type` varchar(20) NOT NULL DEFAULT '',
	`entityId` int(10) unsigned NOT NULL DEFAULT 0,
	`entityName` varchar(100) NOT NULL DEFAULT '',
	`funcName` varchar(100) NOT NULL DEFAULT '',
	`comments` varchar(255) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,INDEX (`transId`)
	,INDEX (`entityId`)
	,INDEX (`entityName`)
	,INDEX (`type`)
	,INDEX (`funcName`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`isRead` bool NOT NULL DEFAULT 0,
	`sendType` varchar(10) NOT NULL DEFAULT '',
	`toId` int(10) unsigned NOT NULL DEFAULT 0,
	`fromId` int(10) unsigned NOT NULL DEFAULT 0,
	`type` varchar(10) NOT NULL DEFAULT '',
	`subject` varchar(100) NOT NULL DEFAULT '',
	`body` longtext NOT NULL ,
	`transId` varchar(32) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`toId`)
	,INDEX (`fromId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,INDEX (`isRead`)
	,INDEX (`sendType`)
	,INDEX (`transId`)
	,INDEX (`type`)
	,INDEX (`subject`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `organization`;
CREATE TABLE `organization` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`skey` varchar(32) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,INDEX (`name`)
	,INDEX (`skey`)
	,UNIQUE INDEX (`skey`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `orgpersonrole`;
CREATE TABLE `orgpersonrole` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`personId` int(10) unsigned NOT NULL DEFAULT 0,
	`roleId` int(10) unsigned NOT NULL DEFAULT 0,
	`organizationId` int(10) unsigned NOT NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`personId`)
	,INDEX (`roleId`)
	,INDEX (`organizationId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`email` varchar(100) NOT NULL DEFAULT '',
	`firstName` varchar(50) NOT NULL DEFAULT '',
	`lastName` varchar(50) NOT NULL DEFAULT '',
	`fullName` varchar(200) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,INDEX (`email`)
	,INDEX (`firstName`)
	,INDEX (`lastName`)
	,INDEX (`fullName`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(50) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,UNIQUE INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`key` varchar(32) NOT NULL DEFAULT '',
	`data` longtext NOT NULL ,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,UNIQUE INDEX (`key`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `systemsettings`;
CREATE TABLE `systemsettings` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`type` varchar(50) NOT NULL DEFAULT '',
	`value` varchar(255) NOT NULL DEFAULT '',
	`description` varchar(100) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,UNIQUE INDEX (`type`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `useraccount`;
CREATE TABLE `useraccount` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`username` varchar(100) NOT NULL DEFAULT '',
	`password` varchar(40) NOT NULL DEFAULT '',
	`personId` int(10) unsigned NOT NULL DEFAULT 0,
	`confirmationId` int(10) unsigned NULL DEFAULT NULL,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`personId`)
	,INDEX (`confirmationId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,INDEX (`username`)
	,INDEX (`password`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `entitytag`;
CREATE TABLE `entitytag` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`type` varchar(20) NOT NULL DEFAULT '',
	`entityId` int(10) unsigned NOT NULL DEFAULT 0,
	`EntityName` varchar(100) NOT NULL DEFAULT '',
	`tagId` int(10) unsigned NOT NULL DEFAULT 0,
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`tagId`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,INDEX (`entityId`)
	,INDEX (`EntityName`)
	,INDEX (`type`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `tag`;
CREATE TABLE `tag` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL DEFAULT '',
	`active` bool NOT NULL DEFAULT 1,
	`created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
	`createdById` int(10) unsigned NOT NULL DEFAULT 0,
	`updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`updatedById` int(10) unsigned NOT NULL DEFAULT 0,
	PRIMARY KEY (`id`)
	,INDEX (`createdById`)
	,INDEX (`updatedById`)
	,INDEX (`active`)
	,INDEX (`created`)
	,INDEX (`updated`)
	,UNIQUE INDEX (`name`)
) ENGINE=innodb DEFAULT CHARSET=utf8;

-- Completed CRUD Setup.