<?php
/**
 *  create table
 *  please delete this file after created database
 */
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
        echo "で作成しました。setup.php をサーバから削除してください。";
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
                  name VARCHAR(10) BINARY UNIQUE NOT NULL,
                  secret VARCHAR(32) BINARY NOT NULL,
                  INDEX(id)
                ) ENGINE=InnoDB');

    $pdo->exec('CREATE TABLE IF NOT EXISTS score (
                  id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
                  gid int NOT NULL,
                  name VARCHAR(10) BINARY NOT NULL,
                  score int NOT NULL,
                  INDEX(gid),
                  FOREIGN KEY(gid) REFERENCES game(id)
                ) ENGINE=InnoDB');

    $statement = $pdo->prepare('INSERT IGNORE INTO game(name, secret) VALUES (:game, :secret)');
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

    <title>セットアップ</title>

    <meta http-equiv="content-script-type" content="text/javascript">
    <meta http-equiv="content-style-type" content="text/css">

    <style type="text/css">
      html {
        height: 100%;
      }

      body {
        background-color: #fff;
        color: #02243C;
        font-family: Helvetica,Arial,Verdana,"Hiragino Kaku Gothic Pro","ヒラギノ角ゴ Pro W3",sans-serif;
        margin: 0;
        padding: 0;
        font-size: 1.2em;
        line-height: 1.3em;
        height: 100%;
      }

      h1, h2, h3, h4, h5, h6, p {
        font-size: 1em;
        font-weight: normal;
        margin: 0;
        padding: 0;
      }

      h1 {
        font-size: 1.5em;
        line-height: 1.5em;
        padding: 5px;
        font-weight:bold;
        background-color: #e6e6fa;
        border-top: 5px solid #191970;
      }

      div#content {
        padding: 10px;
      }
      
      table {
        border: 1px #191970 solid;
        border-collapse: collapse;
        border-spacing: 0;
      }
 
      table th {
        padding: 5px;
        border: #191970 solid;
        border-width: 0 0 1px 1px;
        background: #e6e6fa;
        font-weight: normal;
        line-height: 120%;
        text-align: center;
      }
 
      table td {
        padding: 5px;
        border: 1px #191970 solid;
        border-width: 0 0 1px 1px;
        text-align: center;
      }

      input, button {
        font-size: 1em;
      }
    </style>

  </head>
  <body>
    <h1>セットアップ</h1>
    <div id="content">
      <?php
        showMessage();
      ?>
      <form method="POST" action="./setup.php">
        <table>

          <tr>
            <th><label for="gameName">ゲーム名<br>(半角英数字10文字以内)</label></th>
            <td><input type="text" id="gameName" name="gameName" maxlength="10"></td>
          </tr>

          <tr>
            <th><label for="secretKey">シークレットキー<br>(半角英数字32文字以内)</label></th>
            <td><input type="text" id="secretKey" name="secretKey" maxlength="32"></td>
          </tr>

          <tr>
            <td colspan="2"><button type="submit">作成する</button></td>
          </tr>
        </table>
        
      </form>
    </div>
  </body>
</html>
