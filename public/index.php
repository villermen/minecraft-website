<?php

use Symfony\Component\HttpFoundation\Request;
use Villermen\Minecraft\App;

require_once(__DIR__ . '/../vendor/autoload.php');

$request = Request::createFromGlobals();
$app = new App();
$response = $app->run($request);
$response->prepare($request);
$response->send();

exit();


require_once("minecraft/modules/mysql.php");
require_once("minecraft/modules/misc.php");
require_once("minecraft/modules/account.php");

//pageloader
if (isset($_GET["p"]))
	$page = $_GET["p"];
else
	$page = "home";

//don't allow characters that could potentially harm the mechanism
if (!preg_match("~^[A-z-_0-9]+$~", $page))
	$page = "home";

//execute page if it exists
$content = "";
if (!@include("minecraft/pages/{$page}.php"))
{
	header("HTTP/1.0 404 Not Found");
	$page = "404";
	$content .= "
		<h1>Page not found</h1>

		<div class='center'>
			The requested page could not be found.<br />
			Please make sure the url is typed correctly.<br />
			If another page on this site led you here, please inform me about it.
		</div>";
}

//banner background
$bannerBackground = false;
//$bannerBackground = "img/banners/onmate_vacguy99_creativechristmas.png"; //override

if (!$bannerBackground)
{
	if ($bannerBackgrounds = glob("img/banners/*.png"))
	{
		$bannerBackground = $bannerBackgrounds[rand(0, count($bannerBackgrounds) - 1)];
		$bannerBackground = "img/banners/" . basename($bannerBackground);
	}
}
?>
<!doctype html>
<html>
	<head>
		<meta charset='UTF-8' />
		<title><?php echo ucfirst($page); ?> - villermen.com, a minecraft creative/survival server</title>
		<link rel="icon" type="image/x-icon" href="favicon.ico" />
		<link type="text/css" rel="stylesheet" href="css/default.css" />

		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" type='text/javascript'></script>

		<script type="text/javascript">
			//add playerheads to banner
			$.getJSON("serverinfo.php", function(data) {
				var bannerPlayers = $(".bannerplayers").first();

				for (var i = 0; i < data.length; i++)
				{
					var uuid = data[i].uuid;
					var name = data[i].name;
					bannerPlayers.append("<img class='bannerplayerhead' src='playerhead.php?p=" + uuid + "&s=18' title='" + name + "' />");
				}
			});
		</script>

		<script type="text/javascript">
			function toggle(id)
				{
				var object=document.getElementById(id);
				if (object.style.display=='none')
					{
					object.style.display='inline';
					}
				else
					object.style.display='none';
				}
		</script>
	</head>

	<body>
		<div class="body">
			<div class='bannercontainer'>
				<img class='banner' src='css/banner_title.png' alt='' style='background-image:url("<?php echo $bannerBackground ?>")' />
				<div class='bannertext bannersubtitle'>A minecraft creative/survival server</div>
				<div class='bannertext bannerversion'>Minecraft 1.11.2</div>
				<a href='online' class='bannertext bannerplayers'></a>
			</div>

			<?php
			//banner
			function CreateNavbarLink($pageName, $text, $newTab = false)
			{
				global $page;

				$classString = "navbarlink";
				if ($pageName == $page)
					$classString .= " active";

				$newTabString = $newTab ? " target='_blank'" : "";

				echo "
					<a class='$classString' href='$pageName'$newTabString>$text</a>
					<div class='navbarspacer'></div>";
			}

			echo "
				<div class='hline'></div>
				<div class='navbarcontainer'>
					<div class='navbarspacer'></div>";

			CreateNavbarLink("home", "Home");
			// CreateNavbarLink("forum", "Forum");
			CreateNavbarLink("worlds", "Worlds");
			CreateNavbarLink("ranks", "Ranks");
			CreateNavbarLink("rules", "Rules");
			CreateNavbarLink("plugins", "Plugins");
			CreateNavbarLink("donating", "Donating");
			CreateNavbarLink("voting", "Voting");
			CreateNavbarLink("map/", "Map", true);

			//login/logout
			if (!Account::$loggedInAccount)
				CreateNavbarLink("login", "Log in");
			else
				CreateNavbarLink("logout", "Log out");

			CreateNavbarLink("signup", "Sign up");

			echo "
				</div>
				<div class='hline'></div>";

			//donator bar
			if (Account::$loggedInAccount)
			{
				echo "
					<div class='navbarcontainer'>
						<div class='navbarspacer'></div>
				";

				CreateNavbarLink("account", Account::$loggedInAccount->name);

				if (Account::$loggedInAccount->admin)
				{
					CreateNavbarLink("adminhelp", "Admin help");
					CreateNavbarLink("adminlookup", "Lookup");
					CreateNavbarLink("adminbans", "Bans");
				}

				if (Account::$loggedInAccount->villermen)
					CreateNavbarLink("villermen", "The chosen");

				echo "
					</div>
					<div class='hline'></div>";
			}
			?>

			<!-- Content -->
			<div class="content">
				<?php echo $content; ?>
			</div>

			<div class='hline'></div>

			<!-- Footer -->
			<?php
			$time = date("G:i");
			$year = date("Y");
			$zone = date("T");

			echo "
				<div class='footer'>
					©2011-{$year} Villermen -
					<span title='Current server time ($zone)'>$time</span> -
					<a href='contact'>contact</a> -
					<a href='webdevelopment'>
						<img class='webdevelopmenticon' alt='Web development' src='css/chromefirefox.png' /></a>
				</div>";
			?>
		</div>
	</body>
</html>
