drop index `username` on useraccount;
ALTER TABLE `useraccount` ADD UNIQUE(`username`);