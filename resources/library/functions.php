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

	function getCurrentUserAsArray(){
		$u = getCurrentUser();
		return $u ? $u->asArray('id', 'first_name', 'last_name', 'email', 'role') : false;
	}

	function requireLogIn(){
		if (!isLoggedIn()){
			$flasher->danger = "You must be logged in to view that page.";
			header("Location:logIn");
			die();
		}
	}






	function sendActivationEmail($user){
		if ($user->activated)
			return false;
		//Generate activation url
		$code = hash('sha256', rand());
		$activation_url = HOST_URL . "activate.php?i={$user->id}&c={$code}";
		$user->activation_hash = password_hash($code, PASSWORD_DEFAULT);
		$user->save();
		//Send activation email.
		$mailer = $GLOBALS['mailer'];
		$mailer->addAddress($user->email, "{$user->first_name} {$user->last_name}");
		$mailer->Subject = 'Account Activation';
		$mailer->isHTML(true);
		$twig = $GLOBALS['twig'];
		$body = $twig->render("activation.twig", array("activation_url"=>$activation_url));
		$mailer->Body = $body;
		$mailer->altBody = `
			Thanks for signing up with Basics Credit!
			Begin improving your credit today. Click the link below to activate your account.
		` . $activation_url;
		if ($mailer->send())
			return true;
		else{
			return false;
		}
	}




	//Returns a boolean. True if the password is secure enough, false if not
	function passwordSecure($pwd){
		//Check password regex
		$uppercase = preg_match('@[A-Z]@', $pwd);
		$lowercase = preg_match('@[a-z]@', $pwd);
		$number    = preg_match('@[0-9]@', $pwd);
		if (!$uppercase || !$lowercase || !$number || strlen($pwd) < 8){
			return false;
		}
		return true;
	}





?>