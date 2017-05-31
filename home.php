<?php
session_start();
require_once 'functions.php';
loginRedirect(2, 'index.php');

?>
<!DOCTYPE HTML>
<html>

<head>
<link rel="stylesheet" type="text/css" href="main.css">
<title>NearlyEpic's API Homepage</title>
</head>
<body>
<div id="main">
<?php
require_once 'settings.php';
require_once 'vendor/autoload.php';
use Pheal\Pheal;
use Pheal\Core\Config;

try
{
  Config::getInstance()->cache = new \Pheal\Cache\FileStorage($_SERVER['DOCUMENT_ROOT'].'/tmp/phealcache/');
  Config::getInstance()->access = new \Pheal\Access\StaticCheck();
}
catch (PDOException $e) {
  echo get_class($e) . "<br/>";
  echo $e->getMessage() . "<br/>";
  die();
}

$getapi = new PDO('mysql:host=localhost;dbname=eve', $dbusr, $dbpass);
$getapi->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$getapi->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

try {

$query = $getapi->prepare('SELECT keyid,vcode FROM users WHERE username=?');
$query->bindParam(1, $_SESSION['username']);
$query->execute();
$gotapi = $query->fetch();

} catch (PDOException $e) {
 echo 'Failed: ' . $e->getMessage();
 die();
}
$keyid = $gotapi[0];
$vcode = $gotapi[1];


$account = new Pheal($keyid, $vcode, 'account');
try
  {
    $characters = $account->Characters();
    $result = $characters->toArray();
  }
  catch (\Pheal\Exceptions\PhealException $e)
  {
    echo sprintf(
      "an exception was caught! Type: %s Message: %s",
      get_class($e),
      $e->getMessage()
    );
}
$char = new Pheal($keyid, $vcode, "char");

foreach($result['result']['characters'] as $x) {
 echo "<div id=\"char\">";
 echo sprintf("<img src=\"http://image.eveonline.com/Character/%s_128.jpg\"><br/>", $x['characterID']);
 echo "</div>";
 $charinfo = $char->CharacterSheet(array("characterID" => $x['characterID']));
 echo sprintf("Name: %s <br/> ISK: %s <br/>", $charinfo->name, $charinfo->balance);
}

?>


</div>

<form action="logout.php">
  <input type="submit"  value='Log Out...'>
</form>

</html>
