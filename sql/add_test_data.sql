-- Lisää INSERT INTO lauseet tähän tiedostoon
INSERT INTO Player (username, password) VALUES ('testaaja', 'testi123');
INSERT INTO Player (username, password) VALUES ('testaaja2', 'testi123');

INSERT INTO Game (player, legend, win, hero, opponent) VALUES
(1,true, true, 5, 5);
INSERT INTO Game (player, legend, win, hero, opponent) VALUES
(1,false, false, 5, 5);

INSERT INTO Team(leader, group_name) VALUES (1, 'testiryhmä');

INSERT INTO Membership (player, team, accepted) VALUES (1, 1, true);
INSERT INTO Membership (player, team, accepted) VALUES (2, 1, true);