<?php
	require_once __DIR__ . "/../resources/config.php";
	if (isset($_GET["i"]) && isset($_GET["c"]) && !isset($_POST["id"])){
		//If we need to respond with a password reset form
		try{
			//Check if the code is correct
			$user = ORM::forTable('users')->findOne($_GET["i"]);
			if (!$user){
				error_log("User tried to reset password with bad id. Id: {$_GET['id']}");
				throw new Exception(GENERIC_ERROR_MESSAGE);
			}
			if ($user->password_reset_hash === '0' || is_null($user->password_reset_hash)){
				throw new Exception(GENERIC_ERROR_MESSAGE);
			}
			if (!password_verify($_GET["c"], $user->password_reset_hash)){
				//$flasher->info = $_GET["c"] . "<br/>" . $user->password_reset_hash;
				error_log("User {$user->id} tried to reset password with bad code. Code: {$_GET['c']}. User's reset hash is {}$user->password_reset_hash}");
				throw new Exception(GENERIC_ERROR_MESSAGE);
			}
			//If we're here, render the form
			echo $twig->render("set_new_password.twig", array("code"=>$_GET["c"], 
																"id"=>$_GET["i"],
																"flash_messages" => $flasher->getAll()
																)
								);
			die();
		}
		catch (Exception $e){
			$flasher->danger = $e->getMessage() . $e->getLine();
			header("Location: /");
			die();
		}
	}
	else if (isset($_POST['id'])){
		try{
			//Reset the user's password
			$user = ORM::forTable('users')->findOne($_POST["id"]);
			if (!$user){
				throw new Exception(GENERIC_ERROR_MESSAGE);
			}
			if ($user->password_reset_hash === '0' || is_null($user->password_reset_hash)){
				throw new Exception(GENERIC_ERROR_MESSAGE);
			}
			if (!password_verify($_POST["code"], $user->password_reset_hash)){
				throw new Exception(GENERIC_ERROR_MESSAGE);
			}
			if ($_POST["password"] !== $_POST["password_confirmation"]){
				throw new Exception("The passwords don't match.");
			}
			unset($_POST["password_confirmation"]);
			//Make sure the password meets our standards
			if (!passwordSecure($_POST["password"]))
				throw new Exception("Your password must be at least 8 characters long, contain at least 1 lowercase letter, at least 1 uppercase letter, and at least 1 number.");
			//If we're here, we're good
			$user->password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);
			$user->password_reset_hash = NULL;
			$user->save();
			$flasher->success = "Your password has been reset!";
			header("Location: /");
			die();
		}
		catch(Exception $e){
			$flasher->danger = $e->getMessage();
			/*
			echo $twig->render("set_new_password.twig", array("code"=>$_POST["code"], 
																"flash_messages" => $flasher->getAll()
																)
								);
			*/
			unset($_POST);
			header("Location:  " . PROTOCOL . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
			die();
		}
	}
	else{
		unset($_POST);
		$flasher->danger = GENERIC_ERROR_MESSAGE;
		header("Location: /");
		die();
	}
?>