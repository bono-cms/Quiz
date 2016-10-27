
DROP TABLE IF EXISTS `bono_module_quiz_categories`;
CREATE TABLE `bono_module_quiz_categories` (
  
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `lang_id` INT NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Category name',
  `order` INT NOT NULL COMMENT 'Category sorting order'
  
) DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_quiz_questions`;
CREATE TABLE `bono_module_quiz_questions` (
  
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `category_id` INT NOT NULL,
  `title` varchar(255) NOT NULL COMMENT 'Question title',
  `order` INT NOT NULL COMMENT 'Sorting order (for non-random mode)',
  
) DEFAULT CHARSET = UTF8;

DROP TABLE IF EXISTS `bono_module_quiz_answers`;
CREATE TABLE `bono_module_quiz_answers` (
  
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `question_id` INT NOT NULL,
  `name` TEXT NOT NULL,
  `order` INT NOT NULL COMMENT 'Sorting order'
  `correct` varchar(1) NOT NULL COMMENT 'Whether the answer is corrent. Possible values are 0 and 1'
  
) DEFAULT CHARSET = UTF8;
