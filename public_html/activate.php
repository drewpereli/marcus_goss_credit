<?php
	require_once __DIR__ . "/../resources/config.php";
	try{
		if (!array_key_exists("c", $_GET) || !array_key_exists("i", $_GET))
			throw new Exception("Bad get array.");
		$user = ORM::forTable('users')->findOne($_GET['i']);
		if (!$user)
			throw new Exception("Bad id.");
		if ($user->activation_hash == '0')
			throw new Exception("Activation email wasn't sent properly.");
		if (!password_verify($_GET["c"], $user->activation_hash))
			throw new Exception("Bad hash.");
		//If we're here, the has is proper
		//Activate the user
		$user->activated = '1';
		$user->save();
		logIn($user);
		$flasher->success = "Your account has been activated!";
		header("Location: index.php");
		die();
	}
	catch (Exception $e){
		$flasher->danger = $e->getMessage();
		header("Location: index.php");
		die();
	}
?>