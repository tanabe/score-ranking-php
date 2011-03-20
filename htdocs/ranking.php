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
    exit;
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
      $userName = $result['userName'];
      $score = $result['score'];
      $token = $result['token'];
      if (isValidGameName($gameName)
          && isValidUserName($userName)
          && isValidScore($score)) {
        executeAddScoreStatement($gameName, $userName, $score, $token);
        printOK();
      } else {
        //error
        printError();
      }
    } else {
      printError();
    }
  }

  function executeAddScoreStatement($gameName, $userName, $score, $token) {
    try {
      $pdo = getDBManager();
      //echo $gameName, $userName, $score, $token;
      //exit;
      $statement = $pdo->prepare('
        INSERT INTO score (gid, name, score)
        VALUES (
          (SELECT id FROM game WHERE name = :game
            AND MD5(CONCAT(:game, :user, :score, (SELECT secret FROM game WHERE name = :game))) = :token),
          :user, :score)
      ');
      $statement->bindValue(':game', $gameName);
      $statement->bindValue(':user', $userName);
      $statement->bindValue(':score', $score);
      $statement->bindValue(':token', $token);
      $statement->execute();
      unset($pdo);
    } catch (PDOException $error) {
      printError();
    }
  }

  function parseScoreData($chunked) {
    //format -> rand-gameName-userName-score-token to base64encode
    //token is md5(gamaeName . userName . score . secretKey)
    $result = array();
    $keys = array(null, 'gameName', 'userName', 'score', 'token');
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

      //TODO
      $total = 10;
      if (isset($_POST['total']) && (preg_match('/^[0-9]+$/', $_POST['total']))) {
        $total = intval($_POST['total']);
      }

      if (isValidGameName($gameName) && isValidSecretKey($secretKey)) {
        $result = executeGetRankingStatement($gameName, $secretKey, $total);
        printRanking($result);
      } else {
        //error
        printError();
      }
    } else {
      printError();
    }
  }

  function executeGetRankingStatement($gameName, $secretKey, $total) {
    $pdo = getDBManager();
    $statement = $pdo->prepare('SELECT DISTINCT name, score FROM score WHERE (SELECT id FROM game WHERE name = :gameName AND secret = :secretKey) ORDER BY score DESC LIMIT 0, :total');
    $statement->bindValue(':gameName', $gameName);
    $statement->bindValue(':secretKey', $secretKey);
    $statement->bindValue(':total', $total, PDO::PARAM_INT);
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
