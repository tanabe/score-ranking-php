-- insert test data
INSERT INTO game (name, secret) VALUES ('racerX', 'hoge');

INSERT INTO score (gid, name, score) VALUES ((SELECT id FROM game WHERE name = 'racerX' AND secret = 'hoge'), 'user', '1234');
