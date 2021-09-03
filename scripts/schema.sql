CREATE TABLE IF NOT EXISTS `mappings`
(
    `endpoint` VARBINARY(32) NOT NULL,
    `host`     INT        NOT NULL,
    `type`     INT        NOT NULL,
    PRIMARY KEY (`endpoint`, `type`)
);

CREATE TABLE IF NOT EXISTS `categories`
(
    `id`        VARBINARY(32) NOT NULL,
    `parent_id` VARBINARY(32) NULL,
    `status`    TINYINT    NOT NULL,
    PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `category_translations`
(
    `category_id`      VARBINARY(32)   NOT NULL,
    `language_iso`     VARCHAR(2)   NOT NULL,
    `name`             VARCHAR(255) NOT NULL,
    `description`      TEXT         NULL,
    `title_tag`        VARCHAR(255) NULL,
    `meta_description` VARCHAR(255) NULL,
    `meta_keywords`    VARCHAR(255) NULL,
    PRIMARY KEY (`category_id`, `language_iso`),
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
);
