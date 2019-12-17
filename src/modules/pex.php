<?php
require_once("minecraft/modules/account.php");
require_once("minecraft/modules/rcon.php");

class Pex
{
	//returns rankvalue of the given user or false if he does not exist
	public static function GetRank($nameOrUUID, &$colorCode = "&7")
	{
		if ($uuid = Account::FormatUUID(Account::ResolveUUID($nameOrUUID)))
		{
			if ($reply = Rcon::RunCommand("pex user {$uuid} group list"))
			{
				$in = strrpos($reply, "\n  ") + 3;
				$out = strrpos($reply, "\n");
				$rank = substr($reply, $in, $out - $in);
				
				return $rank;
			}
		}
		
		return false;
	}
		
	public static function SetRank($nameOrUUID, $rank)
	{
		if ($uuid = Account::FormatUUID(Account::ResolveUUID($nameOrUUID)))
		{
			if (Rcon::RunCommand("pex user {$uuid} group set {$rank}"))
				return true;
			else
				return false;
		}
	}
		
	public static function SetPrefix($nameOrUUID, $prefix)
	{
		if ($uuid = Account::FormatUUID(Account::ResolveUUID($nameOrUUID)))
		{
			if (Rcon::RunCommand("pex user {$uuid} prefix \"{$prefix}\""))
				return true;
			else
				return false;
		}
	}
		
	public static function SetSuffix($nameOrUUID, $suffix)
	{
		if ($uuid = Account::FormatUUID(Account::ResolveUUID($nameOrUUID)))
		{
			if (Rcon::RunCommand("pex user {$uuid} suffix \"{$suffix}\""))
				return true;
			else
				return false;
		}
	}
}
?>
