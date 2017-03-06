<?php
	require_once __DIR__ . "/../resources/config.php";
	requireLogIn();
	logOut();
	$flasher->success = "You're logged out.";
	header("Location: index.php");
	die();
?>