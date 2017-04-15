<?php
  require_once("classes.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require_once("application.php"); ?>
    <link rel="stylesheet" href="web/css/profile.css" type="text/css" />
    <title> Biedobet </title>
  </head>
  <body>
    <?php require_once("menu.php"); ?>
    <div id="profile">
      <div id="about">
        <div id="profil-data">
          <h2 style="text-align: center;">Your Profile</h2>
          <h3><?php echo $currentUser->getNick(); ?></h3>
        </div>

        <div id="last-bets">
          <h2 style="text-align: center;">Your Bets</h2>
          <?php
            $bets = new Bets;
            $a = $bets->findAllBets("user_id = ".$currentUser->getId()." ORDER BY created_at DESC LIMIT 10");

            foreach ($a as $bet) {

              $match = new Match;
              $match->findById( $bet->getMatchId() );

              $team1 = new Team;
              $team1->findById( $match->getFirstTeam() );

              $team2 = new Team;
              $team2->findById( $match->getSecondTeam() );

              $pickedTeam = new Team;
              $pickedTeam->findById( $bet->getTeamId() );

              echo '<div class="placed-bets">'.$team1->getName() . ' VS ' . $team2->getName().' you placed on '. $pickedTeam->getName() . ' <b>'. $bet->getAmount() .'</b> coins';

              if ( $match->getWinner() !== null) {

                if ($match->getWinner() == -1 ) {
                  echo '<div class="team correct-winner"> Draw coins returned </div>';
                }

                else {

                  $winner = new Team;
                  $winner->findById( $match->getWinner() );

                  if ( $winner->getId() == $bet->getTeamId()) {

                    if ($bet->getTeamId() == $match->getFirstTeam()) {
                      $p = $match->getFirstTeamPercent()/100;
                    }
                    else {
                      $p = $match->getSecondTeamPercent()/100;
                    }

                    echo '<div class="team correct-winner"> won: '.$winner->getName().' (+'.round( $bet->getAmount()* (1/$p-1) * 0.8).' + '.$bet->getAmount().')</div>';
                  }

                  else {
                    echo '<div class="team wrong-winner"> won: '.$winner->getName().'</div>';
                  }

                }

              }
              echo '</div>';
            }
          ?>
        </div>
        <div style="clear:both;"> </div>
      </div>

      <div id="deposit">
        <h2>Deposit</h2>
        <p>Loading inventory...</p>
        <p>Reload</p>
      </div>

      <div id="exchange">
      </div>

    </div>
  </body>
</html>
