/*
SQLyog Ultimate v10.00 Beta1
MySQL - 5.5.5-10.4.32-MariaDB : Database - studentoverflow
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`studentoverflow` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `studentoverflow`;

/*Table structure for table `comment_kudos` */

DROP TABLE IF EXISTS `comment_kudos`;

CREATE TABLE `comment_kudos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(10) unsigned NOT NULL,
  `giver_user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_kudos` (`comment_id`,`giver_user_id`),
  KEY `fk_ck_giver` (`giver_user_id`),
  CONSTRAINT `fk_ck_comment` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ck_giver` FOREIGN KEY (`giver_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `comment_kudos` */

insert  into `comment_kudos`(`id`,`comment_id`,`giver_user_id`,`created_at`) values (1,1,16,'2025-11-06 15:39:58'),(2,2,16,'2025-11-12 08:09:25');

/*Table structure for table `comment_likes` */

DROP TABLE IF EXISTS `comment_likes`;

CREATE TABLE `comment_likes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_comment_user` (`comment_id`,`user_id`),
  KEY `fk_cl_user` (`user_id`),
  CONSTRAINT `fk_cl_comment` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cl_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `comment_likes` */

insert  into `comment_likes`(`id`,`comment_id`,`user_id`,`created_at`) values (1,1,16,'2025-11-06 15:39:57');

/*Table structure for table `comments` */

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_c_post` (`post_id`),
  KEY `fk_c_user` (`user_id`),
  KEY `fk_c_parent` (`parent_id`),
  CONSTRAINT `fk_c_parent` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_c_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_c_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `comments` */

insert  into `comments`(`id`,`post_id`,`user_id`,`content`,`parent_id`,`created_at`,`updated_at`) values (1,7,16,'*Insert useful advice*',NULL,'2025-11-06 15:39:49',NULL),(2,7,3,'Nice Messi image!!!!',NULL,'2025-11-11 18:42:35',NULL),(3,3,3,'Testing',NULL,'2025-11-11 18:43:42',NULL),(4,3,3,'Testing',NULL,'2025-11-11 18:43:47',NULL);

/*Table structure for table `contact_messages` */

DROP TABLE IF EXISTS `contact_messages`;

CREATE TABLE `contact_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `contact_messages` */

insert  into `contact_messages`(`id`,`name`,`email`,`subject`,`message`,`created_at`) values (1,'Hau','tonytech0909@gmail.com','haha','haha','2025-11-04 17:08:01'),(2,'Hau','hau.trungnguyen1709@gmail.com','23','123','2025-11-04 17:08:37'),(3,'Hau','hau.trungnguyen1709@gmail.com','23','123','2025-11-04 17:09:19'),(4,'Hau','hau.trungnguyen1709@gmail.com','23','123','2025-11-04 17:09:20'),(5,'Hau','hau.trungnguyen1709@gmail.com','23','123','2025-11-04 17:09:26'),(6,'Hau','hau.trungnguyen1709@gmail.com','123','123','2025-11-04 17:09:44'),(7,'Hau','hau.trungnguyen1709@gmail.com','123','123','2025-11-04 17:12:05'),(8,'123','tonytech0909@gmail.com','haha','123','2025-11-05 16:08:06'),(9,'123','tonytech0909@gmail.com','haha','123','2025-11-05 16:10:03'),(10,'123','tonytech0909@gmail.com','haha','123','2025-11-05 16:10:08'),(11,'hau','tonytech0909@gmail.com','123','123','2025-11-05 16:22:57'),(12,'hau','tonytech0909@gmail.com','123','123','2025-11-05 16:26:35'),(13,'hau','tonytech0909@gmail.com','asd','wqe','2025-11-05 16:26:48'),(14,'hau','tonytech0909@gmail.com','asd','wqe','2025-11-06 13:52:14'),(15,'Hau','tonytech0909@gmail.com','123','23','2025-11-06 14:00:26'),(16,'hau','tonytech0909@gmail.com','123','23','2025-11-06 14:07:03'),(17,'admin','hau.trungnguyen1709@gmail.com','123','123','2025-11-06 14:18:05'),(18,'admin','hau.trungnguyen1709@gmail.com','123','123','2025-11-06 14:18:34'),(19,'hau','tonytech0909@gmail.com','123','123','2025-11-06 14:19:49'),(20,'hau','tonytech0909@gmail.com','123','123','2025-11-06 14:23:06'),(21,'hau','tonytech0909@gmail.com','123','23','2025-11-06 14:25:06'),(22,'thayho','thayho@gmail.com','123','123','2025-11-12 08:06:10');

/*Table structure for table `modules` */

DROP TABLE IF EXISTS `modules`;

CREATE TABLE `modules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(120) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `modules` */

insert  into `modules`(`id`,`code`,`name`,`created_at`) values (1,'COMP1752','Object-Oriented Programming','2025-11-01 09:53:34'),(2,'COMP1843','Principles of Security','2025-11-01 09:53:34');

/*Table structure for table `post_images` */

DROP TABLE IF EXISTS `post_images`;

CREATE TABLE `post_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_size` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_images_post` (`post_id`),
  CONSTRAINT `fk_images_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `post_images` */

insert  into `post_images`(`id`,`post_id`,`file_name`,`mime_type`,`file_size`,`created_at`) values (4,7,'20251106_083632_1d5f271e0593d179.jpg','image/jpeg',162346,'2025-11-06 14:36:32');

/*Table structure for table `post_upvotes` */

DROP TABLE IF EXISTS `post_upvotes`;

CREATE TABLE `post_upvotes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_post_user` (`post_id`,`user_id`),
  KEY `fk_pu_user` (`user_id`),
  CONSTRAINT `fk_pu_post` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pu_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `post_upvotes` */

insert  into `post_upvotes`(`id`,`post_id`,`user_id`,`created_at`) values (1,7,16,'2025-11-06 15:22:35'),(4,7,3,'2025-11-29 20:30:54');

/*Table structure for table `posts` */

DROP TABLE IF EXISTS `posts`;

CREATE TABLE `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `module_id` int(10) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_posts_user` (`user_id`),
  KEY `idx_posts_module` (`module_id`),
  CONSTRAINT `fk_posts_module` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_posts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `posts` */

insert  into `posts`(`id`,`title`,`content`,`user_id`,`module_id`,`created_at`,`updated_at`) values (3,'Hello world post','Testing joins & list view',16,1,'2025-11-01 09:54:08','2025-11-29 18:39:59'),(7,'123123','Why is Messi so good looking?',16,1,'2025-11-06 14:36:32','2025-11-29 18:39:59'),(10,'123','123',16,1,'2025-11-29 18:21:29','2025-11-29 18:39:59'),(11,'qwe','qwe',16,1,'2025-11-29 18:22:22','2025-11-29 18:39:59');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`username`,`email`,`password_hash`,`role`,`is_system`,`created_at`) values (3,'admin','hau.trungnguyen1709@gmail.com','$2y$10$T.mQArh8KJfoyeZzSMX5zOTKe/6Hm.bQ8Rrt2Z5Y.2gJamFWPvk3.','admin',0,'2025-11-04 16:54:28'),(15,'zm','zm@gmail.com','$2y$10$9a6gWWoByTvgSjvaN8tKKOD4gvmHseNb/6ysGVEFZKn1r23bdvSd6','student',0,'2025-11-29 18:32:23'),(16,'deleted_account','deleted@local.invalid','$2y$10$NKQwtHS7Nj.z68fVW0otr.vwkG2brwwodqilS8MsgzTmtJoUoXEDa','admin',0,'2025-11-29 18:39:59');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
