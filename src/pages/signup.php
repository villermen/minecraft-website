<?php
$content .= "<h1>Sign up</h1>";

if (false)
	$content .= "<div class='notice red'>Signing up is disabled at the moment, please check back later!</div>";
else
{
	require_once("minecraft/modules/pex.php");
	require_once("minecraft/modules/account.php");
	require_once("minecraft/modules/rcon.php");
	require_once("minecraft/modules/misc.php");
	require_once("minecraft/modules/mysql.php");
	
	$errorMessage = "";
	$subPage = 1;
	
	session_name("minecraft-signup");
	session_start();

	if (isset($_GET["clearsession"]))
		session_unset();
	
	if (isset($_POST["details"]))
	{
		if (strlen($_POST["password1"]) < 5)
			$errorMessage = "Please use a password with 5 or more characters.";
		elseif ($_POST["password1"] != $_POST["password2"])
			$errorMessage = "The two password don't match.";
		elseif (!preg_match("~^[A-z0-9_]+$~", $_POST["name"]))
			$errorMessage = "That is not a valid Minecraft name.";
		elseif ($_POST["email"] && ($mysqli->real_escape_string($_POST["email"]) != $_POST["email"] ||
			!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)))
			$errorMessage = "The given email is not valid.";
		else
		{	
			if (!($uuid = Account::ResolveUUID($_POST["name"])) ||
				!($name = Account::ResolveName($uuid)))
				$errorMessage = "Could not find the UUID for your account name.";
			else
			{
				//check if account already exists
				$mysqli->query("SELECT uuid FROM accounts WHERE uuid='{$uuid}'");
				if ($mysqli->errno || $mysqli->affected_rows)
					$errorMessage = "There is already an account registered with that UUID.";
				else
				{
					//generate and send code
					$code = Misc::GenerateRandomString(4, "ABCDEFGHKMNPQRSTUVWXYZ23456789");
					if (!Rcon::RunCommand("msg {$name} &b{$code} &7Please use this code on the site to verify your account."))
						$errorMessage = "Could not send you a code, maybe the server is down? If not please contact me.";
					else
					{
						$_SESSION["uuid"] = $uuid;
						$_SESSION["password"] = password_hash($_POST["password1"], PASSWORD_BCRYPT, ["cost" => 11]);
						$_SESSION["code"] = $code;
						$_SESSION["email"] = $_POST["email"];
						$_SESSION["name"] = $name;
					}
				}
			}
		}
	}

	if (isset($_SESSION["code"]))
	{
		$subPage = 2;
		
		if (isset($_POST["confirmcode"]))
		{
			$confirmCode = strtoupper($_POST["code"]);
			if ($confirmCode == $_SESSION["code"])
			{
				//create account
				$time = time();
				$mysqli->query("INSERT INTO accounts (uuid, password, signup_time, email) VALUES ('{$_SESSION["uuid"]}', '{$_SESSION["password"]}', '{$time}', '{$_SESSION["email"]}');");
				
				if ($mysqli->affected_rows > 0)
				{
					//promote to member if account is currently guest
					if (Pex::GetRank($_SESSION["uuid"]) == "guest")
						Pex::SetRank($_SESSION["uuid"], "member");
					
					Rcon::RunCommand("broadcast &9{$_SESSION["name"]}&f just signed up on the site. Welcome &9{$_SESSION["name"]}&f!");
					
					//make sure we have accurate data on the account after this point
					Account::UpdateProfile($_SESSION["uuid"]);
					
					$subPage = 3;
					session_unset();
				}
				else
					$errorMessage = "Could not create your account in the database, please contact me if this error persists.";
			}
			else
				$errorMessage = "The two codes did not match. Please try again.";
		}
		
	}

	$subPageContents = [
		1 => "
		<div class='center'>
			You will need to sign up in order to get promoted to the member rank and be able to build on the server.<br />
			<br />
			Please fill in your minecraft username and a preferred password for this site (please don't use your minecraft password) in the form below.
			You will need to be online on the server in order to receive the code to confirm that you own the account.
			Click \"Send code\" to receive the confirmation code in-game.<br />
			<br />
			<form method='post' action='signup'>
				<input type='text' name='name' maxlength='16' autofocus='autofocus' placeholder='Minecraft username' /><br />
				<input type='password' name='password1' placeholder='Password' /><br />
				<input type='password' name='password2' placeholder='Password (confirm)' /><br />
				<br />
				If you specify an email address here you will be notified of upcoming events so you won't miss them.
				This is optional and you will be able to change this at any time from your account page.<br />
				<br />
				<input type='email' name='email' placeholder='Email address' /><br />
				<br />
				<input type='submit' name='details' value='Send code' />
			</form>
			
			<h2>Member+</h2>
			A week after joining you will be automatically promoted to member+. Easy right?
		</div>",
	
		2 => "
		<div class='center'>
			A code has been sent to you in-game.<br />
			Please fill in the code you received in the form below.<br />
			<br />
			<form method='post' action='signup'>
				<input type='text' name='code' maxlength='4' autofocus='autofocus' />
				<input type='submit' name='confirmcode' value='Submit code' />
			</form>
			<br />
			Didn't get a code? <a href='signup&clearsession'>click here to try again</a>.<br />
			<br />
		</div>",
	
		3 => "
		<div class='center'>
			The code was accepted and you have been promoted to member if you weren't already.
			You can now log in to the site whenever you want with your current minecraft name or UUID and your given password.
			Enjoy your stay at the server!
		</div>"
	];

	if ($errorMessage)
	{
		$content .= "
			<div class='notice red'>
				{$errorMessage}
			</div><br />";
	}
	
	$content .= $subPageContents[$subPage];
}
?>