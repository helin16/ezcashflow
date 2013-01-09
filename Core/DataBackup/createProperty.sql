CREATE TABLE `property` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `addressId` int(10) unsigned NOT NULL DEFAULT 0,
    `boughtValue` float(12, 2) NOT NULL DEFAULT '0.00',
    `setupAccId` int(10) unsigned NOT NULL DEFAULT 0,
    `incomeAccId` int(10) unsigned NOT NULL DEFAULT 0,
    `outgoingAccId` int(10) unsigned NOT NULL DEFAULT 0,
    `comments` varchar(6400) NOT NULL DEFAULT '',
    `active` bool NOT NULL DEFAULT 1,
    `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
    `createdById` int(10) unsigned NOT NULL DEFAULT 0,
    `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedById` int(10) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
    ,INDEX (`addressId`)
    ,INDEX (`createdById`)
    ,INDEX (`updatedById`)
    ,INDEX (`setupAccId`)
    ,INDEX (`incomeAccId`)
    ,INDEX (`outgoingAccId`)
) ENGINE=innodb DEFAULT CHARSET=utf8;
