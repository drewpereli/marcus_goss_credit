<?php
	require_once __DIR__ . "/../resources/config.php";
	$requiredFields = array("name", "email", "message");
	foreach ($requiredFields as $field){
		if (empty($_POST[$field])){
			$flasher->danger = "Please fill out all fields.";
			header("Location: /#contact-row");
			die();
		}
	}
	$mailer->addAddress("admin@basicscredit.com");
	$mailer->Subject = 'New Message From Website';
	$mailer->isHTML(true);
	$mailer->Body = "<div>New message from the website.</div><div>&nbsp;</div>"
					. "<div>Name: " . $_POST["name"] . "<div><div>&nbsp;</div>"
					. "<div>Email: " . $_POST["email"] . "<div><div>&nbsp;</div>"
					. "<div>Message:</div><div>&nbsp;</div>" . $_POST["message"];
	if ($mailer->send()){
		$flasher->success = "Your message has been sent!";
		header("Location: /");
		die();
	}
	else{
		$flasher->danger = "There was an error. Please try again later.";
		header("Location: /#contact-row");
		die();
	}
?>