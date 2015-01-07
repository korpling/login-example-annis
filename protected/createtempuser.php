<!--
 * Copyright 2015 Corpuslinguistic working group Humboldt University Berlin.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
-->
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
    <form action="<?=Config::annisURL?>/login" id="loginForm" method="POST">
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
