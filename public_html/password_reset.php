<?php
	require_once __DIR__ . "/../resources/config.php";
	$redirectLocation = "/";
	if (!isset($_POST["email"])){ //If we need to send a password reset form
		echo $twig->render("password_reset.twig", array(
			"flash_messages" => $flasher->getAll()
			));
		die();
	}
	else { //If we need to send a password reactivation has to the given email
		try{
			$user = ORM::forTable('users')->where("email", $_POST["email"])->findOne();
			if (!$user){
				throw new Exception("That is not a valid email address.");
			}
			$user->password_hash = "0";
			$code = md5(rand());
			$user->password_reset_hash = password_hash($code, PASSWORD_DEFAULT);
			$user->save();
			$reset_url = HOST_URL . "set_new_password?i={$user->id}&c={$code}";
			//Send the email
			$mailer->addAddress($_POST["email"], "{$user->first_name} {$user->last_name}");
			$mailer->Subject = "Password Reset";
			$mailer->isHTML(true);
			$mailer->Body = $twig->render("password_reset_email.twig", array("password_reset_url"=>$reset_url));
			if (!$mailer->send()){
				throw new Exception("There was an error. Please try again later.");
			}
			else{
				$flasher->success = "A password reset link has been sent to your email.";
				header("Location: /");
			}
		}
		catch (Exception $e){
			$flasher->danger = $e->getMessage();
			echo $twig->render("password_reset.twig", array(
				"flash_messages" => $flasher->getAll()
				));
			die();
		}
	}

?>