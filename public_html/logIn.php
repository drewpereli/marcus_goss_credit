<?php
	require_once __DIR__ . "/../resources/config.php";
	//If trying to log in
	if (sizeof($_POST) > 0){
		try{
			//Try to find user based on email
			$user = ORM::forTable('users')->where('email', strtolower($_POST["email"]))->findOne();
			if (!$user)
				throw new Exception("Invalid email address.");
			//If user isn't activated
			if ($user->activated !== '1')
				throw new Exception('You need to activate your account. Please check your email for an activation link. <a href="#">Click here</a> to resend the link.');
			//Check password
			if (!password_verify($_POST["password"], $user->password_hash))
				throw new Exception("Invalid password.");
			//If we're here, we're good. Log the user in.
			unset($_POST["password"]);
			logIn($user);
			$flasher->success = "You are now logged in.";
			header("Location: /");
			die();
		}
		catch(Exception $e){
			unset($_POST["password"]);
			$flasher->danger = $e->getMessage();
		}
	}
	echo $twig->render("logIn.twig", array(
		"flash_messages" => $flasher->getAll(),
		"post_data" => $_POST
		));
?>