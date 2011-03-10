-- create database
CREATE DATABASE IF NOT EXISTS game_ranking CHARACTER SET utf8;

-- create user
GRANT SELECT,INSERT,UPDATE,DELETE,CREATE,DROP ON game_ranking.* TO game@localhost IDENTIFIED BY 'ranking';
