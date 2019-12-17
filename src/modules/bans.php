<?php
require_once("minecraft/modules/account.php");

class Bans
{
	//loaded once per page
	private static $bans = false;

	public static function UpdateBans()
	{
		global $mysqli;

		$banFile =  "/srv/minecraft/banned-players.json";

		//there's probably something wrong if there are no bans =(
		if ($bans = @json_decode(file_get_contents($banFile)))
		{
			//construct query
			$query = "INSERT INTO bans (uuid,name,time) VALUES ";

			$saveBans = [];

			foreach($bans as $ban)
			{
				$uuid = Account::DeformatUUID($ban->uuid);
				$name = $ban->name;
				$time = strtotime($ban->created);
				$query .= "('{$uuid}','{$name}',{$time}),";

				$saveBans[$uuid]["name"] = $name;
				$saveBans[$uuid]["time"] = $time;
			}

			self::$bans = $saveBans;

			//remove last comma
			$query = substr($query, 0, -1);

			//lock the table for the duration, so that other processes will never retrieve the empty table
			$mysqli->query("LOCK TABLES bans WRITE");
			$mysqli->query("DELETE FROM bans");
			$mysqli->query($query);
			$mysqli->query("UNLOCK TABLES");
		}
	}

	public static function GetBans()
	{
		global $mysqli;

		if (!self::$bans)
		{
			//read from database and store in class property
			$result = $mysqli->query("SELECT uuid,name,time FROM bans ORDER BY time DESC");

			if (!$mysqli->errno && $mysqli->affected_rows)
			{
				$i = 0;
				while($row = $result->fetch_assoc())
				{
					$bans[$row["uuid"]]["name"] = $row["name"];
					$bans[$row["uuid"]]["time"] = $row["time"];
				}

				self::$bans = $bans;
			}
		}

		return self::$bans;
	}

	public static function IsBanned($nameOrUUID)
	{
		//make sure the bans are loaded
		self::GetBans();

		$uuid = Account::ResolveUUID($nameOrUUID);

		return isset(self::$bans[$uuid]);
	}
}
?>
