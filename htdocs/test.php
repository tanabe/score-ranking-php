<?php
function hoge() {
  $token = md5('ActionGame' . 'hogeo' . '8888' . 'HogeHoge');
  echo $token;
}

function huga() {
  $data = array(rand(0, 9999), 'ActionGame', 'HogeHoge');
  echo base64_encode(implode('-', $data));
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="content-language" content="ja">

    <title>test</title>

    <meta http-equiv="content-script-type" content="text/javascript">
    <meta http-equiv="content-style-type" content="text/css">

    <style type="text/css">
      input {
        width: 500px;
      }
    </style>

  </head>
  <body>
    <h1>test</h1>

    <h2>add</h2>
    <form method="POST" action="./ranking.php?action=add">

      <p>
        <label for="gameName">gameName</label>
        <input type="text" name="gameName" maxlength="10" value="ActionGame">
      </p>

      <p>
        <label for="userName">userName</label>
        <input type="text" name="userName" maxlength="10" value="hogeo">
      </p>

      <p>
        <label for="score">score</label>
        <input type="text" name="score" maxlength="10" value="8888">
      </p>

      <p>
        <label for="token">token</label>
        <input type="text" name="token" maxlength="255" value="<?php echo hoge(); ?>">
      </p>

      <p>
        <button type="submit">送信</button>
      </p>
    </form>

    <h2>ranking</h2>
    <form method="POST" action="./ranking.php?action=ranking">
      <p>
        <label for="gameName">gameName</label>
        <input type="text" name="gameName" maxlength="10" value="ActionGame">
      </p>
      <p>
        <label for="total">total</label>
        <input type="text" name="total" maxlength="3" value="10">
      </p>
      <p>
        <button type="submit">送信</button>
      </p>
    </form>
  </body>
</html>
