-- ============================================================
--  eSmart Database
-- ============================================================
CREATE DATABASE IF NOT EXISTS `esmart` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `esmart`;

-- ── ADMIN ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admin` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `full_name`  VARCHAR(100) NOT NULL,
  `email`      VARCHAR(100) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ── COURSE ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `course` (
  `course_id`   INT(11) NOT NULL AUTO_INCREMENT,
  `course_name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `credits`     INT(11) NOT NULL DEFAULT 1,
  `price`       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `image`       VARCHAR(255) DEFAULT NULL,
  `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ── TEACHER ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `teacher` (
  `teacher_id`   INT(11) NOT NULL AUTO_INCREMENT,
  `full_name`    VARCHAR(100) NOT NULL,
  `email`        VARCHAR(100) NOT NULL,
  `phone_number` VARCHAR(20) DEFAULT NULL,
  `course_id`    INT(11) NOT NULL,
  `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`teacher_id`),
  UNIQUE KEY `email` (`email`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ── LEARNER ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `learner` (
  `learner_id`  INT(11) NOT NULL AUTO_INCREMENT,
  `full_name`   VARCHAR(100) NOT NULL,
  `email`       VARCHAR(100) NOT NULL,
  `password`    VARCHAR(255) NOT NULL,
  `date_joined` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`learner_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ── ENROLLMENT ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `enrollment` (
  `enrollment_id` INT(11) NOT NULL AUTO_INCREMENT,
  `learner_id`    INT(11) NOT NULL,
  `course_id`     INT(11) NOT NULL,
  `status`        ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `enrolled_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`enrollment_id`),
  UNIQUE KEY `unique_enrollment` (`learner_id`,`course_id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ── CONTACT MESSAGES ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `contact_messages` (
  `id`         INT(11) NOT NULL AUTO_INCREMENT,
  `full_name`  VARCHAR(100) NOT NULL,
  `email`      VARCHAR(100) NOT NULL,
  `message`    TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;