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






	function sendActivationEmail($user){
		if ($user->activated)
			return false;
		//Generate activation url
		$activation_url = HOST_URL . "activate.php?i={$user->id}&c={$user->activation_hash}";
		//Send activation email.
		$mailer = $GLOBALS['mailer'];
		$mailer->addAddress($user->email, "{$user->first_name} {$user->last_name}");
		$mailer->Subject = 'Account Activation';
		$mailer->isHTML(true);
		$twig = $GLOBALS['twig'];
		$body = $twig->render("activation.twig", array("activation_url"=>$activation_url));
		$mailer->Body = $body;
		$mailer->altBody = `
			Thanks for signing up with White Label Credit Repairs!
			Begin improving your credit today. Click the link below to activate your account.
		` . $activation_url;
		if ($mailer->send())
			return true;
		else{
			return false;
		}
	}
?>