<?php
  require_once("classes.php");
?>

<!DOCTYPE html>
<html>
  <head>
    <?php require_once("application.php"); ?>
    <title> Biedobet </title>
  </head>
  <body>
    <?php require_once("menu.php"); ?>
    <?php
      global $match;
      $match = new Match;
      $match->findById($_GET['id']);
        $team = new Team;
        $team->findById($match->getFirstTeam());

        $team2 = new Team;
        $team2->findById($match->getSecondTeam());
    ?>


    <div id="match">
      <?php
        if (isset($currentUser)) {
          $bets = new Bets;
          $a = $bets->findAllBets("user_id = ".$currentUser->getId()." AND match_id = ".$match->getId());
          if ( $a ) {
            echo '<div class="warning"><b>Warning:</b> You\'ve already placed coins on this match if you want see click <a href="profile.php">here</a></div>';
          }
        }
      ?>


      <div class="hidden" id="match-id"><?php echo intval($match->getId()); ?></div>
      <div class="time"><?php echo $match->getPlayingAt(); ?></div>

      <div class="first-team <?php if($match->getWinner() == null && !$match->isLive()) echo 'team-bet'; ?>" style="float:left;">
        <div class="hidden" id="team-first-id"><?php echo $team->getId(); ?></div>
        <h1><?php echo $team->getName(); ?></h1>
        <h2><?php echo $match->getFirstTeamPercent(); ?>%</h2>
        <?php echo '<img src="web/teams/'.$team->getImg().'" alt="'.$team->getName().'">'; ?>
      </div>

      <span>VS</span>

      <div class="second-team <?php if($match->getWinner() == null && !$match->isLive()) echo 'team-bet'; ?>" style="float:right;">
        <div class="hidden" id="team-second-id"><?php echo $team2->getId(); ?></div>
        <h1><?php echo $team2->getName(); ?></h1>
        <h2><?php echo $match->getSecondTeamPercent(); ?>%</h2>
        <?php echo '<img src="web/teams/'.$team2->getImg().'" alt="'.$team2->getName().'">'; ?>
      </div>
      <div style="clear:both;"></div>

      <?php if ( $match->getWinner() == null && !$match->isLive()): ?>
        <div id="value">
          <p class="hint"> Insert value then click on a team that u want to bet </p>
          <h2>Coins</h2>
          <input type="text" name="credits" placeholder="Place bet" id="credits"/>
        </div>
      <?php endif; ?>

    </div>

    <?php require_once("scripts.php"); ?>
  </body>
</html>
