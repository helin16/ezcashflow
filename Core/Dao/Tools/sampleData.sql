INSERT INTO `useraccount` (`id` ,`active` ,`created` ,`updated` ,`CreatedById` ,`UpdatedById` ,`UserName` ,`Password` ,`PersonId`)VALUES (1 , '1', NOW( ) , NOW( ) , '1', '1', 'admin', SHA1( 'admin' ) , '1');

INSERT INTO `role` (`id` ,`active` ,`created` ,`updated` ,`CreatedById` ,`UpdatedById` ,`Name`)VALUES ('1', '1', NOW( ) , NOW( ) , '1', '1', 'Administrator');

INSERT INTO `person` (`id` ,`active` ,`created` ,`updated` ,`CreatedById` ,`UpdatedById` ,`FirstName` ,`LastName`)VALUES ('1', '1', NOW( ) , NOW( ) , '1', '1', 'admin', 'system');

INSERT INTO `useraccount_roles_role_useraccounts`  (`RolesId` ,`UserAccountsId`)VALUES ('1', '1');

INSERT INTO `accountentry` (`id` ,`active` ,`created` ,`updated` ,`CreatedById` ,`UpdatedById` ,`name` ,`accountNumber` ,`comments` ,`value` ,`parentId`,`rootId`)VALUES (1 , '1', NOW( ) , NOW( ) , '1', '1', 'Assets', '1', 'This the parent for all bank accounts.', '0.0', '0','1');
INSERT INTO `accountentry` (`id` ,`active` ,`created` ,`updated` ,`CreatedById` ,`UpdatedById` ,`name` ,`accountNumber` ,`comments` ,`value` ,`parentId`,`rootId`)VALUES (2 , '1', NOW( ) , NOW( ) , '1', '1', 'Liablity', '2', 'This the parent for all Expense.', '0.0', '0','2');
INSERT INTO `accountentry` (`id` ,`active` ,`created` ,`updated` ,`CreatedById` ,`UpdatedById` ,`name` ,`accountNumber` ,`comments` ,`value` ,`parentId`,`rootId`)VALUES (3 , '1', NOW( ) , NOW( ) , '1', '1', 'Income', '3', 'This the parent for all Income.', '0.0', '0','3');
INSERT INTO `accountentry` (`id` ,`active` ,`created` ,`updated` ,`CreatedById` ,`UpdatedById` ,`name` ,`accountNumber` ,`comments` ,`value` ,`parentId`,`rootId`)VALUES (4 , '1', NOW( ) , NOW( ) , '1', '1', 'Expense', '4', 'This the parent for all Expense.', '0.0', '0','4');