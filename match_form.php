<?php
  require_once("classes.php");
  if (isset($_GET['id'])) {
    # code...
  }
  $id = intval($_GET['id']);

  $match = new Match;
  $arr = $match->findById($id, true);

  echo json_encode($arr);
?>
