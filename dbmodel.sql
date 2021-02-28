ALTER TABLE `player`
  ADD `can_buy_gazette` BOOLEAN NOT NULL DEFAULT FALSE;

CREATE TABLE IF NOT EXISTS `board` (
  `id` int(2) PRIMARY KEY AUTO_INCREMENT,
  `collector_blue` int(2) unsigned NULL,
  `collector_yellow` int(2) unsigned NULL,
  `collector_green` int(2) unsigned NULL,
  `collector_pink` int(2) unsigned NULL,
  `ambroise` VARCHAR(10) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gazettes` (
  `id` int(2) PRIMARY KEY AUTO_INCREMENT,
  `value` int(2) unsigned NOT NULL,
  `nb_diff` int(2) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `deck_cards` (
  `id` int(2) PRIMARY KEY AUTO_INCREMENT,
  `deck_number` int(2) unsigned NOT NULL,
  `position` int(2) unsigned NOT NULL,
  `muse_value` int(2) unsigned NOT NULL,
  `muse_color` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `hands` (
  `id` int(2) PRIMARY KEY AUTO_INCREMENT,
  `player_id` int(2) unsigned NOT NULL,
  `muse_value` int(2) unsigned NOT NULL,
  `muse_color` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `paintings` (
  `id` int(2) PRIMARY KEY AUTO_INCREMENT,
  `player_id` int(2) unsigned NOT NULL,
  `muse_value` int(2) unsigned NOT NULL,
  `muse_color` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `discard_pile` (
  `id` int(2) PRIMARY KEY AUTO_INCREMENT,
  `muse_value` int(2) unsigned NOT NULL,
  `muse_color` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `attracted_collector` (
  `id` int(2) PRIMARY KEY AUTO_INCREMENT,
  `player_id` int(2) unsigned NOT NULL,
  `value` int(2) unsigned NOT NULL,
  `color` VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `events` (
  `id` int(2) PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `payload` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
