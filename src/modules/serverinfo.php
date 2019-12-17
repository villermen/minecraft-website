<?php
require_once("minecraft/modules/account.php");

require_once("minecraft/MinecraftPing.php");
require_once("minecraft/MinecraftPingException.php");
use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;

class Serverinfo
{
	private static $info;

	//if expanding on this, cache all data once whenever needed first and then read it out, like rcon
	public static function GetOnlinePlayers()
	{
		//actually query if not done before
		if (!self::$info)
		{
			try
			{
				$ping = new MinecraftPing("localhost", 25565, 1);
				self::$info = $ping->Query();
				$ping->Close();
			}
			catch (MinecraftPingException $e)
			{
				return false;
			}
		}
		
		//return cached result
		if (!isset(self::$info["players"]["sample"]))
			return false;
		
		$players = [];
		foreach(self::$info["players"]["sample"] as $player)
		{
			$players[] = [
				"uuid" => Account::DeformatUUID($player["id"]),
				"name" => $player["name"]
			];
		}
		
		return $players;
	}
}
?>