-- SQL CODE FOR AGGREGATOR DATABASE

CREATE DATABASE `aggregator`;

USE `aggregator`;

CREATE TABLE `users` (
    `user_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_username` VARCHAR(50) NOT NULL,
    `user_email` VARCHAR(256) NOT NULL,
    `user_password` VARCHAR(256) NOT NULL,
    `user_joined` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=INNODB;

CREATE TABLE `user_sessions` (
    `session_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `session_user` INT NOT NULL,
    `session_token` VARCHAR(256) UNIQUE NOT NULL,
    `user_agent` TEXT NOT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME NOT NULL,
    FOREIGN KEY (`session_user`) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=INNODB;

CREATE TABLE `remember_tokens` (
    `remember_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `remember_user` INT NOT NULL,
    `selector` VARCHAR(32) NOT NULL UNIQUE,
    `hashed_validator` VARCHAR(256) NOT NULL,
    `user_agent` TEXT NULL,
    `ip_address` VARCHAR(45) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expires_at` DATETIME NOT NULL,
    FOREIGN KEY (`remember_user`) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=INNODB;

CREATE TABLE `categories` (
    `category_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `category_name` VARCHAR(50) NOT NULL,
    `category_description` VARCHAR(500),
    `category_created` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `category_by` INT(8) NOT NULL
) ENGINE=INNODB;

CREATE TABLE `follows` (
    `follow_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `follow_user` INT NOT NULL,
    `follow_category` INT NOT NULL
) ENGINE=INNODB;

CREATE TABLE `posts` (
    `post_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `post_title` VARCHAR(256) NOT NULL,
    `post_url` VARCHAR(256),
    `post_text` VARCHAR(5000),
    `post_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `post_category` INT NOT NULL,
    `post_by` INT NOT NULL
) ENGINE=INNODB;

CREATE TABLE `comments` (
    `comment_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `comment_text` VARCHAR(5000) NOT NULL,
    `comment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `comment_post` INT NOT NULL,
    `comment_by` INT NOT NULL
) ENGINE=INNODB;

CREATE TABLE `points` (
    `point_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `point_user` INT NOT NULL,
    `point_post` INT NOT NULL
) ENGINE=INNODB;