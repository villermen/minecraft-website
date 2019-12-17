<?php
require_once("minecraft/modules/mysql.php");
require_once("minecraft/modules/misc.php");
require_once("minecraft/modules/pex.php");

class AccountDetails
{
	public $uuid;
	public $formattedUUID;
	public $name;
	public $password;
	public $rank;
	public $banned;
	public $email;
	public $donator;
	public $admin;
	public $villermen;
	
	public function __construct($uuid, $name, $password, $rank, $banned, $email)
	{
		if ($uuid == null)
			$this->uuid = Account::ResolveUUID($name);
		else
			$this->uuid = Account::ResolveUUID($uuid);
		
		$this->formattedUUID = Account::FormatUUID($this->uuid);

		$this->name = $name;
		$this->password = $password;
		$this->rank = $rank;
		$this->banned = $banned;
		$this->email = $email;
		
		if ($this->rank == "donator-" ||
			$this->rank == "donator" ||
			$this->rank == "moneybagd" ||
			$this->rank == "admin" ||
			$this->rank == "moneybaga")
			$this->donator = true;
		else
			$this->donator = false;
			
		if ($this->rank == "admin" ||
			$this->rank == "moneybaga")
			$this->admin = true;
		else
			$this->admin = false;
			
		if ($this->uuid == "7ce734e04293464babe16ada78ec553b")
		{
			$this->donator = true;
			$this->admin = true;
			$this->villermen = true;
		}
		else
			$this->villermen = false;
	}
}

class Account
{
	public static $loggedInAccount = false;
	
	private static $storedNames = [];
	private static $storedUUIDs = [];

	//updates name and rank in the database
	public static function UpdateProfile($nameOrUUID)
	{
		global $mysqli;
		$uuid = self::ResolveUUID($nameOrUUID);
		$formattedUUID = self::FormatUUID($uuid);
		
		//update name
		self::ResolveName($uuid, true);	
		
		//update rank
		if ($rank = Pex::GetRank($formattedUUID))
			$mysqli->query("UPDATE accounts SET rank='{$rank}' WHERE uuid='{$uuid}'");
	}

	//adds dashes to an unformatted UUID
	public static function FormatUUID($uuid)
	{
		$length = strlen($uuid);
		
		if ($length == 36)
			return $uuid;
		elseif ($length == 32)
		{
			$uuid = substr_replace($uuid, "-", 20, 0); 
			$uuid = substr_replace($uuid, "-", 16, 0); 
			$uuid = substr_replace($uuid, "-", 12, 0); 
			$uuid = substr_replace($uuid, "-", 8, 0);
			return $uuid;
		}
		else
			return false;
	}

	//removes dashes from a formatted UUID
	public static function DeformatUUID($UUID)
	{
		$length = strlen($UUID);
		
		if ($length == 32)
			return $UUID;
		elseif ($length == 36)
			return str_replace("-", "", $UUID);
		else
			return false;	
	}

	//obtains username's uuid
	//checks database, then mojang api (and stores it in the database)
	//returns the same uuid (unformatted) if an uuid is given as the argument
	public static function ResolveUUID($nameOrUUID)
	{
		global $mysqli;
		
		//don't allow mysql breaking characters
		if ($mysqli->escape_string($nameOrUUID) != $nameOrUUID)
			return false;
			
		$length = strlen($nameOrUUID);
		//return argument (it's already an unformatted UUID)
		if ($length == 32)
			return $nameOrUUID;
		elseif ($length == 36)
		{
			//remove dashes and return
			return self::DeformatUUID($nameOrUUID);
		}
		else
		{
			$upperCaseName = strtoupper($nameOrUUID);
			
			//retrieve uuid if it can be resolved with the local lookup array
			if (isset(self::$storedUUIDs[$upperCaseName]))
				return self::$storedUUIDs[$upperCaseName];
			else
			{
				//retrieve uuid if it can be resolved with the database
				$result = $mysqli->query("SELECT uuid,name FROM uuids WHERE name='{$nameOrUUID}'");
				if ($mysqli->affected_rows != 0)
				{
					$result = $result->fetch_assoc();
					
					//store in local lookup table before returning
					self::$storedUUIDs[$upperCaseName] = $result["uuid"];
					
					return $result["uuid"];
				}
				else
				{
					//look up UUID with Mojang's API and format and store or update it
					$profile = json_decode(@file_get_contents("https://api.mojang.com/users/profiles/minecraft/{$nameOrUUID}"));
					
					if (!$profile || isset($profile->error))
						return false;
					else
					{
						$uuid = $profile->id;
						$name = $profile->name;
						
						if ($uuid)
						{
							//store in database before returning
							$mysqli->query("INSERT INTO uuids (uuid, name) VALUES ('{$uuid}', '{$name}') ON DUPLICATE KEY UPDATE name='{$name}'");
							
							//store in local lookup table before returning
							self::$storedUUIDs[$upperCaseName] = $uuid;
							self::$storedNames[$uuid] = $name;
							
							return $uuid;
						}
						else
							return false;
					}
				}
			}
		}
	}
	
	public static function ResolveName($nameOrUUID, $forceMojang = false)
	{
		global $mysqli;
		
		//resolve name to uuid first before resolving back (correctly formatting) to name
		if ($uuid = self::ResolveUUID($nameOrUUID))
		{
			//retrieve name if it can be resolved with the local lookup table
			if (!$forceMojang && isset(self::$storedNames[$uuid]))
				return self::$storedNames[$uuid];
			else
			{
				//retrieve name if it can be resolved with the database
				$result = $mysqli->query("SELECT name FROM uuids WHERE uuid='{$uuid}'");
				$result = $result->fetch_assoc();
				if ($mysqli->affected_rows != 0 && !$forceMojang)
				{
					//store in local lookup table before returning
					self::$storedNames[$uuid] = $result["name"];
					
					return $result["name"];
				}
				else
				{
					//look up name with Mojang's API and store it
					$profile = json_decode(@file_get_contents("https://api.mojang.com/user/profiles/{$uuid}/names"));
					
					if (!$profile || isset($profile->error))
						return false;
					else
					{
						$name = end($profile)->name; //<-- latest name, all names are contained in the api
						
						if ($name)
						{
							//store in database before returning
							$mysqli->query("INSERT INTO uuids (uuid, name) VALUES ('{$uuid}', '{$name}') ON DUPLICATE KEY UPDATE name='{$name}'");
							
							//store in local lookup table before returning
							self::$storedNames[$uuid] = $result["name"];
							
							return $name;
						}
					}
				}
			}
		}
		
		return false;
	}

	public static function GetDetails($nameOrUUID)
	{
		global $mysqli;
		
		$uuid = self::ResolveUUID($nameOrUUID);
		
		$queryResult = $mysqli->query("SELECT password,rank,uuids.name,EXISTS(SELECT uuid FROM bans WHERE bans.uuid=accounts.uuid) AS banned,email FROM accounts JOIN uuids ON accounts.uuid=uuids.uuid WHERE accounts.uuid='{$uuid}'");

		if (!$mysqli->errno && $mysqli->affected_rows)
		{
			$queryResult = $queryResult->fetch_assoc();
			return new AccountDetails($uuid, $queryResult["name"],  $queryResult["password"], $queryResult["rank"], $queryResult["banned"] == true, $queryResult["email"]);
		}
		else
			return false;
	}

	public static function UpdateLoggedInAccount()
	{
		global $mysqli, $account_loggedInAccount;
		
		if (isset($_COOKIE["LoginID"]))
		{
			//calculate identity
			$identity = md5($_SERVER["HTTP_USER_AGENT"] . $_COOKIE["LoginID"] . $_SERVER["REMOTE_ADDR"]);
			
			//get identity from database
			$queryResult = $mysqli->query("SELECT uuid FROM accounts WHERE login_identity='{$identity}' AND NOT EXISTS(SELECT uuid FROM bans WHERE bans.uuid=accounts.uuid)");
			if (!$mysqli->errno && $mysqli->affected_rows)
			{
				$uuid = $queryResult->fetch_assoc()["uuid"];
				$details = self::GetDetails($uuid);
				
				if ($details)
					self::$loggedInAccount = $details;			
			}
		}
	}

	public static function Login($nameOrUUID, $password, $persistent)
	{
		global $mysqli;
		
		//check if combination of uuid and password exist and are correct
		$uuid = self::ResolveUUID($nameOrUUID);
		
		if ($uuid)
		{
			$queryResult = $mysqli->query("SELECT uuid,password FROM accounts WHERE uuid='{$uuid}' AND NOT EXISTS(SELECT uuid FROM bans WHERE bans.uuid=accounts.uuid)");
			if (!$mysqli->errno && $mysqli->affected_rows == 1)
			{
				$queryResult = $queryResult->fetch_assoc();
				
				if (password_verify($password, $queryResult["password"]))
				{
					$loginID = Misc::GenerateRandomString();
					
					if ($persistent)
						$expirationTime = time() + 2678400;
					else
						$expirationTime = 0;
					
					if (setcookie("LoginID", $loginID, $expirationTime, "/minecraft/"))
					{
						//calculate and store identity in database
						$identity = md5($_SERVER["HTTP_USER_AGENT"] . $loginID . $_SERVER["REMOTE_ADDR"]);
						$mysqli->query("UPDATE accounts SET login_identity='{$identity}' WHERE uuid='{$uuid}'");
						
						if (!$mysqli->errno && $mysqli->affected_rows)
						{
							//force login for this page
							$_COOKIE["LoginID"] = $loginID;
							self::UpdateLoggedInAccount();
							
							return true;
						}				
					}
				}
			}
		}
		
		return false;
	}

	public static function Logout()
	{
		global $mysqli, $account_loggedInAccount;
		
		$unsetCookie = setcookie("LoginID", null, time() - 1, "/minecraft/");
		
		if (isset($account_loggedInAccount))
		{
			$uuid = $account_loggedInAccount->uuid;
			
			$mysqli->query("UPDATE accounts SET login_identity=NULL WHERE uuid='{$uuid}'");
		}
		
		self::$loggedInAccount = false;
	}
}

Account::UpdateLoggedInAccount();
?>