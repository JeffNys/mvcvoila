/**
 * cleanup tables
 */

DROP TABLE IF EXISTS `item`;

/**
 * create the table item
 */

CREATE TABLE
    `item` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `title` VARCHAR(100) NOT NULL,
        PRIMARY KEY (`id`)
    );

/**
 * create content for item TABLE
 */

INSERT INTO `item` (`title`) VALUES ('foo'), ('bar');