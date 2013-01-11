DROP TABLE IF EXISTS `asset_property`;
CREATE TABLE  `asset_property` (
  `assetId` int(10) unsigned NOT NULL DEFAULT '0',
  `propertyId` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`assetId`, `propertyId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
insert into `asset_property`(`assetId`, `propertyId`,`created`, `createdById`)
values (1, 1, 1, NOW());

DROP TABLE IF EXISTS `asset_transaction`;
CREATE TABLE  `asset_transaction` (
  `assetId` int(10) unsigned NOT NULL DEFAULT '0',
  `transactionId` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0001-01-01 00:00:00',
  `createdById` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`assetId`, `transactionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
insert into `asset_transaction`(`assetId`, `transactionId`,`created`, `createdById`)
values (1, 1, 1, NOW());