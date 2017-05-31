<?php
require_once 'settings.php';
require_once 'vendor/autoload.php';


//import namespace
use Pheal\Pheal;
use Pheal\Core\Config;

$keyID = 3301518;
$vCode = "YbV2XLYCpxS34elB4LbxHlU9G2fUyw2eZ56cmpmZFai9uREOXxvqXTmTjY9lPVvi";
$characterID = 1623919097;

try {
Config::getInstance()->cache = new \Pheal\Cache\PdoStorage('mysql:host=localhost;dbname=pheal', "phealng", "phealng");


Config::getInstance()->access = new \Pheal\Access\StaticCheck();
} catch (PDOException $e) {
	echo get_class($e) . "<br/>";
	echo $e->getMessage() . "<br/>";
}
$pheal = new Pheal($keyID, $vCode, "char");

try {

    $response = $pheal->CharacterSheet(array("characterID" => $characterID));

    echo sprintf(
        "Hello Visitor, Character %s was created at %s is of the %s race and belongs to the corporation %s",
        $response->name,
        $response->DoB,
        $response->race,
        $response->corporationName
    );


} catch (\Pheal\Exceptions\PhealException $e) {
    echo sprintf(
        "an exception was caught! Type: %s Message: %s",
        get_class($e),
        $e->getMessage()
    );
}
