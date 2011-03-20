-- create database
CREATE DATABASE IF NOT EXISTS score_ranking CHARACTER SET utf8;

-- create user
GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP ON score_ranking.* TO game_score@localhost IDENTIFIED BY 'ranking';
