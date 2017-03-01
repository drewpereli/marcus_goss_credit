<?php
	require_once __DIR__ . "/../resources/config.php";
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
    unlink($pdfPath);
?>