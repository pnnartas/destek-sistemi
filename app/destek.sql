CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `salt` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `role` text,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `name`, `surname`, `username`, `email`, `salt`, `password`, `enabled`, `role`, `deleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ADMİN', 'ADMİN', NULL, 'admin@admin.com', 'g9ro51ar0w00c00cswco0s4cgokc8gg', 'FyPAfqjdK6E6mlfpBvD2PacwXMu1NqCJB8sYl6ZSR3IZfDdVYQMipFkboUqN/oGgrpq1EdLQi9uLI78tY/mjjg==', 1, 'ROLE_ADMIN', 0, '2015-11-07 12:19:58', NULL, NULL),
(2, 'USER', 'USER', NULL, 'user@user.com', 'lvlizmxr1z4wccs0ooko0sk8k80k8gw', 'TeFX/w9Z7FQeghKBRimlUeR7H3zDLk6BDuB1TvAR8fncZ9682hBuKUu3IhdxhsoTMZB+uuxBfgPjEi8xQ/INeA==', 1, 'ROLE_USER', 0, '2015-11-07 12:19:58', NULL, NULL);

CREATE TABLE category (
id INT AUTO_INCREMENT NOT NULL,
name varchar(50) NOT NULL,
deleted TINYINT(1) DEFAULT '0' NOT NULL,
deleted_at DATETIME DEFAULT NULL,
created_at DATETIME NOT NULL,
updated_at DATETIME DEFAULT NULL,
PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;


CREATE TABLE priority (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, deleted TINYINT(1) DEFAULT '0' NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE ticket_category (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, ticket_id INT NOT NULL, deleted TINYINT(1) DEFAULT '0' NOT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE ticket_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, deleted TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB

CREATE TABLE tickets (id INT AUTO_INCREMENT NOT NULL, recipient_id INT DEFAULT NULL, owner_user_id INT DEFAULT NULL, priority_id INT DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, message LONGTEXT DEFAULT NULL, status_id SMALLINT DEFAULT NULL, ip VARCHAR(50) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX track_id (track_id), INDEX recipient_id (recipient_id), INDEX owner_user_id (owner_user_id), INDEX priority_id (priority_id), INDEX deleted (deleted), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE ticket_replies (id INT AUTO_INCREMENT NOT NULL, ticket_id INT NOT NULL, reply_user_id INT NOT NULL, message LONGTEXT DEFAULT NULL, ip VARCHAR(50) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted TINYINT(1) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX ticket_id (ticket_id), INDEX reply_user_id (reply_user_id), INDEX deleted (deleted), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;



INSERT INTO `priority` (`id`, `name`, `deleted`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Düşük', 0, NULL, '2015-11-10 00:00:00', NULL),
(2, 'Orta', 0, NULL, '2015-11-10 00:00:00', NULL),
(3, 'Yüksek', 0, NULL, '2015-11-10 00:00:00', NULL),
(4, 'Kritik', 0, NULL, '2015-11-10 00:00:00', NULL);


INSERT INTO `ticket_status` (`id`, `name`, `deleted`, `created_at`) VALUES
(1, 'Açık', 0, '2015-11-11 00:00:00'),
(2, 'Çözüldü', 0, '2015-11-11 00:00:00');


INSERT INTO `category` (`id`, `name`, `deleted`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 'Destek', 0, NULL, '2015-11-10 15:58:25', NULL),
(2, 'Satış', 0, NULL, '2015-11-10 15:58:25', NULL),
(3, 'Muhasebe', 0, NULL, '2015-11-10 15:58:40', NULL);

