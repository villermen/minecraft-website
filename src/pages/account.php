<?php
require_once("minecraft/modules/mysql.php");
require_once("minecraft/modules/account.php");
require_once("minecraft/modules/pex.php");

if (Account::$loggedInAccount)
{
	$notice = "";
	
	//update profile
	if (isset($_POST["updateprofile"]))
	{
		Account::UpdateProfile(Account::$loggedInAccount->uuid);
		Account::UpdateLoggedInAccount();
		
		$notice = "<div class='notice green'>Your profile has been updated.</div>";
	}
	
	//change password
	if (isset($_POST["changepassword"]))
	{
		if ($result = $mysqli->query("SELECT password FROM accounts WHERE uuid='" . Account::$loggedInAccount->uuid . "'"))
		{
			if (!$mysqli->errno && $mysqli->affected_rows)
			{
				$result = $result->fetch_assoc();		
				
				if (password_verify($_POST["currentpassword"], $result["password"]))
				{
					if ($_POST["newpassword1"] == $_POST["newpassword2"])
					{
						if (strlen($_POST["newpassword1"]) >= 5)
						{
							$newPassword = password_hash($_POST["newpassword1"], PASSWORD_BCRYPT, ["cost" => 11]);
							
							if ($mysqli->query("UPDATE accounts SET password='{$newPassword}' WHERE uuid='" . Account::$loggedInAccount->uuid . "'"))
								$notice = "<div class='notice green'>Your password has been successfully changed.</div>";
							else
								$notice = "<div class='notice red'>Could not change your password?</div>";
						}
						else
							$notice = "<div class='notice red'>Your new password must contain at least 5 characters.</div>";
					}
					else
						$notice = "<div class='notice red'>The two new passwords don't match. Durr.</div>";
				}
				else
					$notice = "<div class='notice red'>Your current password was incorrect.</div>";
			}
			else
				$notice = "<div class='notice red'>Could not obtain your account details from the database?</div>";
		}
		else
			$notice = "<div class='notice red'>Could not obtain your account details from the database?</div>";
	}
	
	//set email
	if (isset($_POST["setemail"]))
	{
		if (($mysqli->real_escape_string($_POST["email"]) == $_POST["email"] &&
			$email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) ||
			!$_POST["email"])
		{ 
			if ($mysqli->query("UPDATE accounts SET email='{$email}' WHERE uuid='" . Account::$loggedInAccount->uuid . "';"))
			{
				Account::UpdateLoggedInAccount();
				
				if ($email)
					$notice = "<div class='notice green'>Your email has been updated to '{$email}'.</div>";
				else
					$notice = "<div class='notice green'>Your email has been unset.</div>";
			}
			else
				$notice = "<div class='notice red'>Something went wrong trying to update your email address.</div>";
		}
		else
			$notice = "<div class='notice red'>The email address entered is not valid.</div>";
	}
	
	if (Account::$loggedInAccount->donator)
	{
		//set custom rank
		if (isset($_POST["setcustomrank"]))
		{
			$rank = trim($_POST["rank"]);
			$prefix = false;
			
			//resolve rank to color
			switch(Account::$loggedInAccount->rank)
			{
				case "donator":
				case "donator-":
				default:
					$rankColor = "&2";
					break;
					
				case "admin":
					$rankColor = "&4";
					break;
					
				case "moneybagd":
				case "moneybaga":
					$rankColor = "&6";
					break;
			}
			
			if ($rank)
			{
				if (strlen($rank) <= 12)
				{
					if (preg_match("~^[\w .-]*$~", $rank))
					{
						if (!strpos($rank, "  ") && !strpos($rank, "__") && !strpos($rank, "_ ") && !strpos($rank, " _"))
						{
							$blackList = "admin|op|owner|server|mod|fuck|villermen|viller|cunt|shit|ass|piss";	
							if (!preg_match("~({$blackList})~i", $rank, $match))
							{
								$prefix = "&7[{$rankColor}{$rank}&7]{$rankColor}";
							}
							else
								$notice = "<div class='notice red'>Your new rank contained a word from the blacklist: {$match[0]}. I know they are easy to bypass but would you risk a ban for that?</div>";
						}
						else
							$notice = "<div class='notice red'>Don't spam those spaces, only one space or underscore between words.</div>";
					}
					else
						$notice = "<div class='notice red'>The given rank contains illegal characters, only alphanumerical characters, spaces, underscores, dashes and dots can be used.</div>";
				}
				else
					$notice = "<div class='notice red'>Your rank can't be over 12 characters long.</div>";
			}
			else
				$prefix = $rankColor;
				
			if (!$notice)
			{
				if (Pex::SetPrefix(Account::$loggedInAccount->uuid, $prefix))
					$notice = "<div class='notice green'>Your custom rank has been set.</div>";
				else
					$notice = "<div class='notice red'>Something weird went down, please contact me.</div>";
			}
		}
	}

	$content .= "
		<h1>" . Account::$loggedInAccount->name . "'s account page</h1>
		
		{$notice}
		
		<div class='spacer'></div>
		
		<h2>Your details</h2>
		
		<div class='center'>
			<div class='horizontalsection'>
				UUID<br />
				" . Account::$loggedInAccount->formattedUUID. "
			</div>
			
			<div class='horizontalsection'>
				Rank<br />
				" . Account::$loggedInAccount->rank. "
			</div>
			
			<div class='horizontalsection'>
				Email address<br />
				" . (Account::$loggedInAccount->email ? Account::$loggedInAccount->email : "not set") . "
			</div>
		</div>
		
		<div class='spacer'></div>
		
		
		<h2>Update profile</h2>
		<div class='center'>
			Updating your profile updates your name from the Mojang API and your rank from the server. This happens automatically every 2 hours but you can press this button if you're impatient.
		</div>
		<br />
		<form method='post' action=''>
			<input type='submit' name='updateprofile' value='Update profile' />
		</form>
			
		<div class='spacer'></div>
		
		<h2>Change password</h2>
		<form method='post' action=''>
			<div class='horizontalsection'>
				Current password<br />
				<input type='password' name='currentpassword' />
			</div>
			
			<div class='horizontalsection'>
				New password<br />
				<input type='password' name='newpassword1' />
			</div>
			
			<div class='horizontalsection'>
				New password (confirm)<br />
				<input type='password' name='newpassword2' />
			</div>
			
			<div class='horizontalsection'>
				<br />
				<input type='submit' name='changepassword' value='Change password' />
			</div>
		</form>
		
		<div class='spacer'></div>
		
		<h2>Email address</h2>
		<form method='post' action=''>
			Here you can specify an email address that will be used to notify you of upcoming events.
			If given, I will notify you of the event at least a week and a few hours before it is held.
			I might also contact you by email if you've specified one and I can reach you in no other way.<br />
			<br />
			<input type='email' name='email' maxlength='255' />
			<input type='submit' name='setemail' value='Set email' />
		</form>
		
		<div class='spacer'></div>
	";
	
	if (Account::$loggedInAccount->donator)
	{
		$content .= "
			<h2>Set custom rank</h2>
			<div class='center'>
				Set your custom donator rank using this form. Leave the form blank to remove your custom rank (even the brackets containing it). If you've been promoted or demoted you can also use this as a hacky way to fix your name's color =)
			</div>
			<br />
			<form method='post' action=''>
				<input type='text' name='rank' maxlength='12' />
				<input type='submit' name='setcustomrank' value='Set custom rank' />
			</form>
			
			<div class='spacer'></div>
		";
	}
}
else
{
	$conten .= "
		<div class='notice red'>
			You are not logged in. Please <a href='login'>log in</a> before using this page.
		</div>
	";
}
?>