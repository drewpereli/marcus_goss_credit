<?php
	use mikehaertl\wkhtmlto\Pdf;
	require_once __DIR__ . "/../resources/config.php";
	//print_r($_POST);
	//die();
	requirePaymentToAccess();
	//
	//Validate $_POST here
	//
	//If non-admin, set name to current user name
	$currentUser = getCurrentUser();
	if ($currentUser->role === '0'){
		$_POST["name"] = $currentUser->first_name . " " . $currentUser->last_name;
	}
	//
	//Get letter content 
	//If it's a hippa or intent to file lawsuit letter, generate one for each bureau
	$numLetters;
	$includeCreditBureaus;
	$pages;//Number of pages in the letter
	if ($_POST["form_type"] === "HIPPA_violation" 
			|| $_POST["form_type"] === "intent_to_file_lawsuit"
			|| $_POST["form_type"] === "x_deletion"
			|| substr($_POST["form_type"], 0, 11) === "section_609"
	){
		$includeCreditBureaus = true;
		$numLetters = 3;
	}
	else{
		$includeCreditBureaus = false;
		$numLetters = 1;
	}
	if (array_key_exists($_POST["form_type"], $paginatedLetters)){
		$pages = $paginatedLetters[$_POST["form_type"]];
	}
	else{
		$pages = 1;
	}
	$letters = array();
	for ($i = 0 ; $i < $numLetters ; $i++){	
		//If we're sending it to a credit bureau, update the info on each iteration
		if ($includeCreditBureaus){
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
		
		$fileHash;
		$pdfPath;
		$pdf = new Pdf();
		for ($page = 1 ; $page <= $pages ; $page++){
			$_POST["page"] = $page;
			try{
				$content = $twig->render($_POST["form_type"] . ".twig", $_POST);
				//Write to temp html file
				$fileHash = md5(rand());
				//$htmlPath = __DIR__ . '/../tmp/' . $fileHash . '.html';
				$htmlPath = __DIR__ . "/tmp/{$fileHash}.html"; 
				$file = fopen($htmlPath, "w");

				fwrite($file, $content);
				fclose($file);
				$pdf->addPage(HOST_URL . "tmp/{$fileHash}.html");
			}
			catch (Exception $e){
				error_log($e->getFile() . ":" . $e->getLine()." -- ".$e->getMessage());
				$flasher->danger = "There was an error. Please try again later.";
				foreach ($letters as $l){
					unlink($l);
				}
				die();
			}
		}
		$pdfPath = __DIR__ . "/../tmp/{$fileHash}.pdf";
		$success = $pdf->saveAs($pdfPath);
		//Delete html file
		unlink($htmlPath);
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
		$mailer->Subject = 'Basics Credit';
		$mailer->isHTML(true);
		//Temporary
		//If the form type is section_609_verification, strip the :first_attempt, :second_attempt, etc from the end
		//to find the proper email template (which is just section_609_verification_email.twig);
		$form_type;
		if (substr($_POST["form_type"], 0, 24) === "section_609_verification"){
			$form_type = "section_609_verification";
		}
		else{
			$form_type = $_POST["form_type"];
		}
		$mailer->Body = $twig->render($form_type . "_email.twig");
		foreach ($letters as $l){
			$mailer->addAttachment($l, "Credit_Repair_Letter_{$i}.pdf");
		}
		if(!$mailer->send()) {
		    $flasher->danger = "There was an error sending your email. Please try again later.";
		} else {
		    $flasher->success = "<b>Your credit repair letter has been sent to your email!</b><br/>Make sure to read the email for instructions.";
		}
		foreach ($letters as $l){
			unlink($l);
		}
		header("Location:dispute_letters");
		die();
	}
	if ($_POST["action"] === 'download'){
		if ($currentUser->role == '1')
		{
			$downloadPath;
			$filename = $_POST['form_type'] . ".pdf";
			$contentType = 'application/octet-stream';
			//If there is more than one letter, create a zip archive of them
			if (sizeof($letters) > 1){ 
				$downloadPath = "tmp/" . md5(rand()) . ".pdf";
				//$downloadPath = __DIR__ . "/../tmp/test.pdf";
				$command = "gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=$downloadPath";
				foreach ($letters as $i=>$l){
					$command .= " $l";
				}
				exec($command);
				foreach ($letters as $l){
					unlink($l);
				}
				$letters[0] = $downloadPath;
			}
			else{
				$downloadPath = $letters[0];
			}
			//Provide download link
			if (headers_sent()) {
			    echo 'HTTP header already sent';
			} else {
			    if (!is_file($downloadPath)) {
			        header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
			        echo 'File not found';
			    } else if (!is_readable($downloadPath)) {
			        header($_SERVER['SERVER_PROTOCOL'].' 403 Forbidden');
			        echo 'File not readable';
			    } else {
					ignore_user_abort(true);
					header($_SERVER['SERVER_PROTOCOL'] . ' 200 OK');
					header('Content-Description: File Transfer');
					header("Content-Transfer-Encoding: Binary");
				    header("Content-Type: $contentType");
				    header("Content-Disposition: attachment; filename=\"$filename\"");
				    header('Expires: 0');
				    header('Cache-Control: must-revalidate');
				    header('Pragma: public');
				    header('Content-Length: ' . filesize($downloadPath));
				    ob_clean();
					flush();
				    readfile($downloadPath);
				}
			}
		}
		else{
			$flasher->danger = "You're not allowed to do that.";
		}
		unlink($downloadPath);
	}
    foreach ($letters as $l){
		unlink($l);
	}
	die();
?>