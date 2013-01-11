DROP TABLE IF EXISTS `asset`;
CREATE TABLE  `asset` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `assetTypeId` int(10) unsigned NOT NULL DEFAULT '0',
  `assetId` varchar(32) NOT NULL DEFAULT '',
  `filename` varchar(100) NOT NULL DEFAULT '',
  `mimeType` varchar(50) NOT NULL DEFAULT '',
  `path` varchar(255) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `assetId` (`assetId`),
  KEY `assetTypeId` (`assetTypeId`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
insert into `asset`(`assetTypeId`, `assetId`, `filename`, `mimeType`, `path`, `active`, `created`, `createdById`, `updated`, `updatedById`)
values (1, '968284c869c18f965d9612b796ab593a', 'logo.png', 'image/png', 'test', 1, NOW(), 1, NOW(), 1);

DROP TABLE IF EXISTS `assettype`;
CREATE TABLE  `assettype` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL DEFAULT '',
  `path` varchar(50) NOT NULL DEFAULT '',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updatedById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `createdById` (`createdById`),
  KEY `updatedById` (`updatedById`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into `assettype`(`type`, `path`, `active`, `created`, `createdById`, `updated`, `updatedById`)
values('Graph', 'assets/graphs/', 1, NOW(), 1, NOW(), 1),
('Report', 'assets/report/', 1, NOW(), 1, NOW(), 1);