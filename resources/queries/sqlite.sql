-- #!mysql
-- #{ gewinum.authsystem
-- #  { init
CREATE TABLE IF NOT EXISTS `authsystem` (
                              `Id` INTEGER PRIMARY KEY AUTOINCREMENT,
                              `Name` VARCHAR(255) NOT NULL DEFAULT '',
                              `Password` VARCHAR(255) NULL DEFAULT '',
                              `Xuid` VARCHAR(255) NULL DEFAULT ''
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
INSERT INTO authsystem (
    Id,
    Name,
    Password,
    Xuid
) VALUES (
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