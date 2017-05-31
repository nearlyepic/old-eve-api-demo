<!DOCTYPE HTML>
<html>

<head>
<link rel="stylesheet" type="text/css" href="main.css">
<title>Registration</title>
</head>

<div id="main">
<h2>NearlyEpic's EVE API Registration Page</h2>
<div id="api">
<form method="post" action="">
<p>Username: <input type="text" name="user" required></p>
<p>Password: <input type="password" name="pass" required></p>
<p>API KeyID: <input type="text" name="keyid" required></p>
<p>API VCode: <input type="text" name="vcode" required></p>
<input type="submit" value="Register"><br/>
</form>
</div>

<?php
if (isset($_POST['user']) && isset($_POST['pass'])&& isset($_POST['keyid'])&& isset($_POST['vcode'])) {
include 'settings.php'; //database connection settings

date_default_timezone_set('UTC');

$user = htmlspecialchars($_POST['user']); //taking the data submitted and assigning it to variables
$pass = htmlspecialchars($_POST['pass']);
$keyid = htmlspecialchars($_POST['keyid']);
$vcode = htmlspecialchars($_POST['vcode']);
$client_ip = $_SERVER['REMOTE_ADDR'];
$curdate = date('U');

$ipdb = new PDO('mysql:host=localhost;dbname=eve', $dbusr, $dbpass); //conn to db with user/pass defined in settings.php
$ipdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$ipdb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


try {
$usrchk = $ipdb->prepare('SELECT * FROM users WHERE username = ?'); //goodbye, SQL injection
$usrchk->bindParam(1, $user); //insert the data supplied from the form into the query
$usrchk->execute(); //run the query
$usrout = $usrchk->fetch(); //put the results of the query into an array
} catch (PDOException $e) {
	echo 'Failed, ' . $e->getMessage();
	die();
}


if (strcasecmp($usrout[0], $user) != 0) {
	$hashopts = [
		'cost' => 10, //hashing options, you can do other stuff here like change the salt generation method
	];

	$hashedpw = password_hash($pass, PASSWORD_BCRYPT, $hashopts);

	try {
	$usr_create = $ipdb->prepare('INSERT INTO users (username, password, keyid, vcode, creation, ip) VALUES (?,?,?,?,?,?)');
	$usr_create->bindParam(1, $user);
	$usr_create->bindParam(2, $hashedpw);
	$usr_create->bindParam(3, $keyid);
	$usr_create->bindParam(4, $vcode);
	$usr_create->bindParam(5, $curdate);
	$usr_create->bindParam(6, $client_ip);
	$usr_create->execute();
	//echo "Sent query. <br>";
	} catch (Exception $e) {
		echo "Failed to create user: " . $e->getMessage();
		die();
	}
	echo "<h3>User Created Successfully!</h3><br/><a href=\"index.php\">Return to home.</a>";
} else {
	echo"<h3> Username already exists!</h3>";
}
} else {
	echo"<p> Please complete all fields. Full API Keys only.</p>";
}
?>
</html>
