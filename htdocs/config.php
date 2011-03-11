<?php
define("DB_NAME", "game_ranking");
define("DB_USER", "game");
define("DB_PASSWORD", "ranking");

  function isValidGameName($gameName) {
    return preg_match('/^[a-zA-Z0-9]{1,10}$/', $gameName) == 1;
  }

  function isValidSecretKey($secretKey) {
    return preg_match('/^[a-zA-Z0-9]{1,32}$/', $secretKey) == 1;
  }

  function isValidUserName($userName) {
    return preg_match('/^[a-zA-Z0-9]{1,10}$/', $userName) == 1;
  }

  function isValidScore($score) {
    return (preg_match('/^[0-9]{1,10}$/', $score) == 1) && (intval($score) >= 0);
  }

  /*
  function isValidToken($token, $gameName, $userName, $score, $secretKey) {
    return $token === md5($gameName . $userName . $score . $secretKey);
  }
  */
