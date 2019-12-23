<?php

use Symfony\Component\HttpFoundation\Request;
use Villermen\Minecraft\App;

require_once(__DIR__ . '/../vendor/autoload.php');

$request = Request::createFromGlobals();
$app = new App();
$response = $app->run($request);
$response->prepare($request);
$response->send();

exit();


require_once("minecraft/modules/mysql.php");
require_once("minecraft/modules/misc.php");
require_once("minecraft/modules/account.php");

//banner background
$bannerBackground = false;
//$bannerBackground = "img/banners/onmate_vacguy99_creativechristmas.png"; //override

if (!$bannerBackground)
{
	if ($bannerBackgrounds = glob("img/banners/*.png"))
	{
		$bannerBackground = $bannerBackgrounds[rand(0, count($bannerBackgrounds) - 1)];
		$bannerBackground = "img/banners/" . basename($bannerBackground);
	}
}
?>
