<?php
require_once("minecraft/modules/account.php");

$content.="<h1>Log in</h1>
<div class='center'>
	Log in with your current minecraft username or UUID and your password for this website.
</div>";

if (isset($_POST["login"]))
{
	if (Account::Login($_POST['name'], $_POST["password"], $_POST["persistent"]))
		header("Location: account");
	else
	{
		$content .= "
			<div class='notice red'>
				You could not be logged in. Please check if you entered your current Minecraft username and your site password correctly.
			</div>";
	}
}

$content.="
<br />
<form method='post'>
	<input type='text' name='name' maxlength='36' placeholder='name' />
	<input type='password' name='password' placeholder='password' /><br />
	<br />
	<input type='checkbox' name='persistent' value='1' id='persistent' /><label for='persistent'>Stay logged in.</label><br />
	<br />
	<input type='submit' name='login' value='Log in' />
</form>";
?>