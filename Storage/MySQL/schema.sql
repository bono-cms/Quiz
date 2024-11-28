
/* Categories */
DROP TABLE IF EXISTS `bono_module_quiz_categories`;
CREATE TABLE `bono_module_quiz_categories` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `lang_id` INT NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Category name',
  `order` INT NOT NULL COMMENT 'Category sorting order',
  `mark` FLOAT NOT NULL COMMENT 'Optional mark for attached questions',
  `limit` INT COMMENT 'Optional listing limit for questions',

  FOREIGN KEY (lang_id) REFERENCES bono_module_cms_languages(id) ON DELETE CASCADE
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;

/* Questions */
DROP TABLE IF EXISTS `bono_module_quiz_questions`;
CREATE TABLE `bono_module_quiz_questions` (
  
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `category_id` INT NOT NULL,
  `question` varchar(255) NOT NULL COMMENT 'Question',
  `order` INT NOT NULL COMMENT 'Sorting order (for non-random mode)',
  `description` LONGTEXT NOT NULL COMMENT 'Optional description for the question',

  FOREIGN KEY (category_id) REFERENCES bono_module_quiz_categories(id) ON DELETE CASCADE

) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;

/* Answers */
DROP TABLE IF EXISTS `bono_module_quiz_answers`;
CREATE TABLE `bono_module_quiz_answers` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `question_id` INT NOT NULL,
  `answer` LONGTEXT NOT NULL,
  `order` INT NOT NULL COMMENT 'Sorting order',
  `correct` BOOLEAN NOT NULL COMMENT 'Whether the answer is corrent. Possible values are 0 and 1',

  FOREIGN KEY (question_id) REFERENCES bono_module_quiz_questions(id) ON DELETE CASCADE  
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;

/* History */
DROP TABLE IF EXISTS `bono_module_quiz_history`;
CREATE TABLE `bono_module_quiz_history` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL COMMENT 'Unique slug',
  `category` varchar(30) NOT NULL COMMENT 'Category name',
  `name` varchar(30) COMMENT 'User name',
  `points` INT NOT NULL COMMENT 'Total points',
  `timestamp` INT(10) NOT NULL,
  `content` TEXT NOT NULL COMMENT 'Meta data'
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;

/* Session track */
DROP TABLE IF EXISTS `bono_module_quiz_session`;
CREATE TABLE `bono_module_quiz_session` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `started` varchar(255) NOT NULL,
    `finished` varchar(255)
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;

DROP TABLE IF EXISTS `bono_module_quiz_session_track`;
CREATE TABLE `bono_module_quiz_session_track` (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `session_id` INT NOT NULL COMMENT 'Session ID',
    `category` TEXT NOT NULL COMMENT 'Category name',
    `question` TEXT NOT NULL COMMENT 'Question',
    `answers` LONGTEXT NOT NULL COMMENT 'JSON representation of answers',

    FOREIGN KEY (session_id) REFERENCES bono_module_quiz_session(id) ON DELETE CASCADE  
) DEFAULT CHARSET=UTF8 ENGINE = InnoDB;