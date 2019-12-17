<?php
set_time_limit(1800);

require_once("minecraft/modules/bans.php");
require_once("minecraft/modules/account.php");
require_once("minecraft/modules/mysql.php");
require_once("minecraft/modules/pex.php");

$logFile = fopen(__DIR__ . "/cron.log", "a");

fwrite($logFile, "\n" . date("Y-m-d H:i:s") . " Started cronjob.\n");

//update bans
Bans::UpdateBans();

fwrite($logFile, date("Y-m-d H:i:s") . " Updated " . count(Bans::GetBans()) . " bans.\n");

//update profiles
if ($queryResult = $mysqli->query("SELECT uuid FROM accounts"))
{
	$i = 0;
	while($row = $queryResult->fetch_assoc())
	{
		Account::UpdateProfile($row["uuid"]);
		$i++;
	}
}

fwrite($logFile, date("Y-m-d H:i:s") . " Updated {$i} profiles.\n");

//promote members
$promoteMaxTime = time() - 604800;
$promotedMembers = [];
if ($queryResult = $mysqli->query("SELECT uuid FROM accounts WHERE signup_time<={$promoteMaxTime} AND rank='member'"))
{
	while($row = $queryResult->fetch_assoc())
	{
		if (Pex::SetRank($row["uuid"], "member+"))
		{
			$promotedMembers[] = $row["uuid"];
		}
	}
}

if ($promotedMembers)
{
	$uuids = implode("' OR uuid='", $promotedMembers);
	$mysqli->query("UPDATE accounts SET rank='member+' WHERE uuid='{$uuids}'");
}

fwrite($logFile, date("Y-m-d H:i:s") . " Promoted " . count($promotedMembers) . " members.\n");

//update forum
$cleanupTime = time() - 2592000;
$mysqli->query(
	"UPDATE forum_threads " .
	"INNER JOIN (" .
		"SELECT thread, MAX(created) as lastposttime FROM forum_posts " .
		"GROUP BY thread) " .
	"AS forum_posts " .
	"ON forum_posts.thread = forum_threads.id " .
	"SET active = 0 " .
	"WHERE lastposttime <= {$cleanupTime} AND sticky = 0");

fwrite($logFile, date("Y-m-d H:i:s") . " Performed forum cleanup.\n");

fwrite($logFile, date("Y-m-d H:i:s") . " Finished cronjob.\n");

fclose($logFile);
?>