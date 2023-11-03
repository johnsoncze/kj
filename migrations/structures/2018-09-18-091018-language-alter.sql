ALTER TABLE `language`
ADD INDEX (`lan_prefix`),
ADD INDEX (`lan_default`),
ADD INDEX (`lan_active`);