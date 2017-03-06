<?php
	function logIn($user){
		$_SESSION["user_id"] = $user->id;
	}

	function logOut(){
		if (isLoggedIn())
			unset($_SESSION["user_id"]);
	}

	function isLoggedIn(){
		return array_key_exists("user_id", $_SESSION);
	}

	function getCurrentUser(){
		return isLoggedIn() ? ORM::forTable('users')->findOne($_SESSION["user_id"]) : false;
	}

	function requireLogIn(){
		if (!isLoggedIn()){
			header("Location:index.php");
			die();
		}
	}
?>