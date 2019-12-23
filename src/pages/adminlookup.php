<?php
error_reporting(E_ALL);

require_once("minecraft/modules/account.php");

// Just putting this out here in case I want to integrate it into lookup
// $playtimes = [];
// foreach (glob('.../worlds/hub/stats/*.json') as $statFile) {
//     $stats = json_decode(file_get_contents($statFile), true);
//
//     $uuid = basename($statFile, '.json');
//
//     if (isset($stats['stats']['minecraft:custom']['minecraft:play_one_minute'])) {
//         $playtime = (int)$stats['stats']['minecraft:custom']['minecraft:play_one_minute'];
//     } elseif (isset($stats['stat.playOneMinute'])) {
//         $playtime = (int)$stats['stat.playOneMinute'];
//     } else {
//         // Is possible when somebody fails to join the server for the first time
//         continue;
//     }
//
//     $playtimes[$uuid] = $playtime;
// }
//
// arsort($playtimes);
//
// dd($playtimes);

if (Account::$loggedInAccount && Account::$loggedInAccount->admin)
{
	$content.="<h1>Player Lookup</h1>
	
	<form method='post'>
		<input type='text' name='player' maxlength='32' placeholder='Name or UUID' />
	</form>
	<br />";

	if (isset($_POST["player"]))
	{
		require_once("minecraft/modules/pex.php");
		require_once("minecraft/modules/bans.php");

		if ($account = Account::GetDetails($_POST["player"]))
		{
			$bannedString = $account->banned ? "Eyup" : "Nah";

			$content .= "
				<table>
					<tbody>
						<tr><td>Name</td><td>{$account->name}</td></tr>
						<tr><td>UUID</td><td>{$account->formattedUUID}</td></tr>
						<tr><td>Rank</td><td>{$account->rank}</td></tr>
						<tr><td>Banned</td><td>{$bannedString}</td></tr>
					</tbody>
				</table>";
		}
		else
			$content .= "<div class='notice red'>There exists no account with that Username/UUID.</div>";
	}

		/*

		if (bans_isBanned($name,$reason, $when))
		{
			$when = timePassed($when);
			$bannedstring="<span class='red'>Eyup, $when.<br />$reason</span>";
		}
		else
			$bannedstring="Nah.";

		if ($joinsresult=mysql_fetch_array(mysql_query("SELECT uuid,time,promoted FROM joins WHERE uuid='$uuid'")))
		{
			if ($joinsresult["promoted"]==1)
				$autopromote="<span class='green'>Succesfully promoted</span>";
			elseif ($joinsresult["promoted"]==0)
				$autopromote="<span class='blue'>Not yet promoted</span>";
			else
				$autopromote="<span class='red'>Failed</span>";

			$jointime=timePassed($joinsresult["time"])." (".date("j-n-Y G:i",$joinsresult["time"]).")";
		}
		else
		{
			$autopromote="No record";
			$jointime="Unknown";
		}

		$content.="
		<table>
			<tr><td>Name</td><td>$displayname</td></tr>
			<tr><td>Rank</td><td><span class='$rankcolor'>$groupname</span></td></tr>
			<tr><td>Banned</td><td>$bannedstring</td></tr>
			<tr><td>Autopromote</td><td>$autopromote</td></tr>
			<tr><td>Joined</td><td>$jointime</td></tr>
		</table>";
	}


	/*$content.="<h1>Joins</h1>

	<div class='center'>
		This list spans from 19-10-2011 (the start of the old site/account system) to now.<br />
		Note though: accounts on the database used to get removed after a cleanup which removed all the banned accounts, so this list is incomplete.<br />
		<br />
		<span class='blue'>Not yet promoted</span><br />
		<span class='green'>Promoted</span><br />
		<span class='red'>Error promoting (already promoted, rcon failure, other bugs)</span><br />
		Hover over the time for exact dates.
	</div>";

	$i=0;
	$joinsArray=array();
	$result=mysql_query("SELECT name,time,promoted FROM joins ORDER BY time DESC");
	while ($row=mysql_fetch_array($result))
	{
		$joinsArray[$i]["name"]=$row["name"];
		$joinsArray[$i]["time"]=$row["time"];
		$joinsArray[$i++]["status"]=$row["promoted"];
	}

	$content.="<table><tr><th>Name</th><th>Joined</th></tr>";

	foreach($joinsArray as $join)
	{
		$name=$join["name"];
		$timeRelative=timePassed($join["time"]);
		$timeDate=date("j-n-Y G:i",$join["time"]);

		if ($join["status"]==1)
			$statusColor="green";
		elseif ($join["status"]==0)
			$statusColor="blue";
		else
			$statusColor="red";
		$content.="<tr class='$statusColor'><td>$name</td><td title='$timeDate'>$timeRelative</td></tr>";
	}
	$content.="</table>";*/
}
else
	$content.="<div class='notice red'>You aren't logged in.<br />Log in before using this page (if you are an admin)</div>";
?>
