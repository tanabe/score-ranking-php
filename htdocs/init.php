<?php
  require_once 'config.php';
  $messages = array();
  $gameName;
  $secretKey;

  function initialize() {
    global $messages, $gameName, $secretKey;
    if (isset($_POST['gameName'], $_POST['secretKey'])) {
      $gameName = $_POST['gameName'];
      $secretKey = $_POST['secretKey'];
      $validGameName = isValidGameName($gameName);
      $validSecretKey = isValidSecretKey($secretKey);
      if (!$validGameName) {
        $messages[] = "ゲーム名を入力してください";
      }

      if (!$validSecretKey) {
        $messages[] = "シークレットキーを入力してください";
      }

      //is valid
      if ($validGameName && $validSecretKey) {
        createTable();
        echo "ゲーム名: " . htmlspecialchars($gameName, ENT_QUOTES, 'UTF-8') . "<br>";
        echo "シークレットキー: " . htmlspecialchars($secretKey, ENT_QUOTES, 'UTF-8') . "<br>";
        echo "で作成しました。init.php をサーバから削除してください。";
        exit;
      }
    }
  }

  function showMessage() {
    global $messages;
    if (count($messages)) {
      echo '<ul>';
      foreach ($messages as $message) {
        echo '<li>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</li>';
      }
      echo '</ul>';
    }
  }

  function createTable() {
    global $gameName, $secretKey;
    $pdo = new PDO('mysql:host=127.0.0.1; dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //$pdo->exec('DROP TABLE IF EXISTS score');
    //$pdo->exec('DROP TABLE IF EXISTS game');
 
    //create tables
    $pdo->exec('CREATE TABLE IF NOT EXISTS game (
                  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                  name VARCHAR(10) NOT NULL,
                  secret VARCHAR(32) NOT NULL,
                  INDEX(id)
                ) ENGINE=InnoDB');

    $pdo->exec('CREATE TABLE IF NOT EXISTS score (
                  id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
                  gid int NOT NULL,
                  name VARCHAR(10) NOT NULL,
                  score int NOT NULL,
                  INDEX(gid),
                  FOREIGN KEY(gid) REFERENCES game(id)
                ) ENGINE=InnoDB');

    $statement = $pdo->prepare('INSERT INTO game(name, secret) VALUES (:game, :secret)');
    $statement->bindValue(':game', $gameName);
    $statement->bindValue(':secret', $secretKey);
    $statement->execute();
  }

  //entry point
  initialize();
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="content-language" content="ja">

    <title>初期設定</title>

    <meta http-equiv="content-script-type" content="text/javascript">
    <meta http-equiv="content-style-type" content="text/css">

    <style type="text/css">
      label {
        display: block;
      }

      input {
        display: block;
      }
    </style>

  </head>
  <body>
    <h1>初期設定</h1>
    <?php
      showMessage();
    ?>
    <form method="POST" action="./init.php">
      <label for="gameName">ゲーム名(半角英数字10文字以内)</label>
      <input type="text" id="gameName" name="gameName" maxlength="10">

      <label for="gameName">シークレットキー(半角英数字32文字以内)</label>
      <input type="text" id="secretKey" name="secretKey" maxlength="32">

      <button type="submit">送信</button>
    </form>
  </body>
</html>
