<?php
	require_once __DIR__ . "/../resources/config.php";
	requireLogIn();
	//
	//Validate $_POST here
	//
	//Get letter content 
	try{
		$content = $twig->render($_POST["form_type"] . ".twig", $_POST);
	}
	catch (Exception $e){
		echo $e->getMessage();
		die();
	}
	//Write to temp html file
	$fileHash = md5(rand());
	//$htmlPath = __DIR__ . '/../tmp/' . $fileHash . '.html';
	$htmlPath = "tmp/{$fileHash}.html"; 
	$file = fopen($htmlPath, "w");
	fwrite($file, $content);
	fclose($file);
	//chmod($htmlPath, 0400);
	//Create temp pdf of temp html file
	use mikehaertl\wkhtmlto\Pdf;
	//$pdf = new Pdf($htmlPath);
	$pdf = new Pdf(HOST_URL . "/tmp/{$fileHash}.html");
	$pdfPath = __DIR__ . "/../tmp/{$fileHash}.pdf";
	$success = $pdf->saveAs($pdfPath);
	//Delete html file
	unlink($htmlPath);
	if (!$success) {
	    echo $pdf->getError();
		die();
	}
	if ($_POST["submit"] === 'email'){
		$user = getCurrentUser();
		$mailer->addAddress($user->email, "{$user->first_name} {$user->last_name}");
		$mailer->Subject = 'Credit Repair Letter';
		$mailer->isHTML(true);
		$mailer->Body = "Here is your credit repair letter.";
		$mailer->addAttachment($pdfPath, "Credit_Repair_Letter.pdf");
		if(!$mailer->send()) {
		    $flahser->danger = "There was an error sending your email. Please try again later.";
		} else {
		    $flasher->success = "Your credit repair letter has been sent to your email!";
		}
		unlink($pdfPath);
		header("Location:letter_generator.php");
		die();
	}
	if ($_POST["submit"] === 'download'){
		if (getCurrentUser()->role == '1')
		{
			//Provide download link
			ignore_user_abort(true);
			header('Content-Description: File Transfer');
		    header('Content-Type: application/octet-stream');
		    header("Content-Disposition: attachment; filename=\"{$_POST['form_type']}.pdf\"");
		    header('Expires: 0');
		    header('Cache-Control: must-revalidate');
		    header('Pragma: public');
		    header('Content-Length: ' . filesize($pdfPath));
		    readfile($pdfPath);
		}
		else{
			$flasher->danger = "You're not allowed to do that.";
		}
	}
    unlink($pdfPath);
?>




