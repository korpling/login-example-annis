<?php
include_once '../common/Config.php';
include_once '../common/UserCreator.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <script src="../jquery-2.1.3.min.js"></script>
    <title>Protected login</title>
  </head>
  <body>
    <?php
    $annisUser = '';
    $annisPwd = '';
    // we expect the REMOTE_USER variable to be set to a valid identifier
    if (filter_has_var(INPUT_SERVER, 'REMOTE_USER')) {
      $annisUser = filter_input(INPUT_SERVER, 'REMOTE_USER');
      // create a new user by using the REST API
      $creator = new UserCreator();
      $annisPwd = $creator->createTemporaryUser($annisUser);
    }
    ?>
    <form action="/annis-gui/login" id="loginForm" method="POST">
      <input type="hidden" name="annis-login-user" value="<?=$annisUser?>">
      <input type="hidden" name="annis-login-password" value="<?=$annisPwd?>">
    </form>
    <p>Automatically logging in user &quot;<?=$annisUser?>&quot;</p>
    <script>
      $(document).ready(function () {
        $("#loginForm").submit();
      });
    </script>
  </body>
</html>
