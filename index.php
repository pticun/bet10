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

    <div id="bets">
      <?php
        $matches = new Matches;
        $arr = $matches->getAllMatches("LIMIT 12");

        foreach($arr as $match){
          $team1 = new Team;
          $team1->findById( $match->getFirstTeam() );
          $team2 = new Team;
          $team2->findById( $match->getSecondTeam() );

           if( $match->getWinner() !== null ) {

             if ($match->getWinner() == -1) {
               echo '<div class="bet closed">';
               echo '<div class="winner">Draw</div>';
             }
             else {
               $winner = new Team;
               $winner->findById( $match->getWinner() );

               echo '<div class="bet closed">';
               echo '<div class="winner">'.$winner->getName().'</div>';
             }

           }
           elseif( $match->isLive() ){
             echo '<div class="bet live">';
             echo '<div style="time"> LIVE! </div>';
           }
           else{
             echo '<div class="bet">';
             echo '<div style="time">'. $match->getPlayingAt() .'</div>';
           }

          echo '
            <div class="team" style="float: left;">
              <img src="web/teams/'.$team1->getImg().'" alt="'.$team1->getName().'">
              <b>'.$team1->getName().'</b>
              <p>'.$match->getFirstTeamPercent().'%</p>
            </div>
          <span>VS</span>
          <div class="team" style="float:right;">
            <img src="web/teams/'.$team2->getImg().'" alt="'.$team2->getName().'">
            <b>'.$team2->getName().'</b>
            <p>'.$match->getSecondTeamPercent().'%</p>
          </div>
          <div style="clear:both;"></div>

          <a href="match.php?id='.$match->getId().'">
            <div class="bet-button">
              Let\'s bet
            </div>
          </a>
        </div>';
        }
      ?>
      <div style="clear:both;"></div>
    </div>

    <?php require_once("scripts.php"); ?>
  </body>
</html>
