ALTER TABLE `forgotten_password`
ADD INDEX (`fp_hash`),
ADD INDEX (`fp_validity_date`);