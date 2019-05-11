-- ----
-- USER
-- ----
CREATE TABLE USERS
(
  `UserName`        VARCHAR(30) NOT NULL PRIMARY KEY,
  `FirstName`       VARCHAR(255),
  `LastName`        VARCHAR(255),
  `Email`           VARCHAR(255),
  `Password`        VARCHAR(40),
  `Token`  	        VARCHAR(255),
  `Auth_Token`      VARCHAR(255),
  `Reset_Token`     VARCHAR(255),
  `Authenticated`   tinyint(1),
  `LevelOfAccess`   VARCHAR(30),
  `About`           TEXT,
  `ProfilePicturePath` TINYTEXT
) ENGINE=InnoDB CHARACTER SET utf8;




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
) ENGINE=InnoDB CHARACTER SET utf8;


CREATE TABLE FRIEND_REQUESTS
(
  `From_User` VARCHAR(30),
  `To_User`   VARCHAR(30),
  FOREIGN KEY (`From_User`) REFERENCES USERS(`Username`),
  FOREIGN KEY (`To_User`) REFERENCES USERS(`Username`),
  PRIMARY KEY (`From_User`, `To_User`)
) ENGINE=InnoDB CHARACTER SET utf8;




-- -----
-- CLANS
-- -----
CREATE TABLE CLANS
(
  `ClanName`  VARCHAR(30) NOT NULL PRIMARY KEY,
  `UserAdmin` VARCHAR(30),
  `Description`  TEXT,
  FOREIGN KEY (`UserAdmin`) REFERENCES USERS(`UserName`)
) ENGINE=InnoDB CHARACTER SET utf8;


CREATE TABLE USER_CLAN
(
  `User` VARCHAR(30),
  `Clan` VARCHAR(30),
  FOREIGN KEY (`User`) REFERENCES USERS(`UserName`),
  FOREIGN KEY (`Clan`) REFERENCES CLANS(`ClanName`),
  PRIMARY KEY (`User`, `Clan`)
) ENGINE=InnoDB CHARACTER SET utf8;




-- -----
-- GAME
-- -----
CREATE TABLE PUBLISHERS
(
  `Name` VARCHAR(255) NOT NULL PRIMARY KEY
) ENGINE=InnoDB CHARACTER SET utf8;

CREATE TABLE DEVELOPERS
(
  `Name`      VARCHAR(255) NOT NULL PRIMARY KEY,
  `Publisher` VARCHAR(255),
  FOREIGN KEY (`Publisher`) REFERENCES PUBLISHERS(`Name`)
) ENGINE=InnoDB CHARACTER SET utf8;

CREATE TABLE GAMES
(
  `Title`         VARCHAR(255) NOT NULL PRIMARY KEY,
  `Description`   TEXT,
  `Developer`     VARCHAR(255),
  FOREIGN KEY (`Developer`) REFERENCES DEVELOPERS(`Name`)
) ENGINE=InnoDB CHARACTER SET utf8;

CREATE TABLE GAME_SERVERS
(
  `Game`    VARCHAR(255),
  `Server` VARCHAR(30),
  FOREIGN KEY (`Game`) REFERENCES GAMES(`Title`),
  PRIMARY KEY (`Game`, `Server`)
) ENGINE=InnoDB CHARACTER SET utf8;

CREATE TABLE GAME_GENRES
(
  `Game`    VARCHAR(255),
  `Genre`   VARCHAR(255),
  FOREIGN KEY (`Game`) REFERENCES GAMES(`Title`),
  PRIMARY KEY (`Game`, `Genre`)
) ENGINE=InnoDB CHARACTER SET utf8;




-- -------
-- LFG/LFM
-- -------
CREATE TABLE SOLOS
(
  `IGN`     VARCHAR(30) NOT NULL PRIMARY KEY,
  `User`    VARCHAR(255),
  `Server`  VARCHAR(30),
  `Status`  VARCHAR(30),
  `Game`    VARCHAR(255),
  FOREIGN KEY (`User`) REFERENCES USERS(`UserName`),
  FOREIGN KEY (`Game`) REFERENCES GAMES(`Title`)
) ENGINE=InnoDB CHARACTER SET utf8;

CREATE TABLE SOLO_GROUP
(
  `Solo`    VARCHAR(30),
  `Group`   VARCHAR(255),
  FOREIGN KEY (`Solo`) REFERENCES SOLOS(`IGN`),
  FOREIGN KEY (`Group`) REFERENCES GROUPS(`Name`),
  PRIMARY KEY (`Solo`, `Group`)
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
) ENGINE=InnoDB CHARACTER SET utf8;




-- -----
-- POSTS
-- -----
CREATE TABLE POSTS
(
  `Post_ID`         INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `User`            VARCHAR(30),
  `Date_Created`    DATE,
  `LevelOfAccess`   VARCHAR(30),
  `Text`            TEXT,
  `MediaType`       VARCHAR(30),
  `MediaPath`       TINYTEXT,
  FOREIGN KEY (`User`) REFERENCES USERS(`UserName`)
) ENGINE=InnoDB CHARACTER SET utf8;

CREATE INDEX idx_posts_users ON POSTS (`User`); 



CREATE TABLE COMMENTS 
(
  `Comment_ID`          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Post`                INT,
  `User`                VARCHAR(30),
  `Comment`             TEXT,
  `Date_Created`        DATE,
  FOREIGN KEY (`Post`) REFERENCES POSTS(`Post_ID`),
  FOREIGN KEY (`User`) REFERENCES USERS(`UserName`)
) ENGINE=InnoDB CHARACTER SET utf8;
CREATE INDEX COMMENTS_POST_INDEX ON COMMENTS (`Post`);




-- ---------
-- REACTIONS
-- ---------
CREATE TABLE POST_REACTIONS
(
  `Reaction_ID`         INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Post`                INT,   
  `User`                VARCHAR(30),        
  `Value`               INT,
  FOREIGN KEY (`Post`) REFERENCES POSTS(`Post_ID`),
  FOREIGN KEY (`User`) REFERENCES USERS(`UserName`)
) ENGINE=InnoDB CHARACTER SET utf8;
CREATE UNIQUE INDEX POST_REACTIONS_POST_USER_INDEX ON POST_REACTIONS(`Post`, `User`);

CREATE TABLE COMMENT_REACTIONS
(
  `Reaction_ID`         INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Comment`             INT,   
  `User`                VARCHAR(30),        
  `Value`               INT,
  FOREIGN KEY (`Comment`) REFERENCES COMMENTS(`Comment_ID`),
  FOREIGN KEY (`User`) REFERENCES USERS(`UserName`)
) ENGINE=InnoDB CHARACTER SET utf8;
CREATE UNIQUE INDEX COMMENT_REACTIONS_COMMENT_USER_INDEX ON COMMENT_REACTIONS(`Comment`, `User`);

-- --------
-- MESSAGES
-- --------
CREATE TABLE MESSAGES 
(
  `Message_ID`        INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `Sender`            VARCHAR(30),
  `Receiver`          VARCHAR(30),
  `Message`           TEXT,
  `Date_Created`      DATE,
  FOREIGN KEY (`Sender`) REFERENCES USERS(`UserName`),
  FOREIGN KEY (`Receiver`) REFERENCES USERS(`UserName`)
) ENGINE=InnoDB CHARACTER SET utf8;

CREATE INDEX MESSAGES_SENDER_INDEX ON MESSAGES (`Sender`);
CREATE INDEX MESSAGES_RECEIVER_INDEX ON MESSAGES (`Receiver`);