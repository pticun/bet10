<?php
  require_once("classes.php");

  if (isset($currentUser)) {
    header("Location: index.php");
  }

  if (isset( $_POST['nick'] )) {
    $nick = htmlentities( $_POST['nick'] );
    $password = htmlentities( $_POST['password'] );
    $passwordConfirmation = htmlentities( $_POST['password_confirmation'] );

    try {
      if ( strlen($nick) < 5 ) {
        throw new Exception("Nick is too short");
      }
      else {
        if ( preg_match('/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $nick) ) {
          throw new Exception("Nick can't contain any special characters");
        }
        else {
          if ( strlen($password) < 8) {
            throw new Exception("Password is too short");
          }
          else {
            if ($password !== $passwordConfirmation) {
              throw new Exception("Passwords dsn't match");
            }
            else {
              $user = new User;
              $user->setNick($nick);
              if ($user->checkNickDuplicate()) {
                throw new Exception("Nick has been taken");
              }
              else {
                $hashPassword = crypt($password, 'sdfafraw45adsfg');
                $user->setPassword($hashPassword);
                //$user->setCoins = 10000;

                $user->pushToDb();
              }

            }
          }
        }
      }
    } catch (Exception $e) {
      $_SESSION['u_error'] = $e->getMessage();
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

        <h1>Register</h1>
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
          <input type="password" name="password" placeholder="Password" />
        </label>
        <label>
          <input type="password" name="password_confirmation" placeholder="Confirm Password" />
        </label>
        <label>
          <input type="submit" value="Register" />
        </label>

      </form>
    </div>

    <?php require_once('scripts.php'); ?>
  </body>
</html>
