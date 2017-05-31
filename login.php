<?php
 session_start();
 if (isset($_POST['user']) && isset($_POST['pass'])) {
  require_once 'settings.php';

  $user = htmlspecialchars($_POST['user']);
  $pass = htmlspecialchars($_POST['pass']);

  try {
  $evedb = new PDO('mysql:host=localhost;dbname=eve', $dbusr, $dbpass); //conn to db with user/pass defined in settings.php
  $evedb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $evedb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  $usrchk = $evedb->prepare('SELECT password FROM users WHERE username = ?');
  $usrchk->bindParam(1, $user);
  $usrchk->execute();
  $usrout = $usrchk->fetch();

  $passcorrect = password_verify($pass,$usrout[0]);

  if ($passcorrect) {
   $_SESSION['validated'] = true;
   $_SESSION['failedlogin'] = false;
   $_SESSION['username'] = $user;
   $_SESSION['logout'] = date('U') + ($_POST['timelist'] * 3600);
   header( "Location: home.php");
   die(); 
  } else {
   	header("Location: index.php");
	$_SESSION['failedlogin'] = true;
  }
  } catch (PDOException $e) {
   echo "<h2> failed. " . $e->getMessage() . "</h2>";
   die();
  }
 }
?>

