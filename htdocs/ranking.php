<?php
  require_once 'config.php';
  header( "Content-Type: text/xml; Charset=utf-8" );
  echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";

  if (isset($_GET['action'])) {
    $actionName = $_GET['action'];
    if (isAddAction($actionName)) {
      addScore();
    } else if (isRankingAction($actionName)) {
      getRanking();
    } else {
      printError();
    }
  } else {
    printError();
  }

  function isAddAction($actionName) {
    return preg_match('/^add$/', $actionName) == 1;
  }

  function isRankingAction($actionName) {
    return preg_match('/^ranking$/', $actionName) == 1;
  }

  function printError() {
    echo "<result>error</result>";
  }
  
  function printOK() {
    echo "<result>ok</result>";
  }

  function getDBManager() {
    $pdo = new PDO('mysql:host=127.0.0.1; dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
  }

  function addScore() {
    if (isset($_POST['score'])) {
      $result = parseScoreData($_POST['score']);
      $gameName = $result['gameName'];
      $secretKey = $result['secretKey'];
      $userName = $result['userName'];
      $score = $result['score'];

      if (isValidGameName($gameName)
          && isValidSecretKey($secretKey) 
          && isValidUserName($userName)
          && isValidScore($score)) {
        executeAddScoreStatement($gameName, $secretKey, $userName, $score);
        printOK();
      } else {
        //error
        printError();
      }
    } else {
      printError();
    }
  }

  function executeAddScoreStatement($gameName, $secretKey, $userName, $score) {
    $pdo = getDBManager();
    $statement = $pdo->prepare('INSERT INTO score (gid, name, score) VALUES ((SELECT id FROM game WHERE name = :game AND secret = :secret), :user, :score)');
    $statement->bindValue(':game', $gameName);
    $statement->bindValue(':secret', $secretKey);
    $statement->bindValue(':user', $userName);
    $statement->bindValue(':score', $score);
    $statement->execute();
    unset($pdo);
  }

  function parseScoreData($chunked) {
    //format -> rand-gameName-secretKey-userName-score to base64encode
    $result = array();
    $keys = array(null, 'gameName', 'secretKey', 'userName', 'score');
    $raw = explode('-', base64_decode($chunked));
    for ($i = 1; $i < count($raw); $i++) {
      $result[$keys[$i]] = $raw[$i];
    }
    return $result;
  }

  function getRanking() {
    if (isset($_POST['ranking'])) {
      $result = parseRankingRequest($_POST['ranking']);
      $gameName = $result['gameName'];
      $secretKey = $result['secretKey'];

      if (isValidGameName($gameName) && isValidSecretKey($secretKey)) {
        $result = executeGetRankingStatement($gameName, $secretKey);
        //TODO
        printRanking($result);
      } else {
        //error
        printError();
      }
    } else {
      printError();
    }
  }

  function executeGetRankingStatement($gameName, $secretKey) {
    $pdo = getDBManager();
    //top 10 by default
    $statement = $pdo->prepare('SELECT name, score FROM score WHERE (SELECT id FROM game WHERE name = :gameName and secret = :secretKey)ORDER BY score DESC LIMIT 0, 10');
    $statement->bindValue(':gameName', $gameName);
    $statement->bindValue(':secretKey', $secretKey);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    unset($pdo);
    return $result;
  }

  function parseRankingRequest($chunked) {
    //format -> rand-gameName-secretKey to base64encode
    $result = array();
    $keys = array(null, 'gameName', 'secretKey');
    $raw = explode('-', base64_decode($chunked));
    for ($i = 1; $i < count($raw); $i++) {
      $result[$keys[$i]] = $raw[$i];
    }
    return $result;
  }

  function printRanking($ranking) {
    echo "<result>";
    echo "<items>";
    foreach($ranking as $data) {
      echo "<item>";
      echo "<name>" . htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8') . "</name>";
      echo "<score>" . htmlspecialchars($data['score'], ENT_QUOTES, 'UTF-8') . "</score>";
      echo "</item>";
    }
    echo "</items>";
    echo "</result>";
  }

?>
