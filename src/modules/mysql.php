<?php
//connects to mysql server and sets the $mysqli object
$mysqli = @new mysqli('[REDACTED]');
if ($mysqli->connect_errno)
	exit("Could not connect to MySQL server: <br />
		{$mysqli->connect_errno}: {$mysqli->connect_error}");
?>
