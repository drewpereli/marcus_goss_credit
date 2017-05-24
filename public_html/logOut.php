<?php
	require_once __DIR__ . "/../resources/config.php";
	requireLogIn();
	logOut();
	if (isset($_GET["reason"]) && $_GET["reason"] === "sessionExpired"){
		$flasher->success = "Logged out due to inactivity.";
	}
	header("Location: /");
	die();
?>