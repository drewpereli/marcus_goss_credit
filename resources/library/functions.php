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
		return $u ? $u->asArray('id', 'first_name', 'last_name', 'email', 'role', 'has_made_first_payment', 'has_access_until') : false;
	}

	function requireLogIn(){
		if (!isLoggedIn()){
			$GLOBALS['flasher']->danger = "You must be logged in to view that page.";
			header("Location:logIn");
			die();
		}
	}

	//Put at the top of any page that requires that the user be paid up to access it (unless they're an admin).
	function requirePaymentToAccess(){
		requireLogIn();
		$u = getCurrentUser();
		if (userIsAdmin($u)){
			return;
		}
		//If the current datetime is past when the current user has access to
		if (!currentUserHasPaidAccess($u)){
			$GLOBALS['flasher']->danger = "You must make a payment to access that page.";
			header("Location:/");
			die();
		}
	}



	function userIsAdmin($u){
		return $u->role == 1;
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


	//Returns true if $date1 is before $date2. Returns false if they are the same or $date1 is after $date2
	function dateIsBefore($date1, $date2){
		return $date2->diff($date1)->invert === 1;
	}


	//Returns true if the current user has access to paid services. Else returns false
	function currentUserHasPaidAccess($u){
		return dateIsBefore(new DateTime(), new DateTime($u->has_access_until));
	}



?>



