<?php
  require_once("classes.php");

  if (isset($currentUser)) {
    header("Location: index.php");
  }

  if ( isset($_POST['nick']) ) {

    $nick = htmlentities($_POST['nick']);
    $p = htmlentities($_POST['pass']);
    $password = crypt($p, 'sdfafraw45adsfg');

    $user = new User;
    $user->findByNickAndPass($nick, $password);
    unset($user);

    if (isset($currentUser)) {
      header("Location: index.php");
    }

  }
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require_once("application.php"); ?>
    <title> Biedobet </title>
  </head>
  <body>
    <?php require_once("menu.php"); ?>

    <div id="login-box">
      <form method="post">

        <h1>Login</h1>

        <?php
          if( isset($_SESSION['u_error']) ){
            echo '<div class="banner error">'.$_SESSION['u_error'].'</div>';
            unset($_SESSION['u_error']);
          }
        ?>
        <label>
          <input type="text" name="nick" placeholder="Nick" />
        </label>
        <label>
          <input type="password" name="pass" placeholder="Password" />
        </label>
        <label>
          <input type="submit" value="Log in" />
        </label>

      </form>
    </div>

    <?php require_once('scripts.php'); ?>
  </body>
</html>
