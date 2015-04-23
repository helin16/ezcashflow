INSERT INTO `person` (`id`, `email`, `firstName`, `lastName`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
	(1,'', '', 'User', 1, '2014-03-06 19:47:35', 42, '2014-03-25 06:45:56', 42),
	(10,'test@test.com', 'Test', 'User', 0, '2014-03-06 19:47:35', 42, '2014-03-25 06:45:56', 42),
	(42,'System acc', 'System', 'User', 1, '2014-03-06 19:47:35', 42, '2014-03-25 06:45:56', 42);

INSERT INTO `useraccount` (`id`, `username`, `password`, `personId`,  `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
	(1,'Guest Only','no working', 1, 1, '2014-03-06 19:47:35', 42, '2014-03-25 06:45:56', 42),
	(10,'test@test.com', 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3', 10, 0, '2014-03-06 19:47:35', 42, '2014-03-25 06:45:56', 42),
	(42,'System acc','system acc, no login', 1, '2014-03-06 19:47:35', 42, 42, '2014-03-25 06:45:56', 42);

INSERT INTO `role` (`id`, `name`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
	(10,'admin', 1, '2014-03-06 19:47:35', 42, '2014-03-25 06:45:56', 42),
	(11,'bookkeeper', 1, '2014-03-06 19:47:35', 42, '2014-03-25 06:45:56', 42);

INSERT INTO `accounttype` (`id`, `name`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
	(1,'Assets', 1, '2014-03-06 19:47:35', 42, '2014-03-25 06:45:56', 42),
	(2,'Liability', 1, '2014-03-06 19:47:35', 42, '2014-03-25 06:45:56', 42),
	(3,'Income', 1, '2014-03-06 19:47:35', 42, '2014-03-25 06:45:56', 42),
	(4,'Expense', 1, '2014-03-06 19:47:35', 42, '2014-03-25 06:45:56', 42);

INSERT INTO `organization` (`id`, `name`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
	(1024, 'Lin \'s BookKeeping', 1, '2014-03-06 19:47:35', 42, '2014-03-25 06:45:56', 42);

INSERT INTO `orgpersonrole` (`personId`, `organizationId`, `roleId`, `active`, `created`, `createdById`, `updated`, `updatedById`) VALUES
	(10, 1024, 10, 1, '2014-03-06 19:47:35', 42, '2014-03-25 06:45:56', 42);

