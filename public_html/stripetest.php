<?php
	require_once __DIR__ . "/../resources/config.php";
	echo $twig->render("stripetest.twig", array(
		"flash_messages" => $flasher->getAll(),
		"key" => STRIPE_PUBLIC_KEY
		));
?>