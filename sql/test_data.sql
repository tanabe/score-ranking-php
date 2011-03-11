-- insert test data
-- INSERT INTO game (name, secret) VALUES ('racerX', 'hoge');
-- INSERT INTO score (gid, name, score) VALUES ((SELECT id FROM game WHERE name = 'racerX' AND secret = 'hoge'), 'user', '1234');

INSERT INTO score (gid, name, score)
VALUES ((SELECT id FROM game WHERE name = 'digdug' AND MD5(CONCAT('digdug' , 'test' , '123' , (SELECT secret FROM game WHERE name = 'digdug'))) = '2f3fe60c9179410906ebddc77a3e67c1'), 'test', '123');

-- WHERE MD5('digdug' + 'test' + '123' + (SELECT secret FROM game WHERE name = 'digdug')) = '2f3fe60c9179410906ebddc77a3e67c1');
-- SELECT MD5(CONCAT('digdug' , 'test' , '123' , (SELECT secret FROM game WHERE name = 'digdug'))) = '2f3fe60c9179410906ebddc77a3e67c1';
