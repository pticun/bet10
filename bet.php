<?php
  require_once('classes.php');

  $array = ['message' => 'error', 'success' => false];

  try {
      if ( !isset($_POST['match']) || !isset($_POST['team1']) || !isset($_POST['team2']) || !isset($_POST['teambet']) || !isset($_POST['amount'])) {
        throw new Exception("Error problem with data");
      }
      else {
        $matchId = intval( $_POST['match'] );
        $firstTeam = intval( $_POST['team1'] );
        $secondTeam = intval( $_POST['team2'] );
        $bettedTeam = intval( $_POST['teambet'] );
        $amount = intval( $_POST['amount'] );

        if (!isset($currentUser)) {
          throw new Exception("You need to log in!");
        }
        else{
          $match = new Match;
          $match->findById($matchId);
          if ( $match->isLive() || $match->getWinner() !== null ) {
            throw new Exception("You cant bet this match anymore");
          }
          else {
            if ($bettedTeam != $match->getFirstTeam() && $bettedTeam != $match->getSecondTeam()) {
              throw new Exception("Wrong teams, refresh the page");
            }
            else {
              if ($currentUser->getCoins() - $amount < 0) {
                throw new Exception("You dont have enought coins to place this bet");
              }
              else {
                $bets = new Bets;
                $u = $currentUser->getId();
                $m = $match->getId();
                $b = $bets->findAllBets("user_id = $u AND match_id = $m");

                if ( sizeof($b) > 0 ) {
                  throw new Exception("You have already placed coins on this match. ");
                }

                else {
                  $bet = new Bet;

                  $bet->setUserId($currentUser->getId());
                  $bet->setMatchId($match->getId());
                  $bet->setTeamId($bettedTeam);
                  $bet->setAmount($amount);

                  $bet->pushToDb();

                  $currentUser->minusCoins($amount);

                  $team = new Team;
                  $team->findById($bettedTeam);

                  $array['message'] = "Done! you placed <b>$amount</b> coins on <b>".$team->getName()."</b>";
                  $array['success'] = true;
                }


              }
            }
          }
      }

    }

  } catch (Exception $e) {
    $array['message'] = $e->getMessage();
  }

  echo json_encode($array);

?>
