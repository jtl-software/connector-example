CREATE TABLE IF NOT EXISTS `example_connector_db`.`mapping`
(
    `endpoint` INT NOT NULL AUTO_INCREMENT,
    `host`     INT NOT NULL,
    `type`     INT NOT NULL,
    PRIMARY KEY (`endpoint`)
);

CREATE TABLE IF NOT EXISTS `example_connector_db`.`categories`
(
    `id`        INT     NOT NULL AUTO_INCREMENT,
    `parent_id` INT     NULL,
    `status`    TINYINT NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `example_connector_db`.`category_translations`
(
    `id`               INT          NOT NULL AUTO_INCREMENT,
    `category_id`      INT          NOT NULL,
    `name`             VARCHAR(255) NOT NULL,
    `description`      TEXT         NULL,
    `title_tag`        VARCHAR(255) NULL,
    `meta_description` VARCHAR(255) NULL,
    `meta_keywords`    VARCHAR(255) NULL,
    `language_iso`     VARCHAR(3)   NULL,
    PRIMARY KEY (`id`)
);
