<!DOCTYPE html>
<?php
include_once './common/Config.php';
?>
<html>
  <head>
    <meta charset="UTF-8">
    <title>ANNIS login example</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
  </head>
  <body>

    <div class="control">
      <h1>Login options</h1>
      <h2>Login with account from your home institution</h2>
      <p>
        <a href="protected/createtempuser.php">Click here to login with Shibboleth</a>
      </p>
      <h2>Local Login</h2>
      <form action="<?= Config::annisURL ?>/login" method="post">
        <div>User name<br /><input class="text" type="text" name="annis-login-user" value=""/></div>
        <div>Password<br /><input class="text" type="password" name="annis-login-password" value=""/></div>
        <div class="loginButtonBox">
          <input type="submit" value="Login with local account"/> or 
          <a href="javascript:window.parent.annis.gui.logincallback();">Cancel</a>
        </div>
      </form>
    </div>
  </body>
</html>
