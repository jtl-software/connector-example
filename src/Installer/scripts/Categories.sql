CREATE TABLE IF NOT EXISTS `example_connector_db`.`categories`
(
    `id`        INT          NOT NULL AUTO_INCREMENT,
    `name`      VARCHAR(255) NOT NULL,
    `parent_id` INT          NULL,
    `status`    TINYINT      NOT NULL,
    PRIMARY KEY (`id`)
);