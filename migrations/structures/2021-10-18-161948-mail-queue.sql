CREATE TABLE `mail_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `lastTry` datetime DEFAULT NULL,
  `send` datetime DEFAULT NULL,
  `failed` int(11) NOT NULL DEFAULT 0,
  `subject` text COLLATE utf8_czech_ci NOT NULL,
  `fromMail` text COLLATE utf8_czech_ci NOT NULL,
  `fromName` text COLLATE utf8_czech_ci NOT NULL,
  `to` text COLLATE utf8_czech_ci NOT NULL,
  `cc` text COLLATE utf8_czech_ci NOT NULL,
  `bcc` text COLLATE utf8_czech_ci NOT NULL,
  `htmlbody` text COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;