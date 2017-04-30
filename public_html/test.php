<?php
	require_once __DIR__ . "/../resources/config.php";
	$user = getCurrentUser();
	$hasAccessUntil = new DateTime($user->has_access_until);
	$now = new DateTime();
	echo $now->add($accessInterval)->format("Y-m-d h:i:s");
	echo "<br/>";
	//echo $now->format('Y-m-d H:i:s');
?>