CREATE TABLE `users` (
    `id`    int(10)     unsigned NOT NULL AUTO_INCREMENT,
    `name`  varchar(70)          NOT NULL,
    `email` varchar(70)          NOT NULL,
    `password` TEXT              NOT NULL,
    `status`  int(10)            NOT NULL

    PRIMARY KEY (`id`)
);
