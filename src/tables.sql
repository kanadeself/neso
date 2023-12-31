CREATE TABLE Idols (
  IdolID int PRIMARY KEY AUTO_INCREMENT NOT NULL,
  IdolName varchar(50) NOT NULL UNIQUE,
  IdolNameJP varchar(50) NOT NULL,
  Color varchar(7) DEFAULT NULL,
  franchise varchar(50) NOT NULL
);

CREATE TABLE NesoOwnership (
  UserID int NOT NULL,
  NesoID int NOT NULL,
  FOREIGN KEY(UserID) REFERENCES Users(UserID),
  FOREIGN KEY(NesoID) REFERENCES Nesos(NesoID)
);

CREATE TABLE Nesos (
  NesoID int PRIMARY KEY AUTO_INCREMENT NOT NULL,
  NesoName varchar(100) DEFAULT NULL,
  NesoNameJP varchar(50) NOT NULL,
  IdolID int NOT NULL,
  Size varchar(15) DEFAULT NULL,
  SizeJP varchar(50) NOT NULL,
  ReleaseYear varchar(5) DEFAULT NULL,
  ActualSize varchar(10) DEFAULT NULL,
  ImageFileName varchar(50) DEFAULT NULL,
  franchise varchar(50) NOT NULL,
  FOREIGN KEY(IdolID) REFERENCES Idols(IdolID)
);

CREATE TABLE Users (
  UserID int PRIMARY KEY AUTO_INCREMENT NOT NULL,
  Username varchar(50) NOT NULL UNIQUE,
  Pincode int NOT NULL,
  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  twitter varchar(16) DEFAULT NULL,
  lang varchar(2) NOT NULL DEFAULT 'en'
);
