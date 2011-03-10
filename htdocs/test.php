<?php
function hoge() {
  $data = array(rand(), 'digdug', 'hogehoge', 'llll', '999999');
  echo base64_encode(implode('-', $data));
}
function huga() {
  $data = array(rand(), 'digdug', 'hogehoge');
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
      label {
        display: block;
      }

      input {
        display: block;
        width: 500px;
      }
    </style>

  </head>
  <body>
    <h1>test</h1>

    <form method="POST" action="./ranking.php?action=add">
      <label for="data">add</label>
      <input type="text" id="score" name="score" maxlength="255" value="<?php hoge() ?>">
      <button type="submit">送信</button>
    </form>

    <form method="POST" action="./ranking.php?action=ranking">
      <label for="data">ranking</label>
      <input type="text" id="ranking" name="ranking" maxlength="255" value="<?php huga() ?>">
      <button type="submit">送信</button>
    </form>
  </body>
</html>
