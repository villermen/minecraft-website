<?php
require_once("minecraft/modules/account.php");

if (Account::$loggedInAccount && Account::$loggedInAccount->admin)
{
	$content .= "
		<h1>Admin Help</h1>
		
		<h2>Guidelines</h2>
		<ul>
			<li>First of all, be polite, nobody respects a swearing/offending admin.</li>
			<li>Do not explicitly provoke people, even if they deserve it you should know better than that.</li>
			<li>Act formal, if you know people better you can of course talk as you please, but handle new members with respect.</li>
			<li>You CAN instaban people when they only join to grief or just insult, but always warn regular people first, with a kick if disobeying.</li>
			<li>Adminship is no free ticket, you must obey the rules just as much as the others.</li>
			<li>Think before you talk, most arguments get sparked by misunderstanding on one of the sides.</li>
			<li>Do NOT kick for fun, it's nearly always seen as offending by the one kicked.</li>
		</ul>
		
		<div class='spacer'></div>
		
		<h2>Commands</h2>
		<ul>
			<li><code>/kick [name]</code></li>
			<li><code>/ban [name] [reason]</code> - Ban a player, reason is not required but might help me later on.</li>
			<li><code>/tempban 1d12h [name] [reason]</code> - Ban a player for a set amount of time (1y1w1d1h1m: 1 year 1 week 1 day 1 hour and 1 minute).</li> 
			<li><code>/invsee [name]</code> - See a player's inventory.</li>
			<li><code>/lookup [name]</code> - Look up a player's MCBans status (amount of bans, reputation) </li>
			<li><code>/unban [name]</code></li>
			<li><code>/tpo [name]</code> - Force tp to someone (even if they have tptoggled)</li>
			<li><code>/vanish</code> - Become a ghost</li>
		</ul>
		
		<div class='spacer'></div>
		
		<h2>CoreProtect</h2>
		<ul>
			<li><code>/co help</code> - A list of available commands.</li>
			<li><code>/co i</code> - Inspector toggle: left-click/right-click/place-block to see blocks placed or removed.</li>		
		</ul>
		Rollback examples:<br />
		<ul>
			<li><code>/co rollback Crabchicken t:500h r:#global</code> <span style='color:gray'>- Rollback all edits made by Crabchicken in the last 500 hours.</span></li>
			<li><code>/co rollback Crabchicken t:500h r:#vanilla</code> <span style='color:gray'>- In vanilla</span></li>
			<li><code>/co rollback Crabchicken t:500h r:50</code> <span style='color:gray'>- Within 50 blocks around you</span></li>
		</ul>

		<div class='spacer'></div>
		
		<h2>Regions (protected areas)</h2>
		<br />
		<iframe width='1000' height='593' src='//www.youtube.com/embed/j8VaLSeY5zE'></iframe><br />
		<br />
		Select 2 points using your wooden axe. Usually you want to make the area a bit bigger for expansions from the user.<br />
		Expand the region from top to bottom using <code>//expand vert</code>.<br />
		Execute the command <code>/region define [nameofregion]</code> where [nameofregion] is the name the region should have (crabsandbox for example). Remember this name, you will need it in future commands.<br />
		Add the players that can build in the region using <code>/region addmember [nameofregion] [name1] [name2] ...</code><br />
		To make players able to use doors, buttons etc. use <code>/region flag [nameofregion] use allow</code>.

		If you want to expand a region, select it by using <code>/region select [nameofregion]</code>.<br />
		Then, expand it in the wanted direction using <code>//expand # [n/e/s/w/u/d]</code>, where # is how much blocks it should expand in that direction.<br />
		Now execute the command: <code>/region redefine [nameofregion]</code> and the region will be replaced to the selection.<br />
		You can also select a new area with your wooden axe instead of using /region select.

		Removing regions can be done using <code>/region remove [nameofregion]</code>.";
}
else
	$content.="<div class='notice red'>You aren't logged in.<br />Log in before using this page (if you are an admin)</div>";
?>