-- #!mysql
-- #{ gewinum.authsystem
-- #  { init
CREATE TABLE IF NOT EXISTS `authsystem` (
    `Id` INT(11) NOT NULL,
    `Name` VARCHAR(255) NOT NULL DEFAULT '' COLLATE 'utf8mb4_general_ci',
    `Password` VARCHAR(255) NULL DEFAULT '' COLLATE 'utf8mb4_general_ci',
    `Xuid` VARCHAR(255) NULL DEFAULT '' COLLATE 'utf8mb4_general_ci',
    PRIMARY KEY (`Id`) USING BTREE
);
-- #  }
-- #  { getuser
-- #    :username string
SELECT
    Id,
    Name,
    Password,
    Xuid
FROM authsystem WHERE Name=:username;
-- #  }
-- #  { createuser
-- #    :username string
INSERT INTO authsystem(
    Id,
    Name,
    Password,
    Xuid
) VALUE (
    null,
    :username,
    null,
    null
);
-- #  }
-- #  { setpassword
-- #     :username string
-- #     :password string
UPDATE authsystem SET Password = :password WHERE Name = :username;
-- #  }
-- #  { setxuid
-- #     :username string
-- #     :xuid string
UPDATE authsystem SET Xuid = :xuid WHERE Name = :username;
-- #  }
-- #}