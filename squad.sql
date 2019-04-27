-- ----
-- USER
-- ----
CREATE TABLE USERS
(
  `UserName`  VARCHAR(30) NOT NULL PRIMARY KEY,
  `FirstName` VARCHAR(255),
  `LastName`  VARCHAR(255),
  `Email`     VARCHAR(255),
  `Password`  VARCHAR(40),
  `Token`  	  VARCHAR(255),
  `About`     TEXT
);




-- -------
-- FRIENDS
-- -------
CREATE TABLE FRIENDS
(
  `User`    VARCHAR(30),
  `Friend`  VARCHAR(30),
  FOREIGN KEY (`User`) REFERENCES USERS(`Username`),
  FOREIGN KEY (`Friend`) REFERENCES USERS(`Username`),
  PRIMARY KEY (`User`, `Friend`)
);

CREATE TABLE FRIEND_REQUESTS
(
  `From_User` VARCHAR(30),
  `To_User`   VARCHAR(30),
  FOREIGN KEY (`From_User`) REFERENCES USERS(`Username`),
  FOREIGN KEY (`To_User`) REFERENCES USERS(`Username`),
  PRIMARY KEY (`From_User`, `To_User`)
);




-- -----
-- CLANS
-- -----
CREATE TABLE CLANS
(
  `ClanName`  VARCHAR(30) NOT NULL PRIMARY KEY,
  `UserAdmin` VARCHAR(30),
  `Description`  TEXT,
  FOREIGN KEY (`UserAdmin`) REFERENCES USERS(`UserName`)
);


CREATE TABLE USER_CLAN
(
  `User` VARCHAR(30),
  `Clan` VARCHAR(30),
  FOREIGN KEY (`User`) REFERENCES USERS(`UserName`),
  FOREIGN KEY (`Clan`) REFERENCES CLANS(`ClanName`),
  PRIMARY KEY (`User`, `Clan`)
);




-- -----
-- GAME
-- -----
CREATE TABLE PUBLISHERS
(
  `Name` VARCHAR(255) NOT NULL PRIMARY KEY
);

CREATE TABLE DEVELOPERS
(
  `Name`      VARCHAR(255) NOT NULL PRIMARY KEY,
  `Publisher` VARCHAR(255),
  FOREIGN KEY (`Publisher`) REFERENCES PUBLISHERS(`Name`)
);

CREATE TABLE GAMES
(
  `Title`         VARCHAR(255) NOT NULL PRIMARY KEY,
  `Description`   TEXT,
  `Developer`     VARCHAR(255),
  FOREIGN KEY (`Developer`) REFERENCES DEVELOPERS(`Name`)
);

CREATE TABLE GAME_SERVERS
(
  `Game`    VARCHAR(255),
  `Server` VARCHAR(30),
  FOREIGN KEY (`Game`) REFERENCES GAMES(`Title`),
  PRIMARY KEY (`Game`, `Server`)
);

CREATE TABLE GAME_GENRES
(
  `Game`    VARCHAR(255),
  `Genre`   VARCHAR(255),
  FOREIGN KEY (`Game`) REFERENCES GAMES(`Title`),
  PRIMARY KEY (`Game`, `Genre`)
);




-- -------
-- LFG/LFM
-- -------
CREATE TABLE SOLOS
(
  `IGN`     VARCHAR(30) NOT NULL PRIMARY KEY,
  `User`    VARCHAR(255),
  `Server`  VARCHAR(30),
  `Status`  VARCHAR(30),
  `Group`   VARCHAR(255),
  `Game`    VARCHAR(255),
  FOREIGN KEY (`User`) REFERENCES USERS(`UserName`),
  FOREIGN KEY (`Group`) REFERENCES GROUPS(`Name`),
  FOREIGN KEY (`Game`) REFERENCES GAMES(`Title`)
);

CREATE TABLE GROUPS
(
  `Name`         VARCHAR(255) NOT NULL PRIMARY KEY,
  `Admin`        VARCHAR(255),
  `MaxSize`      INT,
  `Status`       VARCHAR(30),
  `Description`  TEXT,
  `Game`         VARCHAR(255),
  FOREIGN KEY (`Admin`) REFERENCES SOLOS(`IGN`),
  FOREIGN KEY (`Game`) REFERENCES GAMES(`TITLE`)
);


