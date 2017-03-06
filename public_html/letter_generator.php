<?php
	require_once __DIR__ . "/../resources/config.php";
	requireLogIn();
	echo $twig->render("letter_generator.twig", array(
		"flash_messages" => $flasher->getAll(),
		"current_user" => getCurrentUser()
		));
?>