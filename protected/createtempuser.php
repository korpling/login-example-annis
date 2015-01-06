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
    <p>
      <?php
      $annisUser = '';
      $annisPwd = '';
      // we expect the REMOTE_USER variable to be set to a valid identifier
      if (filter_has_var(INPUT_SERVER, 'REMOTE_USER')) {
        $annisUser = filter_input(INPUT_SERVER, 'REMOTE_USER');
        // create a new user by using the REST API
        $creator = new UserCreator();
        $annisPwd = $creator->createTemporaryUser($annisUser);
        print('Created user!<br />');
      }
      print('Hello World');
      ?>
    </p>
  </body>
</html>
