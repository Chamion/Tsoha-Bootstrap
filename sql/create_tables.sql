-- Lisää CREATE TABLE lauseet tähän tiedostoon
CREATE TABLE Player (
    id SERIAL PRIMARY KEY,
    username varchar(32) NOT NULL,
    password varchar(50) NOT NULL
);

CREATE TABLE Game (
    id SERIAL PRIMARY KEY,
    player INTEGER REFERENCES Player(id),
    legend boolean,
    win boolean,
    hero INTEGER,
    opponent INTEGER,
    book_date date DEFAULT CURRENT_DATE
);

CREATE TABLE Team(
    id SERIAL PRIMARY KEY,
    leader INTEGER REFERENCES Player(id),
    group_name varchar(32) NOT NULL,
    closed boolean DEFAULT true
);

CREATE TABLE Membership(
    player INTEGER REFERENCES Player(id),
    team INTEGER REFERENCES Team(id),
    accepted boolean DEFAULT false
);