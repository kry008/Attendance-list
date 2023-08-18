CREATE TABLE IF NOT EXISTS `dniwolne` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `data` date NOT NULL,
    `nazwaSwieta` text NOT NULL,
    `aktywne` int(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `dzialy` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `skrot` varchar(25) NOT NULL,
    `nazwa` text NOT NULL,
    `aktywne` int(11) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `skrot` (`skrot`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `logi` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `log` text NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `obecnosc` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `kto` int(10) UNSIGNED NOT NULL,
    `data` date NOT NULL,
    `czasZaczecia` time DEFAULT NULL,
    `czasKonca` time DEFAULT NULL,
    `status` int(10) UNSIGNED NOT NULL,
    `zaakceptowane` tinyint(4) NOT NULL DEFAULT 0,
    `zdalne` tinyint(1) NOT NULL DEFAULT 0,
    `aktywne` tinyint(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `kto` (`kto`),
    KEY `status` (`status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `statusy` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `skrot` varchar(10) NOT NULL,
    `nazwa` text NOT NULL,
    `oznaczaWolne` tinyint(1) NOT NULL DEFAULT 0,
    `aktywne` tinyint(4) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `skrot` (`skrot`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

CREATE TABLE IF NOT EXISTS `uzytkownicy` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `login` text NOT NULL,
    `haslo` text NOT NULL,
    `imie` text NOT NULL,
    `nazwisko` text NOT NULL,
    `dzial` int(10) UNSIGNED NOT NULL,
    `przelozony` int(10) UNSIGNED DEFAULT NULL,
    `aktywne` int(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id`),
    KEY `dzial` (`dzial`),
    KEY `przelozony` (`przelozony`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

ALTER TABLE
    `obecnosc`
ADD
    CONSTRAINT `obecnosc_ibfk_1` FOREIGN KEY (`kto`) REFERENCES `uzytkownicy` (`id`),
ADD
    CONSTRAINT `obecnosc_ibfk_2` FOREIGN KEY (`status`) REFERENCES `statusy` (`id`);

ALTER TABLE
    `uzytkownicy`
ADD
    CONSTRAINT `uzytkownicy_ibfk_1` FOREIGN KEY (`dzial`) REFERENCES `dzialy` (`id`),
ADD
    CONSTRAINT `uzytkownicy_ibfk_2` FOREIGN KEY (`przelozony`) REFERENCES `uzytkownicy` (`id`);

INSERT INTO
    `dniwolne` (`id`, `data`, `nazwaSwieta`, `aktywne`)
VALUES
    (
        1,
        '2023-08-15',
        'Wniebowzięcie Najświętszej Maryi Panny',
        1
    );

INSERT INTO
    `statusy` (
        `id`,
        `skrot`,
        `nazwa`,
        `oznaczaWolne`,
        `aktywne`
    )
VALUES
    (1, 'OB', 'OBECNY/A', 0, 1),
    (2, 'UW', 'URLOP WYPOCZYNKOWY', 1, 1),
    (3, 'UO', 'URLOP OKOLICZNOŚCIOWY', 1, 1),
    (4, 'UR', 'URLOP REHABILITACYJNY', 1, 1),
    (5, 'OP', 'OPIEKA NAD DZIECKIEM', 1, 1),
    (6, 'D', 'DELEGACJA', 1, 1),
    (7, 'L4', 'ZWOLNIENIE LEKARSKIE - CHOROBA', 1, 1),
    (8, 'UB', 'URLOP BEZPŁATNY', 1, 1),
    (9, 'UŻ', 'URLOP NA ŻĄDANIE', 1, 1),
    (10, 'WŚ', 'WOLNE ZA ŚWIĘTO', 1, 1),
    (11, 'OPZ', 'Okazjonalna PZ', 0, 1);

CREATE TABLE `admini` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kto` INT UNSIGNED NOT NULL,
    `odKiedy` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;

INSERT INTO
    `admini` (`id`, `kto`, `odKiedy`)
VALUES
    (NULL, '1', CURRENT_TIMESTAMP)