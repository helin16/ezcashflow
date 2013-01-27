ALTER TABLE `accountentry` ADD `allowTrans` TINYINT NOT NULL DEFAULT '0' AFTER `rootId` ;
ALTER TABLE `accountentry` ADD INDEX ( `allowTrans` );

ALTER TABLE `accountentry` ADD `sum` deciaml(10,2) NOT NULL DEFAULT '0' AFTER `allowTrans`;
ALTER TABLE `accountentry` CHANGE `value` `value` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0' , CHANGE `budget` `budget` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0';
ALTER TABLE `transaction` CHANGE `value` `value` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0';


CREATE  TEMPORARY TABLE IF NOT EXISTS `parentids` as (
SELECT parent.id `id`
FROM `accountentry` parent 
left join `accountentry` child on (child.parentId = parent.id) 
where child.id is null);
update `accountentry` set allowTrans = 1 where id in (select id from `parentids`);
update `accountentry` set allowTrans = 0 where id NOT in (select id from `parentids`);