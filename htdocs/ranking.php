<?php
  require_once 'config.php';
  header( "Content-Type: text/xml; Charset=utf-8" );
  echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";

  /**
   *  validate add action name
   *  @param $actionName action name
   *  @return true of false
   */
  function isAddAction($actionName) {
    return preg_match('/^add$/', $actionName) == 1;
  }

  /**
   *  validate ranking action name
   *  @param $actionName action name
   *  @return true of false
   */
  function isRankingAction($actionName) {
    return preg_match('/^ranking$/', $actionName) == 1;
  }
  
  /**
   *  get PDO instance
   *  @return PDO instance
   */
  function getDBManager() {
    $pdo = new PDO('mysql:host=127.0.0.1; dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
  }

  /**
   *  add score
   */
  function addScore() {
    if (isset($_POST['gameName']) && isValidGameName($_POST['gameName'])
        && isset($_POST['userName']) && isValidUserName($_POST['userName'])
        && isset($_POST['score']) && isValidScore($_POST['score'])
        && isset($_POST['token']) && isValidToken($_POST['token'])
    ) {
      $gameName = $_POST['gameName'];
      $userName = $_POST['userName'];
      $score = $_POST['score'];
      $token = $_POST['token'];
      executeAddScoreStatement($gameName, $userName, $score, $token);

      $result = executeGetRankingStatement($gameName, 10);
      printRanking($result);
    } else {
      printError();
    }
  }

  /**
   *  insert score to database
   *  @param $gameName game name
   *  @param $userName user name
   *  @param $score score
   *  @param $token token
   */
  function executeAddScoreStatement($gameName, $userName, $score, $token) {
    try {
      $pdo = getDBManager();
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

  /**
   *  get ranking
   */
  function getRanking() {
    if (isset($_POST['gameName']) && isValidGameName($_POST['gameName'])) {
      $gameName = $_POST['gameName'];
      $total = 10;
      if (isset($_POST['total']) && (preg_match('/^[0-9]+$/', $_POST['total']))) {
        $total = intval($_POST['total']);
      }
      $result = executeGetRankingStatement($gameName, $total);
      printRanking($result);
    } else {
      printError();
    }
  }

  /**
   *  fetch ranking data from database
   *  @param $gameName game name
   *  @param $total total
   */
  function executeGetRankingStatement($gameName, $total) {
    $pdo = getDBManager();
    $statement = $pdo->prepare('SELECT DISTINCT name, score FROM score WHERE (SELECT id FROM game WHERE name = :gameName) ORDER BY score DESC LIMIT 0, :total');
    $statement->bindValue(':gameName', $gameName);
    $statement->bindValue(':total', $total, PDO::PARAM_INT);
    $statement->execute();
    $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    unset($pdo);
    return $result;
  }

  /**
   *  print ranking XML
   *  @param $ranking ranking data
   */
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

  function printError() {
    echo "<result>error</result>";
    exit;
  }

  //entry point
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
?>
