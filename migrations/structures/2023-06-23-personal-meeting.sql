CREATE TABLE personal_meeting (
  pm_id int(11) NULL,
  pm_name varchar(255) COLLATE utf8_czech_ci NOT NULL,
  pm_surname varchar(255) COLLATE utf8_czech_ci NOT NULL,
  pm_email varchar(255) COLLATE utf8_czech_ci NOT NULL,
  pm_phone varchar(255) COLLATE utf8_czech_ci NOT NULL,
  pm_preffered_date varchar(255) COLLATE utf8_czech_ci DEFAULT NULL,
  pm_note text COLLATE utf8_czech_ci,
  pm_nl_consent int(1) NOT NULL DEFAULT '0',
  pm_created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

ALTER TABLE personal_meeting
  ADD PRIMARY KEY (pm_id);

ALTER TABLE personal_meeting
  MODIFY pm_id int(11) NOT NULL AUTO_INCREMENT;
COMMIT;