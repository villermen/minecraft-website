<?php
require_once("minecraft/modules/skin.php");

$nameOrUUID = $_GET["p"];

if (isset($_GET["s"]) && intval($_GET["s"]))
	$size = $_GET["s"];
else
	$size = 18;


if ($image = Skin::CreatePlayerhead($nameOrUUID, $size))
{
	header("Content-type: image/png");
	echo $image;
}
?>