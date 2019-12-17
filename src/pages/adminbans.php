<?php
require_once("minecraft/modules/account.php");
require_once("minecraft/modules/bans.php");

if (Account::$loggedInAccount && Account::$loggedInAccount->admin)
{
	$bans = Bans::GetBans();
	$banCount = count($bans);

	$content .= "
		<h1>Bans</h1>
		
		<div class='center'>
			Use CTRL + F to search for a name.<br />
			Hover over data for more info.<br />
			<br />
			Total bans: {$banCount} (that's like, a lot)
		</div>
		
		<table>
			<thead>
				<tr>
					<th>Banned</th>
					<th>When</th>
				</tr>
			</thead>
			
			<tbody>
	";
			
	foreach($bans as $uuid => $details)
	{
		$uuid = Account::FormatUUID($uuid);
		$name = $details["name"];
		$when = Misc::TimeAgo($details["time"]);
		$whenDate = date("j-n-Y G:i", $details["time"]);
		
		$content .= "
			<tr>
				<td title='{$uuid}'>{$name}</td>
				<td title='{$whenDate}'>{$when}</td>
			</tr>";
	}
		
	$content .= "</tbody></table>";

	}
else
	$content.="<div class='notice red'>You aren't logged in.<br />Log in before using this page (if you are an admin)</div>";
?>