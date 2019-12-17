<?php
require_once("minecraft/modules/serverinfo.php");

$players = Serverinfo::GetOnlinePlayers();

$content .= "<h1>Players currently online</h1>
	<div class='center'>";

foreach($players as $player)
{
	$content .= "
		<div class='horizontalsection'>
			<img src='playerhead.php?p={$player["uuid"]}&s=36' alt='' />
			<div>{$player["name"]}</div>
		</div>
	";
}

$content .= "</div>";
?>