CREATE TABLE IF NOT EXISTS `board` (
  `id` int(2) PRIMARY KEY AUTO_INCREMENT,
  `collector_blue` int(2) unsigned NOT NULL,
  `collector_yellow` int(2) unsigned NOT NULL,
  `collector_green` int(2) unsigned NOT NULL,
  `collector_pink` int(2) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gazettes` (
  `id` int(2) PRIMARY KEY AUTO_INCREMENT,
  `board_id` int(2) unsigned NOT NULL,
  `value` int(2) unsigned NOT NULL,
  `nb_diff` int(2) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
