<?php
	require_once __DIR__ . "/../resources/config.php";
	//require 'PHPMailerAutoload.php';

	/*
	ORM::configure('mysql:host=localhost;dbname=credit');
	ORM::configure('username', DB_USER);
	ORM::configure('password', DB_PASSWORD);

	
	$user = ORM::forTable("users")->create();
	$user->first_name = "Drew";
	$user->last_name = "Pereli";
	$user->email = "A";
	$user->password_hash = "B";
	$user->save();
	
	$user = ORM::forTable("users")->findMany();
	var_dump($user);
	*/
	/*
	$flasher->warning = "Warning message.";
	$flasher->danger = "Danger message.";
	echo $twig->render("test.twig", array(
		"flash_messages" => $flasher->getAll()
		));
	*/

	/*
	echo "it works";

	
	$mail = new PHPMailer();

	$mail->SMTPDebug = 4;
    $mail->isSMTP();                       
    $mail->Host = 'smtp.zoho.com';
    $mail->SMTPAuth = true;
    $mail->Username   = EMAIL_USER;
    $mail->Password   = EMAIL_PASSWORD;
    $mail->SMTPSecure = 'ssl';                 
    $mail->Port = 465;

    $mail->setFrom('credit@drewpereli.com', 'White Label Credit Repairs');
    $mail->addAddress('drewpereli@gmail.com', 'Drew Pereli');
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    if(!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Message has been sent';
    }
	*/
?>