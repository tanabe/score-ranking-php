<?php
//database settings
//please rewrite below definitions
define("DB_NAME", "score_ranking");
define("DB_USER", "game_score");
define("DB_PASSWORD", "ranking");

/**
 *  validate game name
 *  @poaram $gameName game name
 *  @return true of false
 */
function isValidGameName($gameName) {
  return preg_match('/^[a-zA-Z0-9]{1,10}$/', $gameName) == 1;
}

/**
 *  validate secret key
 *  @poaram $secretKey secret key
 *  @return true of false
 */
function isValidSecretKey($secretKey) {
  return preg_match('/^[a-zA-Z0-9]{1,32}$/', $secretKey) == 1;
}

/**
 *  validate user name
 *  @poaram $userName user name
 *  @return true of false
 */
function isValidUserName($userName) {
  return preg_match('/^[a-zA-Z0-9]{1,10}$/', $userName) == 1;
}

/**
 *  validate score
 *  @poaram $score score
 *  @return true of false
 */
function isValidScore($score) {
  return (preg_match('/^[0-9]{1,10}$/', $score) == 1) && (intval($score) >= 0);
}

/**
 *  validate token
 *  @poaram $token token
 *  @return true of false
 */
function isValidToken($token) {
  return preg_match('/^[a-zA-Z0-9]+$/', $token) == 1;
}
