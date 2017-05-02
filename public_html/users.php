<?php
	require_once __DIR__ . "/../resources/config.php";
	requireAdmin();

	$users = ORM::forTable('users')
		->rawQuery("select users.*, payments.amount from users left outer join (select user_id, sum(amount) as amount from payments group by user_id) as payments on users.id = payments.user_id order by created_at desc")
		->findMany();

	echo $twig->render("users.twig", array(
		"flash_messages" => $flasher->getAll(),
		"current_user" => getCurrentUserAsArray(),
		"users" => $users
		));


?>