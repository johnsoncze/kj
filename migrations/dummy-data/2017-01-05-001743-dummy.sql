INSERT INTO `user` (`u_id`, `u_name`, `u_email`, `u_password`, `u_add_date`)
VALUES (1, 'Dušan Mlynarčík', 'dusan.mlynarcik@email.cz', '$2y$10$yj9SwGG43Y0f8H/U3YrrNuKKSDURil7NWI5WWtU7Afc.UyXExt9J2', CURRENT_TIMESTAMP);

INSERT INTO `language` (`lan_id`, `lan_prefix`, `lan_name`, `lan_default`, `lan_active`, `lan_add_date`) VALUES
(1, 'cs', 'Čeština', NULL, NULL, '2017-01-04 23:11:32'),
(2, 'en', 'Angličtina', NULL, NULL, '2017-01-04 23:11:43');

INSERT INTO `article` (`art_id`, `art_language_id`, `art_name`, `art_url`, `art_title_seo`, `art_description_seo`, `art_cover_photo`, `art_introduction`, `art_content`, `art_status`, `art_add_date`, `art_update_date`) VALUES
(1, 1, 'Článek', 'url', NULL, NULL, NULL, 'Popis článku.', 'Obsah článku.', 'draft', '2017-01-04 23:12:01', '2017-01-04 23:12:27');

INSERT INTO `article_category` (`ac_id`, `ac_language_id`, `ac_name`, `ac_url`, `ac_title_seo`, `ac_description_seo`, `ac_add_date`) VALUES
(1, 1, 'Kategorie 1', 'kategorie-1', NULL, NULL, '2017-01-04 23:12:37'),
(2, 2, 'Kategorie 2', 'kategorie-2', NULL, NULL, '2017-01-04 23:12:49');
