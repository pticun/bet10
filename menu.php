<div id="menu">
  <ul>

    <span class="bar" style="float: left;">
      <li><a href="#">Regulations</a></li>
      <?php if ( !isset($currentUser) ): ?>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
      <?php endif; ?>

      <?php if( isset($currentUser) ): ?>
        <li> <?php echo $currentUser->getNick(); ?> </li>
        <li id="user-coins"> <?php echo $currentUser->getCoins(); ?> </li>
      <?php endif; ?>
    </span>

    <div class="bar">
      <li><a href="index.php">Bets</a></li>
    </div>

    <div class="bar" style="float: right;">
      <?php if ( isset($currentUser) ): ?>
        <?php if ($currentUser->getRole() !== null ): ?>
          <li><a href="manage.php">Manage</a></li>
        <?php endif; ?>
        <li><a href="logout.php">Logout</a></li>
        <li><a href="profile.php">Account</a></li>
      <?php endif; ?>
      <li><a href="#">Contact</a></li>
    </div>

    <div style="clear:both;"></div>
  </ul>
</div>

<?php
  if(isset($_SESSION['notice'])){
    ?>
    <div class="banner info">
        <?php echo $_SESSION['notice']; ?>
    </div>
    <?php
    unset($_SESSION['notice']);
  }

?>
