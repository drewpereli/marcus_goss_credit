<?php
	require_once __DIR__ . "/../resources/config.php";
	echo $twig->render("FAQ.twig", array(
		"flash_messages" => $flasher->getAll(),
		"current_user" => getCurrentUserAsArray()
		));
?>