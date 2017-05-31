<?php
require_once 'vendor/autoload.php';
require_once 'functions.php';
date_default_timezone_set('UTC');
use Pheal\Pheal;
session_start();

loginRedirect(1, 'home.php');
?>

<!DOCTYPE HTML>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="main.css">
		<title>NearlyEpic's EVE Pages</title>
	</head>
	<body>
	<div id="main">
	<h2>NearlyEpic's Eve API thing.</h2>
	<div id="api">
		<form method="post" action='login.php' id="loginform">
		<p>Username: <input type="text" name="user" required></p>
		<p>Password: <input type="password" name="pass" required></p>
		<p>Stay logged in?
		<select name="timelist" form="loginform">
			<option value="1">No</option>
			<option value="12">12 Hours</option>
			<option value="24">24 Hours</option>
			<option value="48">48 Hours</option>
		</select>	
		<br/><br/>
		<a href="register.php">Register</a><input type="submit" value="login"><br/>
		</form>
		<?php
		if(isset($_SESSION['failedlogin']) && $_SESSION['failedlogin']) {
		echo "<h3> Username or Password incorrect. </h3>";
		unset($_SESSION['failedlogin']);
		}
		?>
	</div>
	<?php
	$tq = new Pheal(); //create the thing
	$serverstatus = $tq->serverScope->ServerStatus(); //tranquility server status

	echo sprintf(
	"<br/><center>Tranquility is %s, and there are %s players online.</center>",
	$serverstatus->serverOpen ? "up" : "down",
	$serverstatus->onlinePlayers
	);

	?>
	</body>
</html>
