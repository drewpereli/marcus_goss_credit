<?php
	use mikehaertl\wkhtmlto\Pdf;
	require_once __DIR__ . "/../resources/config.php";
	requireLogIn();
	//
	//Validate $_POST here
	//
	//Get letter content 
	//If it's a hippa or intent to file lawsuit letter, generate one for each bureau
	$numLetters;
	if ($_POST["form_type"] === "HIPPA_violation" 
			|| $_POST["form_type"] === "intent_to_file_lawsuit"){
		$numLetters = 3;
	}
	else{
		$numLetters = 1;
	}
	$letters = array();
	for ($i = 0 ; $i < $numLetters ; $i++){	
		//If we're sending it to a credit bureau, update the info on each iteration
		if ($_POST["form_type"] === "HIPPA_violation" 
			|| $_POST["form_type"] === "intent_to_file_lawsuit"){
			switch ($i){
				case 0:
					$_POST["bureau_name"] = "Equifax Information Services LLC";
					$_POST["bureau_street_address"] = "P.O. Box 740256";
					$_POST["bureau_city"] = "Atlanta";
					$_POST["bureau_state"] = "GA";
					$_POST["bureau_zip"] = "30374";
					break;
				case 1:
					$_POST["bureau_name"] = "Experian";
					$_POST["bureau_street_address"] = "P.O. Box 4500";
					$_POST["bureau_city"] = "Allen";
					$_POST["bureau_state"] = "TX";
					$_POST["bureau_zip"] = "75013";
					break;
				case 2:
					$_POST["bureau_name"] = "TransUnion LLC, Consumer Dispute Center";
					$_POST["bureau_street_address"] = "P.O. Box 2000";
					$_POST["bureau_city"] = "Chester";
					$_POST["bureau_state"] = "PA";
					$_POST["bureau_zip"] = "19022";
					break;
			}
		}
		try{
			$content = $twig->render($_POST["form_type"] . ".twig", $_POST);
		}
		catch (Exception $e){
			echo $e->getFile() . ":" . $e->getLine()." -- ".$e->getMessage();
			foreach ($letters as $l){
				unlink($l);
			}
			die();
		}
		//Write to temp html file
		$fileHash = md5(rand());
		//$htmlPath = __DIR__ . '/../tmp/' . $fileHash . '.html';
		$htmlPath = __DIR__ . "/tmp/{$fileHash}.html"; 
		$file = fopen($htmlPath, "w");
		fwrite($file, $content);
		fclose($file);
		//chmod($htmlPath, 0400);
		//Create temp pdf of temp html file
		//$pdf = new Pdf($htmlPath);
		$pdf = new Pdf(HOST_URL . "tmp/{$fileHash}.html");
		$pdfPath = __DIR__ . "/../tmp/{$fileHash}.pdf";
		$success = $pdf->saveAs($pdfPath);
		//Delete html file
		//unlink($htmlPath);
		if (!$success) {
		    echo $pdf->getError();
		    foreach ($letters as $l){
				unlink($l);
			}
			die();
		}
		array_push($letters, $pdfPath);
	}
	if ($_POST["action"] === 'email'){
		$user = getCurrentUser();
		$mailer->addAddress($user->email, "{$user->first_name} {$user->last_name}");
		$mailer->Subject = 'Credit Repair Letter';
		$mailer->isHTML(true);
		$mailer->Body = "Here is your credit repair letter.";
		foreach ($letters as $l){
			$mailer->addAttachment($l, "Credit_Repair_Letter_{$i}.pdf");
		}
		if(!$mailer->send()) {
		    $flasher->danger = "There was an error sending your email. Please try again later.";
		} else {
		    $flasher->success = "Your credit repair letter has been sent to your email!";
		}
		foreach ($letters as $l){
			unlink($l);
		}
		header("Location:letter_generator.php");
		die();
	}
	if ($_POST["action"] === 'download'){
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
    foreach ($letters as $l){
		unlink($l);
	}
?>




