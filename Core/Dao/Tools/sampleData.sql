INSERT INTO `useraccount` (`id` ,`active` ,`created` ,`updated` ,`CreatedById` ,`UpdatedById` ,`UserName` ,`Password` ,`PersonId`)VALUES (1 , '1', NOW( ) , NOW( ) , '1', '1', 'admin', SHA1( 'admin' ) , '1');

INSERT INTO `role` (`id` ,`active` ,`created` ,`updated` ,`CreatedById` ,`UpdatedById` ,`Name`)VALUES ('1', '1', NOW( ) , NOW( ) , '1', '1', 'Administrator');

INSERT INTO `person` (`id` ,`active` ,`created` ,`updated` ,`CreatedById` ,`UpdatedById` ,`FirstName` ,`LastName`)VALUES ('1', '1', NOW( ) , NOW( ) , '1', '1', 'admin', 'system');

INSERT INTO `useraccount_roles_role_useraccounts`  (`RolesId` ,`UserAccountsId`)VALUES ('1', '1');

INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(1, 1, NOW(), NOW(), 1, 1, 'Assets', 1, 'This the parent for all bank accounts.', '0.0', 0, 1);
INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(2, 1, NOW(), NOW(), 1, 1, 'Liablity', 2, 'This the parent for all Expense.', '', NULL, 2);
INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(3, 1, NOW(), NOW(), 1, 1, 'Income', 3, 'This the parent for all Income.', '', NULL, 3);
INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(4, 1, NOW(), NOW(), 1, 1, 'Expense', 4, 'This the parent for all Expense.', '', NULL, 4);
INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(5, 1, NOW(), NOW(), 1, 1, 'Ignite Master Card', 20001, '', '236.54', 2, 2);
INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(6, 1, NOW(), NOW(),  1, 1, 'AMEX', 20002, '', '416.73', 2, 2);
INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(7, 1, NOW(), NOW(),  1, 1, 'Zhen''s Wages', 30001, '', '0.00', 3, 3);
INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(8, 1, NOW(), NOW(), 1, 1, 'Entertaining', 40001, '', '30.5', 4, 4);
INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(9, 1, NOW(), NOW(), 1, 1, 'Lin''s Salaries', 30002, '', '0.0', 3, 3);
INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(10, 1, NOW(), NOW(), 1, 1, 'Rental Income', 30003, '', '0.0', 3, 3);
INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(11, 1, NOW(), NOW(), 1, 1, 'Daily Expenses', 40002, '', '0.0', 4, 4);
INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(12, 1, NOW(), NOW(), 1, 1, 'Utiltiy Expenses', 40003, '', '0.0', 4, 4);
INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(13, 1, NOW(), NOW(), 1, 1, 'Zhao Cai', 40004, '', '0.0', 4, 4);
INSERT INTO `accountentry` (`id`, `active`, `created`, `updated`, `createdById`, `updatedById`, `name`, `accountNumber`, `comments`, `value`, `parentId`, `rootId`) VALUES(14, 1, NOW(), NOW(), 1, 1, 'Maya', 40005, '', '0.0', 4, 4);