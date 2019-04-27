CREATE TABLE USERS
(
  UserName VARCHAR(30) NOT NULL PRIMARY KEY,
  FirstName VARCHAR(30),
  LastName VARCHAR(30),
  Email VARCHAR(255),
  Password VARCHAR(40)
);

CREATE TABLE FRIENDS
(
  user VARCHAR(30),
  friend VARCHAR(30),
  FOREIGN KEY (user) REFERENCES USERS(Username),
  FOREIGN KEY (friend) REFERENCES USERS(Username),
  PRIMARY KEY (user, friend)
);

CREATE TABLE FRIEND_REQUESTS
(
  from_user VARCHAR(30),
  to_user VARCHAR(30),
  FOREIGN KEY (from_user) REFERENCES USERS(Username),
  FOREIGN KEY (to_user) REFERENCES USERS(Username),
  PRIMARY KEY (from_user, to_user)
);


CREATE TABLE CLANS
(
  ClanName VARCHAR(30) NOT NULL PRIMARY KEY,
  UserAdmin VARCHAR(30),
  FOREIGN KEY (UserAdmin) REFERENCES USERS(UserName)
);


CREATE TABLE USER_CLAN
(
  user varchar(30),
  clan varchar(30),
  FOREIGN KEY (user) REFERENCES USERS(UserName),
  FOREIGN KEY (clan) REFERENCES CLANS(ClanName)
);


