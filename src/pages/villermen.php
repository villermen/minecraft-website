<?php
error_reporting(E_ALL);
require_once("minecraft/modules/account.php");

if (Account::$loggedInAccount && Account::$loggedInAccount->villermen)
{
	
	/*
	$content .= "<pre>" . print_r(Account::$loggedInAccount, true) . "</pre>";
	
	
	$players = Serverinfo::GetOnlinePlayers();
	$content .= "<pre>" . print_r($players, true) . "</pre>";
	*/

	if (isset($_POST["changePassword"]))
	{
		$username = $_POST["username"];

		if ($username && ($accountDetails = Account::GetDetails($username)))
		{
			require_once("minecraft/modules/misc.php");

			$newPassword = Misc::GenerateRandomString(6);
			$newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT, ["cost" => 11]);

			$mysqli->query("UPDATE accounts SET password = '{$newPasswordHash}' WHERE uuid = '{$accountDetails->uuid}'");

			$content .= "<div class='notice green'>New password for {$accountDetails->name} is \"{$newPassword}\".</div>";
		}
		else
		{
			$content .= "<div class='notice red'>Non-existent account given.</div>";
		}
	}

	$content .= "<form method='post'><input type='text' name='username' placeholder='Username/UUID' maxlength='16' /><input type='submit' name='changePassword' value='Reset Password' /></form>";
	
}
else
	$content .= "
		<div class='notice red'>
			<img src='https://exposexpress.files.wordpress.com/2012/05/gandalf.jpg' />
		</div>";
?>