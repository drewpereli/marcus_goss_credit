<?php
	require_once __DIR__ . "/../resources/config.php";
	requireLogIn();
	echo $twig->render("dispute_letters.twig", array(
		"flash_messages" => $flasher->getAll(),
		"current_user" => getCurrentUserAsArray()
		));
?>