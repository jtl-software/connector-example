CREATE TABLE IF NOT EXISTS `example_connector_db`.`mapping`
(
    `endpoint` INT NOT NULL AUTO_INCREMENT,
    `host`     INT NOT NULL,
    `type`     INT NOT NULL,
    PRIMARY KEY (`endpoint`)
);