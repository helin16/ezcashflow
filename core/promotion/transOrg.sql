ALTER TABLE `transaction` ADD `organizationId` INT(10) NOT NULL DEFAULT '0' AFTER `id`, ADD INDEX `organizationId` (`organizationId`) ;
update transaction set organizationIdm = 1024;