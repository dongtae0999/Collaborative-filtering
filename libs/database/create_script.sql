CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nickname` varchar(255) NOT NULL,
  `password` varchar(60) NOT NULL,
  `role` varchar(20),
  `genre1` varchar(60) NOT NULL,
  `genre2` varchar(60),
  `genre3` varchar(60)
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

CREATE TABLE `songs` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(60) NOT NULL,
  `artist` varchar(60) NOT NULL,
  `album`  varchar(60) NOT NULL,
  `img_url` varchar(300) NOT NULL,
  `genre` varchar(60) NOT NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

CREATE TABLE `rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(11) NOT NULL,
  `song_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  FOREIGN KEY (`song_id`) REFERENCES `songs` (`id`)
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';

CREATE TABLE `pearson` (
  `user_id` int(11) NOT NULL,
  `best_match` int(11) NOT NULL
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_general_ci';