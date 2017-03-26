<?php
	require_once __DIR__ . "/../resources/config.php";
	$user = ORM::forTable('users')->findOne(1);
	var_dump($user);
?>