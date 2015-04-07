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