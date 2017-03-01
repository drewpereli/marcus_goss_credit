<?php
	require_once __DIR__ . "/../resources/config.php";
	ORM::configure('mysql:host=localhost;dbname=credit');
	ORM::configure('username', DB_USER);
	ORM::configure('password', DB_PASSWORD);

	/*
	$user = ORM::forTable("users")->create();
	$user->first_name = "Drew";
	$user->last_name = "Pereli";
	$user->email = "A";
	$user->password_hash = "B";
	$user->save();
	*/
	$user = ORM::forTable("users")->findMany();
	var_dump($user);
?>