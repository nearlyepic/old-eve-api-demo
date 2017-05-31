<!DOCTYPE HTML>
<html>
<head>
<title>NearlyEpic's FW farming thing</title>
<link rel="stylesheet" type="text/css" href="main.css">
<link href='http://fonts.googleapis.com/css?family=PT+Sans+Narrow' rel='stylesheet' type='text/css'>
</head>
<body>
<div id="main">
<h2>get rich quick farming: offensive complexes</h2>
<div id="api">
<form method="get" action="" id="farm_form">
<p>Maximum Jumps/Hour: (20 is recommended)

<select name="jumps" form="farm_form">
	<option value="10">10</option>
	<option value="15">15</option>
	<option value="20">20</option>
	<option value="30">30</option>
</select>
</p>
<p> Your Militia:
<select name="militia" form="farm_form">
	<option value="amarr">Amarr/Caldari</option>
	<option value="gallente">Gallente/Minmatar</option>
</select>
</p>
<input type="submit" value="get your farm on!">
</form>
<br/>
</div>
<?php
require_once "vendor/autoload.php";
Use Pheal\Pheal;
Use Pheal\Core\Config;
if (isset($_GET['jumps']) && isset($_GET['militia'])) {
$maxjumps = $_GET['jumps'];
$militia = $_GET['militia'];
try {
 Config::getInstance()->cache = new \Pheal\Cache\FileStorage($_SERVER['DOCUMENT_ROOT'].'/tmp/phealcache/'); //use file caching so we don't get banned from the EVE API


 Config::getInstance()->access = new \Pheal\Access\StaticCheck();
 } catch (PhealException $e) {
         echo get_class($e) . "<br/>";
         echo $e->getMessage() . "<br/>";
	die();
}


$facwar = new Pheal();

$systems = $facwar->mapScope->FacWarSystems(); //get all the system ID's of solar systems that are faction warfare space
$systems = $systems->toArray(); // put them in an array

$jumps = $facwar->mapScope->Jumps(); //get the statistics for jumps in/out for every solar system in the game
$jumps = $jumps->toArray();

$goodSystemNames = array(); //initialize our array of gal/min systems

$evedb = new PDO('mysql:host=localhost;dbname=eve_dump', 'evedb', 'eve'); //connect to db that contains the static data export
$evedb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //throw exceptions when something goes wrong
$evedb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); //don't emulate preparing statements
?>
<div id="systems">
<?php
if ($militia == "amarr") {
foreach ($systems['result']['solarSystems'] as $x) { //run through factional warfare systems
	if (($x['occupyingFactionName'] == "Gallente Federation") || ($x['occupyingFactionName'] == "Minmatar Republic")) { // make an array consisting of systems that are controlled by the opposing militia
		$goodSystemNames[$x['solarSystemID']] = $x['solarSystemName']; //associate solar system IDs of enemy controlled systems to the system names, for later
	}
}
foreach ($jumps['result']['solarSystems'] as $x) { //iterate through every system in the game and its jump statistics
	$curSysID = $x['solarSystemID']; // make the code cleaner by assigning a variable to the current system we're checking
	if (isset($goodSystemNames[$curSysID]) && $x['shipJumps'] <= $maxjumps) { //if the current system is in our table, and there have been less than 15 jumps in the last hour, print it out
		try {
			/**
			* queries the Static Data Export (SDE) we get from CCP to get region IDs of solar systems
			* We could do this with the API but since we have the data here, it's probably faster than
			* making an HTTP request and processing the result
			*/
			$region = $evedb->prepare('SELECT regionID FROM mapSolarSystems WHERE solarSystemID = ?');
			$region->bindParam(1, $curSysID);
			$region->execute();
			$regionid  = $region->fetch();
		 } catch(PDOException $e) {
			echo "Exception! : " . $e;
		}
		try {
			$regionName = $evedb->prepare('SELECT regionName from mapRegions WHERE regionID = ?'); //Similar to above, but for region name now. should be improved in the future.
			$regionName->bindParam(1, $regionid[0]);
			$regionName->execute();
			$regionOut = $regionName->fetch();
		} catch(PDOException $e) {
			echo "Exception! : " . $e;
		}
		$webRegion = str_replace(" ", "_", $regionOut[0]); //strip out the spaces from our region name so we can insert it into the URL for dotlan
		echo "<div id=\"system\">"; //start printing out HTML for the system divs
		echo $goodSystemNames[$curSysID];
		echo sprintf("<h4>%sj, %s</h4>", $x['shipJumps'], $regionOut[0]); // this could be all one line but i'm only doing it this way because it's easier to read
		echo sprintf("<a href=\"http://evemaps.dotlan.net/map/%s/%s\">", $webRegion, $goodSystemNames[$curSysID]);
		echo "<span class=\"link-spanner\"></span>";
		echo "</a>";
		echo "</div>";
	}
}
} else {

foreach ($systems['result']['solarSystems'] as $x) {
	if (($x['occupyingFactionName'] == "Amarr Empire") || ($x['occupyingFactionName'] == "Caldari State")) { //duplicate code, should split off into function
		$goodSystemNames[$x['solarSystemID']] = $x['solarSystemName'];
	}
}
foreach ($jumps['result']['solarSystems'] as $x) { //this is just a duplicate as the above code, but it'll work for now
	$curSysID = $x['solarSystemID'];
	if (isset($goodSystemNames[$curSysID]) && $x['shipJumps'] <= $maxjumps) {
		try {
			$region = $evedb->prepare('SELECT regionID FROM mapSolarSystems WHERE solarSystemID = ?');
			$region->bindParam(1, $curSysID);
			$region->execute();
			$regionid  = $region->fetch();
		 } catch(PDOException $e) {
			echo "Exception! : " . $e;
		}
		try {
			$regionName = $evedb->prepare('SELECT regionName from mapRegions WHERE regionID = ?');
			$regionName->bindParam(1, $regionid[0]);
			$regionName->execute();
			$regionOut = $regionName->fetch();
		} catch(PDOException $e) {
			echo "Exception! : " . $e;
		}

		$webRegion = str_replace(" ", "_", $regionOut[0]);
		echo "<div id=\"system\">";
		echo $goodSystemNames[$curSysID];
		echo sprintf("<h4>%sj, %s</h4>", $x['shipJumps'], $regionOut[0]);
		echo sprintf("<a href=\"http://evemaps.dotlan.net/map/%s/%s\">", $webRegion, $goodSystemNames[$curSysID]);
		echo "<span class=\"link-spanner\"></span>";
		echo "</a>";
		echo "</div>";
	}
}
}
}
?>
</div>
</div>
</body>
</html>
