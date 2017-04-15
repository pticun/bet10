<?php
  require_once("classes.php");

  if ( !isset($currentUser) || $currentUser->getRole() !== 'admin') {
    $_SESSION['notice'] = 'Forbidden';
    header("Location: index.php");
  }


  if (isset( $_POST['name'] )) {

    $dir = 'web/teams/';
    $fileName = $dir.basename($_FILES['photo']['name']);
    move_uploaded_file($_FILES['photo']['tmp_name'], $fileName);

    $team = new Team;
    $team->setName( htmlentities($_POST['name']) );
    $team->setImg( basename($_FILES['photo']['name']) );

    $team->pushToDb();
  }

  if (isset( $_POST['team1'] )) {

    $team1 = htmlentities($_POST['team1']);
    $team2 = htmlentities($_POST['team2']);
    $time = htmlentities($_POST['time_at']);

    $match = new Match;

    $match->setFirstTeam($team1);
    $match->setSecondTeam($team2);
    $match->setPlayingAt($time);

    $match->pushToDb();

  }

  elseif (isset( $_POST['match'] )) {
    $matchId = intval($_POST['match']);
    $winner = intval($_POST['winner']);

    $match = new Match;
    $match->findById($matchId);
    $match->setWinner($winner);
    $match->updateWinner($winner);

  }

?>

<!DOCTYPE html>
<html>
  <head>
    <?php require_once("application.php"); ?>
    <link rel="stylesheet" href="web/css/manage.css" type="text/css"/>
    <title> Biedobet </title>
  </head>
  <body>
    <?php require_once("menu.php"); ?>

    <div id="teams">

      <div class="new-team">
        <h1>Create new team</h1>
        <form method="post" enctype="multipart/form-data">

          <label>
            <p>Name</p>
            <input type="text" name="name" placeholder="Team name" />
          </label>

          <label>
            <p>Team image</p>
            <span class="btn-img">Click to add photo</span>
            <input type="file" name="photo" style="display:none;"/>
          </label>

          <div>
            <input type="submit" value="Add team" />
          </div>


        </form>
      </div>
    </div>

    <div id="matches">
      <h3 style="text-align: center;">List of all matches</h3>

      <select id="matches-history">
        <option value="">----</option>
        <?php
          $matches = new Matches;
          $a = $matches->getAllMatches("", "WHERE winner_id IS NULL");
          unset($matches);

          foreach ($a as $match) {
            $team1 = new Team;
            $team1->findById( $match->getFirstTeam() );
            $team2 = new Team;
            $team2->findById( $match->getSecondTeam() );
            echo '<option value="'.$match->getId().'">'.$team1->getName().' VS '.$team2->getName().'</option>';
          }
        ?>
      </select>

      <div id="match-info" style="display: none;">
        <form method="post">
          <h2>Set winner</h2>
          <input type="hidden" name="match" value="" id="winner-match-id"/>

          <p><input type="radio" name="winner" value="" id="winner-match-first-team-id" /> First team</p>
          <p><input type="radio" name="winner" value="-1" id="winner-none-draw" />Draw</p>
          <p><input type="radio" name="winner" value="" id="winner-match-second-team-id" /> Second team</p>

          <input type="submit" value="Confirm match" />
        </form>
      </div>

      <div class="add-match">
        <form method="post">
          <select name="team1">
            <?php
              $teams = new Teams;
              $a = $teams->getAllOfTeams();
              foreach($a as $team) {
                echo '<option value="'.$team->getId().'">'.$team->getName().'</option>';
              }
            ?>
          </select>
          VS
          <select name="team2">
            <?php
              $teams = new Teams;
              $a = $teams->getAllOfTeams();
              foreach($a as $team) {
                echo '<option value="'.$team->getId().'">'.$team->getName().'</option>';
              }
            ?>
          </select>

          <label>
            <p>When(Y-M-D h:m:s)</p>
            <div>
              <input type="datetime-local" name="time_at"/>
            </div>
          </label>

          <input type="submit" name="" value="Add match" />
        </form>
      </div>
    </div>

    <script src="web/js/jquery.min.js"></script>
    <script src="web/js/manage_scripts.js"></script>

  </body>
</html>
