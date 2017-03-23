<?php
	require_once __DIR__ . "/../resources/config.php";
	$zip = new ZipArchive();
	$destination = 'tmp/test.zip';
	if ($zip->open($destination, ZipArchive::CREATE)!==TRUE) {
	    exit("cannot open <$filename>\n");
	}
	$zip->addFile('test.php', 'it.php');
	$zip->addFile('stripetest.php', 'worked.php');
	$zip->close();
?>