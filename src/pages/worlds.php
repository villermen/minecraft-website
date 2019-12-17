<?php
$content.="
	<div class='navbarcontainer'>
    <div class='navbarspacer'></div>
        <a class='navbarlink' href='#creative'><span class='mc4'>Creative</span></a>
    <div class='navbarspacer'></div>
        <a class='navbarlink' href='#vanilla'><span class='mc9'>Vanilla</span></a>
    <div class='navbarspacer'></div>
        <a class='navbarlink' href='#flatland'><span class='mc2'>Flatland</span></a>
    <div class='navbarspacer'></div>
        <a class='navbarlink' href='#pixelart'><span class='mc5'>Pixelart</span></a>
    <div class='navbarspacer'></div>
       <a class='navbarlink' href='#skyblock'><span class='mc3'>Skyblock</span></a>
    <div class='navbarspacer'></div>
	</div>
<div class='hline'></div>


<a id='creative'></a><h1 class='mc4'>Creative</h1>
Creative is pretty much a heavily customized vanilla world.<br />
A lot of otherwise considered cheat-commands can be used here, and creative mode is active (but not enfoced).<br />
You can request to have your building or area protected by an admin.<br />
Also: mobs, weather, and almost all automatic events (like spreading of mushrooms) are disabled in this world.<br />
Members have build all sort of epic buildings that can be reached with warps.

<h2>Commands</h2>
<br />
Most of the miscellaneous commands (like /spawn) will work in any world.<br />
<br />

<b>Guests:</b>
<ul>
	<li><code>/spawn</code> - Get back to the spawn.</li>
	<li><code>/slay</code> - Kill yourself =S</li>
	<li><code>/who</code> - See who is currently online (/list,/playerlist,/online).</li>
	<li><code>/mvw</code> - See all the players in the different worlds. (/mvwho)</li>
	<li><code>/mvlist</code> - List the worlds running on this server.</li>
	<li><code>/warp [warpname]</code> - Teleport to the given warp.</li>
	<li><code>/call [playername]</code> - Request a teleport from a player (/tpa).</li>
	<li><code>/motd</code> - See the message of the day.</li>
	<li><code>/time</code> - Check the current world's time.</li>
	<li><code>/me [message]</code> - Show others what you are doing.</li>
	<li><code>/msg [playername] [message]</code> - Message a player in private (/whisper,/tell).</li>
	<li><code>/r [message]</code> - Reply to the last received pm (/reply).</li>
	<li><code>/whereami [playername]</code> - Get your location.</li>
	<li><code>/clear [**]</code> - Remove your inventory/gear.</li>
	<li><code>/ping</code> - Get a response from the server to see if you experience any lag.</li>
	<li><code>/home & /sethome</code> - Teleport to and set your home.</li>
	<li><code>/gamemode [0/1/2]</code> - Set your gamemode to survival/creative/adventure (/gm).</li>
	<li><code>/heal</code> - Heal yourself (replenish health+hunger).</li>
    <li><code>/afk</code> - Set your status to away from keyboard (/away).</li>
    <li><code>/mail [options]</code> - Mail actions (read/send/clear).</li>
    <li><code>/recipe [item]</code> - See how an item is made.</li>
    <li><code>/tpa [player]</code> - Request a teleport to a player.</li>
    <li><code>/tptoggle</code> - Disable teleporting to you.</li>
    <li><code>/ignore [player]</code> - Ignore someone (stop receiving messages and chat from them).</li>
    <li><code>/i [itemname/id] [amount]</code> - Give yourself items (/item).</li>
    <li><code>/warp [warpname]</code> - Warp somewhere</li>
    <li><code>/warp list [-c creator -w world]</code> - List all or specific warps.</li>
    <li><code>/playertime [@][day/night/dawn/...]</code> - Set your own servertime to a custom value, @ fixes it.</li>
</ul>
	
<b>Members:</b>
<ul>
	<li><code>/warp set [warpname]</code> - Create a new warp with the given name at your location.</li>
    <li>Build rights.</li>
</ul>

<b>Member+:</b><br />
<br />
Initially, building with fluids (lava &amp; water) and using bonemeal is prohibited in creative.<br />
A week after signing up however you can ask any admin to promote you to Member+.<br />
A user of rank Member+ has the sole advantage of being able to build with fluids and grow stuff with bonemeal in creative.<br />
I have taken this measure to prevent players from griefing with them, which is almost irreversible.<br />
<br />
<b>Donators:</b><br />
<br />
Donator features and commands can be found at the <a href='?p=donating'>donating page</a>.

<div class='spacer'></div>
<a id='vanilla'></a><h1 class='mc9'>Vanilla</h1>

Vanilla is the survival world on the server.<br />
This world has almost the same settings as good old single player.<br />
Mobs are on normal mode.<br />
Homes (/home and /sethome) can be used, and you will spawn at your home when you die.<br />

<h2>Lockette</h2>
Lockette is a way to lock your chests from others.<br />
To use it just put a sign on a chest and it will automatically protect that chest for you.<br />
If you want to add other players to it you can do it manually by placing a sign in front of it and typing:<br />
[private] on the first line.<br />
Your own name on the second.<br />
More names on the third and fourth.<br />
If you put an extra sign on a chest or door, you will be able to add even more players.<br />
You can use <code>/lockette [line] [text]</code> to edit a sign without replacing it.

<div class='spacer'></div>
<a id='flatland'></a><h1 class='mc2'>Flatland</h1>
Flatland is a completely flat world where only donators can build.<br />
It has the same features as creative, so head <a href='?p=creative'>there</a> for the commands.<br />
There's some other features donators can use here, they can all be found at the <a href='?p=donating'>donating page</a>.

<div class='spacer'></div>
<a id='pixelart'></a><h1 class='mc5'>Pixelart</h1>
Pixelart is exactly the same as creative, but it's completely flat and meant only for pixelart.<br />
Simply find a free spot and be as creative as you can be.<br />

<div class='spacer'></div>
<a id='skyblock'></a><h1 class='mc3'>Skyblock</h1>
Skyblock is a world that has multiple skyblock instances and a nice plugin to go with it.<br />
To get started use <code>/island help</code> from any world for a list of the commands, or use <code>/island</code> to get yourself going straight away!";
?>