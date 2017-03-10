<?php
	require_once __DIR__ . "/../resources/config.php";
	//If trying to signup
	if (sizeof($_POST) > 0){
		//Validate info
		try{
			//Validate required fields
			$required_fields = array("first_name", "last_name", "email", "password", "password_confirmation");
			foreach ($required_fields as $req_field){
				if (!isset($_POST[$req_field]))
					throw new Exception("You're missing a required field.");
			}
			//Check email validity
			$email = strtolower($_POST["email"]);
			if (!filter_var($email, FILTER_VALIDATE_EMAIL))
				throw new Exception("That is not a valid email address.");
			//Check email uniqueness
			if (ORM::forTable("users")->where("email", $email)->findOne())
				throw new Exception("There is already an account with that email address.");
			//Check password regex
			$uppercase = preg_match('@[A-Z]@', $_POST["password"]);
			$lowercase = preg_match('@[a-z]@', $_POST["password"]);
			$number    = preg_match('@[0-9]@', $_POST["password"]);
			if (!$uppercase || !$lowercase || !$number || strlen($_POST["password"]) < 8)
				throw new Exception("Your password must be at least 8 characters long, contain at least 1 lowercase letter, at least 1 uppercase letter, and at least 1 number.");
			//Check password match
			$pwd = password_hash($_POST["password"], PASSWORD_DEFAULT);
			unset($_POST["password"]);
			if (!password_verify($_POST["password_confirmation"], $pwd)){
				unset($_POST["password_confirmation"]);
				throw new Exception("The passwords don't match.");
			}
			unset($_POST["password_confirmation"]);
			//If we're here, the posted info is valid. Create the user and redirect to their profile.
			$user = ORM::forTable("users")->create();
			$user->first_name = $_POST["first_name"];
			$user->last_name = $_POST["last_name"];
			$user->email = $email;
			$user->password_hash = $pwd;
			$user->activation_hash = hash('sha256', rand());
			$user->save();
			if (!sendActivationEmail($user)){
				$user->delete();
				throw new Exception("There was an error. Please try again later.");
			}
			$flasher->success = "Thanks for signing up! Check your email for an activation link.";
			header("Location:index.php");
			die();
		}
		catch(Exception $e){
			$flasher->danger = $e->getMessage();
		}
	}
	echo $twig->render("signUp.twig", array(
		"flash_messages" => $flasher->getAll(),
		"current_user" => getCurrentUserAsArray(),
		"post_data" => $_POST
		));
?>