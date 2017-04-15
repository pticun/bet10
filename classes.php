<?php
session_start();
//global $currentUser;
currentUser();

function currentUser(){
  if ( isset($_SESSION['user_id']) ) {
    global $currentUser;
    $currentUser = new User;
    $currentUser->findById($_SESSION['user_id']);
  }
}

function beforeActionIsAdmin(){
  if ( !isset($currentUser) || $currentUser->getRole() !== 'admin') {
    header("Location: index.php");
    $_SESSION['notice'] = "Forbidden";
  }
}

function connectMysqli(){

  $dbdata = array(



    'host' => $host,
    'user' =>  $user,
    'password' => $pass,
    'db' => $db,


  );
  return new mysqli( $dbdata['host'], $dbdata['user'], $dbdata['password'], $dbdata['db'] );
}

class Teams {

  public function getAllOfTeams(){

    try {

      $conn = connectMysqli();
      //$conn = new mysqli('localhost', 'root', 'ubuntu', 'csrich');
      if (!$conn->connect_errno) {
        $sql = "SELECT * FROM teams;";
        $result = $conn->query($sql);
        $returnedArray = [];

        while($row = $result->fetch_assoc()){
          $team = new Team;

          $team->setId($row['id']);
          $team->setName($row['name']);
          $team->setImg($row['img']);
          $team->setCreatedAt($row['created_at']);
          $team->setUpdatedAt($row['updated_at']);

          $returnedArray[] = $team;

        }

        return $returnedArray;
      }
      else {
        throw new Exception("Error while trying to connect".$conn->error);
      }

    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
    } finally {
      $conn->close();
    }


  }
}

class Team {
  private $id;
  private $name;
  private $img;
  private $created_at;
  private $updated_at;

  // Getting

  public function getId(){
    return $this->id;
  }

  public function getName(){
    return $this->name;
  }

  public function getImg(){
    return $this->img;
  }

  public function getCreatedAt(){
    return $this->created_at;
  }

  public function getUpdatedAt(){
    return $this->updated_at;
  }

  //

  // Setting

  public function setId($id){
    $this->id = $id;
  }

  public function setName($name){
    $this->name = $name;
  }

  public function setImg($img){
    $this->img = $img;
  }

  public function setCreatedAt($created_at){
    $this->created_at = $created_at;
  }

  public function setUpdatedAt($updated_at){
    $this->updated_at = $updated_at;
  }
  //


  public function findById($id){

    try {

      $conn = connectMysqli();

      if (!$conn->error) {
        $sql = "SELECT * FROM teams WHERE id = $id LIMIT 1";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        $this->id = $row['id'];
        $this->name = $row['name'];
        $this->img = $row['img'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
      }
      else {
        throw new Exception("Error while trying to connect".$conn->error);
      }

    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
    } finally {
      $conn->close();
    }

  }

  public function pushToDb(){
    try {

      $conn = connectMysqli();

      if (!$conn->error) {
        $sql = "INSERT INTO `teams` (`id`, `name`, `img`, `created_at`, `updated_at`) VALUES (NULL, '$this->name', '$this->img', NOW(), NOW());";
        if( $conn->query($sql) ){
          $_SESSION['notice'] = "Team added";
        }
        else {
          throw new Exception("Error with adding team");
        }
      }
      else {
        throw new Exception("Error while trying to connect".$conn->error);
      }

    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
    } finally {
      $conn->close();
    }
  }

}

class Matches {
  public function getAllMatches($addons="", $where=""){

        try {

          $conn = connectMysqli();

          if (!$conn->error) {

            $sql = "SELECT DISTINCT
            	matches.id,
            	1st_team_id,
            	2nd_team_id,
            	winner_id,
            	playing_at,
            	matches.created_at,
            	updated_at,
            	playing_at - NOW() AS is_live,
            	(SELECT sum(amount) FROM bets WHERE match_id = matches.id) AS sum_amount,
            	(SELECT sum(amount) FROM bets WHERE match_id = matches.id AND team_id = 1st_team_id) AS 1st_team_amount,
            	(SELECT sum(amount) FROM bets WHERE match_id = matches.id AND team_id = 2nd_team_id) AS 2nd_team_amount
              FROM `matches` LEFT JOIN bets ON matches.id = bets.match_id $where ORDER BY matches.created_at DESC $addons;";

            $result = $conn->query($sql);
            $returnedArray = [];

            while($row = $result->fetch_assoc()){
              $match = new Match;

              $match->setId($row['id']);

              $match->setFirstTeam($row['1st_team_id']);
              $match->setFirstTeamAmount($row['1st_team_amount']);

              $match->setSecondTeam($row['2nd_team_id']);
              $match->setSecondTeamAmount($row['2nd_team_amount']);

              $match->setSumAmount($row['sum_amount']);

              $match->setWinner($row['winner_id']);
              $match->setPlayingAt($row['playing_at']);
              $match->setIsLive($row['is_live']);
              $match->setCreatedAt($row['created_at']);
              $match->setUpdatedAt($row['updated_at']);

              $returnedArray[] = $match;

            }

            return $returnedArray;
          }
          else {
            throw new Exception("Error while trying to connect".$conn->error);
          }

        } catch (Exception $e) {
          $_SESSION['error'] = $e->getMessage();
        } finally {
          $conn->close();
        }

  }
}

class Match {
  private $id;
  private $firstTeam;
  private $firstTeamAmount;

  private $secondTeam;
  private $secondTeamAmount;

  private $sumAmount;

  private $winner = null;
  private $playing_at;
  private $is_live;
  private $created_at;
  private $updated_at;


  public function getId(){
    return $this->id;
  }

  public function getFirstTeam(){
    return $this->firstTeam;
  }

  public function getFirstTeamAmount(){
    return $this->firstTeamAmount;
  }

  public function getSecondTeam(){
    return $this->secondTeam;
  }

  public function getSecondTeamAmount(){
    return $this->secondTeamAmount;
  }

  public function getWinner(){
    return $this->winner;
  }

  public function getPlayingAt(){
    return $this->playing_at;
  }

  public function getIsLive(){
    return $this->is_live;
  }

  public function getCreatedAt(){
    return $this->created_at;
  }

  public function getUpdatedAt(){
    return $this->updated_at;
  }


  // ADDONS

  public function getFirstTeamPercent(){
    if ($this->sumAmount == null || $this->sumAmount == 0) {
      return round((100*$this->firstTeamAmount)/1);
    }
    else {
      return round((100*$this->firstTeamAmount)/$this->sumAmount);
    }

  }

  public function getSecondTeamPercent(){
    if ($this->sumAmount == null || $this->sumAmount == 0) {
      return round((100*$this->secondTeamAmount)/1);
    }
    else {
      return round((100*$this->secondTeamAmount)/$this->sumAmount);
    }
  }

  // ADDONS

  // SETS

  public function setId($id){
    $this->id = $id;
  }

  public function setFirstTeam($firstTeam){
    $this->firstTeam = $firstTeam;
  }

  public function setFirstTeamAmount($amount){
    $this->firstTeamAmount = $amount;
  }



  public function setSecondTeam($secondTeam){
    $this->secondTeam = $secondTeam;
  }

  public function setSecondTeamAmount($amount){
    $this->secondTeamAmount = $amount;
  }


  public function setSumAmount($amount){
    $this->sumAmount = $amount;
  }

  public function setWinner($winner){
    $this->winner = $winner;
  }

  public function setPlayingAt($playing_at){
    $this->playing_at = $playing_at;
  }

  public function setIsLive($is_live){
    $this->is_live = $is_live;
  }

  public function setCreatedAt($created_at){
    $this->created_at = $created_at;
  }

  public function setUpdatedAt($updated_at){
    $this->updated_at = $updated_at;
  }


  public function isLive(){
    if ( $this->is_live < 0 ) {
      return true;
    }
    else {
      return false;
    }
  }


  public function findById($id, $assoc=false){
    try {

      $conn = connectMysqli();

      if ($conn) {

        $sql = "SELECT
        	matches.id,
        	1st_team_id,
        	2nd_team_id,
        	winner_id,
        	playing_at,
        	matches.created_at,
        	updated_at,
        	playing_at - NOW() AS is_live,
        	(SELECT sum(amount) FROM bets WHERE match_id = matches.id) AS sum_amount,
        	(SELECT sum(amount) FROM bets WHERE match_id = matches.id AND team_id = 1st_team_id) AS 1st_team_amount,
        	(SELECT sum(amount) FROM bets WHERE match_id = matches.id AND team_id = 2nd_team_id) AS 2nd_team_amount
          FROM `matches` LEFT JOIN bets ON matches.id = bets.match_id WHERE matches.id = $id LIMIT 1;";


        $result = $conn->query($sql);

        if ($result->num_rows > 0) {

          $returned = $result->fetch_assoc();

          if ($assoc) {
            return $returned;
          }

          else {
            $this->id = $returned['id'];

            $this->firstTeam = $returned['1st_team_id'];
            $this->firstTeamAmount = $returned['1st_team_amount'];

            $this->secondTeam = $returned['2nd_team_id'];
            $this->secondTeamAmount = $returned['2nd_team_amount'];

            $this->sumAmount = $returned['sum_amount'];

            $this->winner = $returned['winner_id'];
            $this->playing_at = $returned['playing_at'];
            $this->is_live = $returned['is_live'];
            $this->created_at = $returned['created_at'];
            $this->updated_at = $returned['updated_at'];
          }

        }
        else {
          throw new Exception("Match not found");
        }
      }
      else {
        throw new Exception("Problem with connect");
      }
    } catch (Exception $e) {
      $_SESSION['notice'] = $e->getMessage();
    } finally {
      $conn->close();
    }

  }

  public function updateWinner(){
    try {
      $conn = connectMysqli();

      if (!$conn->error) {

        $sql = "UPDATE matches SET winner_id = $this->winner WHERE id = $this->id;";
          //"SELECT amount, (amount* $percent *0.9) FROM bets WHERE match_id = 16 AND team_id = 2";
        if( $conn->query($sql) ){

          if ($this->winner === -1) {
            $sql = "UPDATE users INNER JOIN bets ON users.id = user_id SET coins = coins + amount WHERE match_id = $this->id;";
          }

          else {

            if ($this->winner == $this->firstTeam) {
              $p = $this->getFirstTeamPercent()/100;
            }
            elseif ( $this->winner == $this->secondTeam ){
              $p = $this->getSecondTeamPercent()/100;
            }

            $sql = "UPDATE users INNER JOIN bets ON users.id = user_id SET coins = coins + (amount*(1/$p-1)*0.8) + amount WHERE match_id = $this->id AND team_id = $this->winner;";
          }


          $conn->query($sql);

          $_SESSION['notice'] = "Match Confirmed";
        }
        else {
          throw new Exception("Error with confirmig match");
        }
      }
      else {
        throw new Exception("Error while trying to connect ".$conn->error);
      }

    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
    } finally {
      $conn->close();
    }
  }

  public function pushToDb(){
      try {
        $conn = connectMysqli();

        if (!$conn->error) {

          $sql = "INSERT INTO `matches`
            (`id`, `1st_team_id`, `2nd_team_id`, `winner_id`, `playing_at`, `created_at`, `updated_at`)
            VALUES (NULL, '$this->firstTeam', '$this->secondTeam', NULL, '$this->playing_at', NOW(), NOW());";

          if( $conn->query($sql) ){
            $_SESSION['notice'] = "Match added";
          }
          else {
            throw new Exception("Error with adding match");
          }
        }
        else {
          throw new Exception("Error while trying to connect".$conn->error);
        }

      } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
      } finally {
        $conn->close();
      }

    }
}

class User {
  private $id;

  private $nick;
  private $password;
  private $role;
  private $coins;

  public function getId(){
    return $this->id;
  }

  public function getNick(){
    return $this->nick;
  }

  public function getRole(){
    return $this->role;
  }

  public function getCoins(){
    return $this->coins;
  }

  public function setId($id){
    $this->id = $id;
  }

  public function setNick($nick){
    $this->nick = $nick;
  }

  public function setPassword($password){
    $this->password = $password;
  }

  public function setCoins($coins){
    $this->coins = $coins;
  }

  /*public function setRole(){
    $this->role = $role;
  }*/



  public function findById($id){
    try {
      $conn = connectMysqli();

      if (!$conn->error) {

        $sql = "SELECT * FROM users WHERE id = $id LIMIT 1;";

        if( $result = $conn->query($sql) ){

          if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $this->id = $row['id'];
            $this->nick = $row['nick'];
            $this->role = $row['role'];
            $this->coins = $row['coins'];

          }
          else {
            throw new Exception("User not found");
          }

        }
        else {
          throw new Exception("Error with user");
        }
      }
      else {
        throw new Exception("Error while trying to connect ".$conn->error);
      }

    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
    } finally {
      $conn->close();
    }
  }


  public function checkNickDuplicate(){
    try {
      $conn = connectMysqli();

      if (!$conn->error) {

        $sql = "SELECT * FROM users WHERE nick = '$this->nick';";

        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          return true;
        }
        else {
          return false;
        }
        throw new Exception("Error with user");

      }
      else {
        throw new Exception("Error while trying to connect ".$conn->error);
      }

    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
    } finally {
      $conn->close();
    }
  }


  public function pushToDb(){
    try {
      $conn = connectMysqli();

      if (!$conn->error) {

        $sql = "INSERT INTO `users`
          (`id`, `nick`, `pass`, `role`, `coins`, `created_at`) VALUES
          (NULL, '$this->nick', '$this->password', NULL, '10000', NOW());";

        if($conn->query($sql) ){
          $_SESSION['notice'] = "User created";
        }
        else {
          throw new Exception("Error with user");
        }
      }
      else {
        throw new Exception("Error while trying to connect ".$conn->error);
      }

    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
    } finally {
      $conn->close();
    }
  }


  public function findByNickAndPass($nick, $pass){
    try {
      $conn = connectMysqli();

      if (!$conn->error) {

        $sql = "SELECT * FROM users WHERE nick = '$nick' AND pass = '$pass' LIMIT 1;";
        $result = $conn->query($sql);

        if( $result ){

          if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            $this->id = $row['id'];
            $this->nick = $row['nick'];
            $this->role = $row['role'];
            $_SESSION['user_id'] = $this->id;
            $_SESSION['notice'] = "Correctly logged in";
            currentUser();

          }
          else {
            throw new Exception("Wrong password or nickname");
          }

        }
        else {
          throw new Exception("Error with user");
        }
      }
      else {
        throw new Exception("Error while trying to connect ".$conn->error);
      }

    } catch (Exception $e) {
      $_SESSION['u_error'] = $e->getMessage();
    } finally {
      $conn->close();
    }
  }


  public function minusCoins($value){
    try {
      $conn = connectMysqli();

      if (!$conn->error) {
        $coins = $this->coins - $value;
        $sql = "UPDATE users SET coins = $coins WHERE id = $this->id";
        $conn->query($sql);

      }
      else {
        throw new Exception("Error while trying to connect ".$conn->error);
      }

    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
    } finally {
      $conn->close();
    }
  }

}


class Bet {
  private $id;
  private $userId;
  private $matchId;
  private $teamId;
  private $amount;

  public function getId(){
    return $this->id;
  }

  public function getUserId(){
    return $this->userId;
  }

  public function getMatchId(){
    return $this->matchId;
  }

  public function getTeamId(){
    return $this->teamId;
  }

  public function getAmount(){
    return $this->amount;
  }


  public function setId($id){
    $this->id = $id;
  }

  public function setUserId($userId){
    $this->userId = $userId;
  }

  public function setMatchId($matchId){
    $this->matchId = $matchId;
  }

  public function setTeamId($teamId){
    $this->teamId = $teamId;
  }

  public function setAmount($amount){
    $this->amount = $amount;
  }


  public function findById($id){
    try {
      $conn = connectMysqli();

      if (!$conn->error) {

        $sql = "SELECT * FROM bets WHERE id = $id LIMIT 1;";
        $result = $conn->query($sql);

        $row = $result->fetch_assoc();
        $this->id = $row['id'];
        $this->matchId = $row['match_id'];
        $this->userId = $row['user_id'];
        $this->teamId = $row['team_id'];
        $this->amount = $row['amount'];

      }
      else {
        throw new Exception("Error while trying to connect ".$conn->error);
      }

    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
    } finally {
      $conn->close();
    }
  }


  public function pushToDb(){
    try {
      $conn = connectMysqli();

      if (!$conn->error) {

        $sql = "INSERT INTO bets
          (`id`, `user_id`, `match_id`, `team_id`, `amount`, `created_at`) VALUES
          (NULL, '$this->userId', '$this->matchId', '$this->teamId', '$this->amount', NOW());";

        $result = $conn->query($sql);

      }
      else {
        throw new Exception("Error while trying to connect ".$conn->error);
      }

    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
    } finally {
      $conn->close();
    }
  }

}


class Bets {

  public function findAllBets($where=""){
    try {
      $conn = connectMysqli();

      if (!$conn->error) {

        $sql = "SELECT * FROM bets WHERE $where;";

        $result = $conn->query($sql);


        $returnedArray = [];

        while($row = $result->fetch_assoc()){
          $bet = new Bet;

          $bet->setId($row['id']);
          $bet->setMatchId($row['match_id']);
          $bet->setUserId($row['user_id']);
          $bet->setTeamId($row['team_id']);
          $bet->setAmount($row['amount']);

          $returnedArray[] = $bet;

        }



        return $returnedArray;
      }
      else {
        throw new Exception("Error while trying to connect ".$conn->error);
      }

    } catch (Exception $e) {
      $_SESSION['error'] = $e->getMessage();
    } finally {
      $conn->close();
    }
  }

}


?>
