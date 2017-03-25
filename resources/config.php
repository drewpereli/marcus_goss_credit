<?php
	define("ENVIRONMENT", "PRODUCTION");
	define("PROTOCOL", "https");
	
	require_once __DIR__ . "/library/vendor/autoload.php";
	require_once __DIR__ . "/secrets.php";
	require_once __DIR__ . "/library/functions.php";

	$flasher = new Flasher();
	
	$loader = new Twig_Loader_Filesystem(
		array(
			__DIR__ . "/templates/layout",
			__DIR__ . "/templates/pages",
			__DIR__ . "/templates/partials/letters",
			__DIR__ . "/templates/partials/emails",
			__DIR__ . "/templates/partials/macros"
			)
		);
	if (ENVIRONMENT === "DEVELOPMENT"){	
		define("HOST_URL", PROTOCOL . "://marcus-credit.drewpereli.com/");
		$twig = new Twig_Environment($loader, array(
		    //'cache' => __DIR__ . "/../resources/templates/compilation_cache/",
		));
	}
	elseif (ENVIRONMENT === "PRODUCTION"){
		define("HOST_URL", PROTOCOL . "://basicscredit.com/");
		$twig = new Twig_Environment($loader, array(
		    //'cache' => __DIR__ . "/../resources/templates/compilation_cache/",
		));
	}

	ORM::configure('mysql:host=localhost;dbname=credit');
	ORM::configure('username', DB_USER);
	ORM::configure('password', DB_PASSWORD);

	$mailer = new PHPMailer();
	$mailer->isSMTP();                       
    $mailer->Host = 'smtp.zoho.com';
    $mailer->SMTPAuth = true;
    $mailer->Username   = EMAIL_USER;
    $mailer->Password   = EMAIL_PASSWORD;
    $mailer->SMTPSecure = 'ssl';                 
    $mailer->Port = 465;
    $mailer->setFrom(EMAIL_USER, 'Basics Credit');
?>


