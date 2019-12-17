<?php
require_once("minecraft/modules/forum.php");

/*$content .= "
	<div class='notice green'>
		UHC (ultra hardcore) pvp event 2 next saturday 20:00 GMT+1.<br />
		Event starts in " . Misc::TimeTillDate("4-4-2015 20:00") . " at <a>villermen.com:1337</a>.<br />
		See <a href='forum&s=thread&id=1'>the post on the news thread</a> for more information.
	</div>";*/

$content .= "
    <p>
        <img src='img/server-down.png' alt='villermen.com has shut down' style='max-width:1000px' />
    </p>
    <p>
        villermen.com was shut down on 18-6-2017 after six years of service.
        This site now serves as a display archive with all member and server functionality stripped out.
        Be sure to check out the <a href='map'>interactive map</a> of the worlds as they were when the server was shut down.
        
        The server's worlds will be available for download soon™.
    </p>
    
	<h1>Home</h1>

	Welcome to villermen.com, a minecraft creative/survival server.
	This site serves as a helpdesk, with all info, news and discussion you could need for the server.
	If you haven't joined in-game yet there won't be much to do here.
	Connect to the server by just typing villermen.com in the server address bar, plain and simple =)<br />
	<br />
	By default you won't be able to build, to obtain build rights please <a href='signup'>sign up</a> on this site and you will be promoted automatically.<br />
	<br />
	<iframe width='1000' height='563' src='//www.youtube.com/embed/wXtZ7d9ogdI'></iframe>

	<h2>Latest news</h2>
	
	<a id='news'></a>
	
	<div class='spacer'></div>";
	
$posts = Forum::GetPosts(1, 0, 10, time() - 2592000);
$posts = array_reverse($posts);

if (!$posts)
	$content .= "There have been no news posts last month.";
else
{
	foreach($posts as $post)
	{
		$content .= Forum::FormatPost($post);
	}
}
	
$content .= "	
	<h2>Vote for villermen.com</h2>

	If you like the server please show your support by <a href='voting'>voting</a> on one of the server-lists this server is registered on.
	It will help out a great deal, and will bring you more players to play with.
	It's a win-win situation really!";
?>