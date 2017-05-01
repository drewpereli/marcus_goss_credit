<?php
	require_once __DIR__ . "/../resources/config.php";
	
	echo $twig->render("index.twig", array(
		"flash_messages" => $flasher->getAll(),
		"current_user" => getCurrentUserAsArray(),
		"sign_up_price" => SIGN_UP_PRICE,
		"monthly_price" => MONTHLY_PRICE
		));
?>