<?php
  session_start();
  session_destroy();
  session_start();
  $_SESSION['notice'] = "Logged out";
  header("Location: index.php");
?>
