<?php
	require_once __DIR__ . "/../resources/config.php";
	ORM::configure('mysql:host=localhost;dbname=credit');
	ORM::configure('username', DB_USER);
	ORM::configure('password', DB_PASSWORD);
?>