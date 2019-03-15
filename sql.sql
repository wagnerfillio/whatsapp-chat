CREATE DATABASE IF NOT EXISTS `whats_clone`;
use whats_clone;
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `email` varchar(32) NOT NULL,
  `user_type` varchar(32),
  `number` varchar(32) NOT NULL DEFAULT '+12345678910',
  `pic` varchar(32) NOT NULL DEFAULT '1.jpg',
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE=InnoDB
DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `username`, `email`, `user_type`, `pic`, `password`) VALUES (1, 'wagner', 'wagner.fillio@gmail.com', 'admin', '1.jpg', 'e10adc3949ba59abbe56e057f20f883e');
INSERT INTO `users` (`id`, `username`, `email`, `user_type`, `pic`, `password`) VALUES (2, 'anish', 'anish@gmail.com', 'admin', '2.png', 'e10adc3949ba59abbe56e057f20f883e');


use whats_clone;
DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
	`sender` INT(11) NOT NULL,
	`recvId` INT(11) NOT NULL,
	`body` TEXT NOT NULL,	
	`time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`status` TINYINT(1) NOT NULL DEFAULT 0,	
	`recvIsGroup` TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`))
ENGINE=InnoDB
DEFAULT CHARSET=utf8;

INSERT INTO `message` (`id`, `sender`, `recvId`, `body`, `time`, `status`, `recvIsGroup`) VALUES ( 1, 1, 2, 'Hello Ghosh', '2019-03-08 17:00:00', 2, false);
INSERT INTO `message` (`id`, `sender`, `recvId`, `body`, `time`, `status`, `recvIsGroup`) VALUES ( 2, 2, 1, 'Hey Wagner', '2019-03-08 17:00:00', 1, false);

DROP TABLE IF EXISTS `last_seen`;
CREATE TABLE IF NOT EXISTS `last_seen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE=InnoDB
DEFAULT CHARSET=utf8;


INSERT INTO `last_seen` (`id`, `user_id`, `message_id`) VALUES (1, 1, 1);
