<!DOCTYPE HTML>
<html>

<head>
<link rel="stylesheet" type="text/css" href="main.css">
<title>Character Sheet Demo Thing</title>
</head>
<body>
<div id="main">
<h2>NearlyEpic's PHP EVE API query thing</h2>
<div id="api">
<form method="post" action="">
<p>Key ID: <input type="text" name="keyid" required /> </p>

<p>Verification Code: <input type="text" name="vcode" required /> </p>
<p><input type="submit" value="Get API Stuff"></p><br/>

</form>
</div>

<?php
require_once 'vendor/autoload.php';

Use Pheal\Pheal;
Use Pheal\Core\Config;

Config::getInstance()->cache = new \Pheal\Cache\NullStorage();
Config::getInstance()->access = new \Pheal\Access\StaticCheck();

if (isset($_POST['vcode'])){
  $keyid = htmlspecialchars($_POST['keyid']);
  $vcode = htmlspecialchars($_POST['vcode']);
  $charid = 1;

  $ply = new Pheal($keyid,$vcode,'account');

  $ply_chars = $ply->Characters();
  $result = $ply_chars->toArray();

  foreach($result["result"]["characters"] as $character)
  {
    $charid = $character["characterID"];
    $charname = $character["name"];

    $char = new Pheal($keyid,$vcode,'char');

    $acct = $char->AccountBalance(array("characterID" => $charid));
    $result = $acct->toArray();
    $balance = $result["result"]["accounts"][0]["balance"];
    $balance = number_format($balance, 2);

    echo sprintf('<img src="https://image.eveonline.com/Character/%s_256.jpg" alt="%s\'s character image">
    ', $charid, $charname);

    echo sprintf('<p>%s has %s isk in their account.</p>
    ', $charname, $balance);
  }
} else {
echo '<p>Please enter your API information. Full keys only please.</p>';
}
?>
</div>
</body>
</html>
