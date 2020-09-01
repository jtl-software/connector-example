CREATE TABLE IF NOT EXISTS `mapping`
(
    `endpoint` INT NOT NULL AUTO_INCREMENT,
    `host`     INT NOT NULL,
    `type`     INT NOT NULL,
    PRIMARY KEY (`endpoint`)
);

CREATE TABLE IF NOT EXISTS `categories`
(
    `id`        INT     NOT NULL AUTO_INCREMENT,
    `parent_id` INT     NULL,
    `status`    TINYINT NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `category_translations`
(
    `id`               INT          NOT NULL AUTO_INCREMENT,
    `category_id`      INT          NOT NULL,
    `name`             VARCHAR(255) NOT NULL,
    `description`      TEXT         NULL,
    `title_tag`        VARCHAR(255) NULL,
    `meta_description` VARCHAR(255) NULL,
    `meta_keywords`    VARCHAR(255) NULL,
    `language_iso`     VARCHAR(2)   NULL,
    PRIMARY KEY (`id`)
);

ALTER TABLE `category_translations`
    ADD CONSTRAINT `fk_category_translations`
        FOREIGN KEY (`category_id`)
            REFERENCES `categories` (`id`)
            ON DELETE CASCADE
            ON UPDATE NO ACTION;
