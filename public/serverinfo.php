<?php
require_once("minecraft/modules/serverinfo.php");

header("Content-type: application/json");
echo json_encode(Serverinfo::GetOnlinePlayers());
?>