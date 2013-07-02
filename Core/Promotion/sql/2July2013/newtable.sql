CREATE TABLE `eofy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `end` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `comments` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(4) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `asset_eofy` (
  `assetId` int(10) unsigned NOT NULL DEFAULT '0',
  `eofyId` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`assetId`,`eofyId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;