<?php
require_once("minecraft/modules/account.php");

Account::Logout();
header("Location: home");
?>