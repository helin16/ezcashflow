insert into state(`name`, `countryId`, `active`, `created`, `createdById`, `updated`, `updatedById`)
values
('ACT', 1, 1, NOW(), 1, NOW(), 1),
('NSW', 1, 1, NOW(), 1, NOW(), 1),
('VIC', 1, 1, NOW(), 1, NOW(), 1),
('SA', 1, 1, NOW(), 1, NOW(), 1),
('WA', 1, 1, NOW(), 1, NOW(), 1),
('TAS', 1, 1, NOW(), 1, NOW(), 1),
('NT', 1, 1, NOW(), 1, NOW(), 1);

insert into country(`name`, `active`, `created`, `createdById`, `updated`, `updatedById`)
values('Australia', 1, NOW(), 1, NOW(), 1);

insert into address(`line1`, `line2`, `suburb`, `postCode`, `stateId`, `countryId`, `active`, `created`, `createdById`, `updated`, `updatedById`)
values('test line 1', 'test line 2', 'test suburb', 'test postCode', 1, 1, 1, NOW(), 1, NOW(), 1);