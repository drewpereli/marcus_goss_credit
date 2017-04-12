<?php
	require_once __DIR__ . "/../resources/config.php";
	requireLogIn();
	logOut();
	header("Location: /");
	die();
?>