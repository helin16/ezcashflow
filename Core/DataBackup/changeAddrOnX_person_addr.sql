ALTER TABLE `x_person_address` CHANGE COLUMN `adressId` `addressId` INT(4) UNSIGNED NOT NULL DEFAULT 0,
 DROP INDEX `adressId`,
 ADD INDEX `addressId` USING BTREE(`addressId`);