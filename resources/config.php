<?php
	define("ENVIRONMENT", "DEVELOPMENT");
	
	require_once __DIR__ . "/library/vendor/autoload.php";
	require_once __DIR__ . "/secrets.php";
	require_once __DIR__ . "/library/functions.php";

	define("PROTOCOL", "https");
	define("HOST_URL", PROTOCOL . "://basicscredit.com/");
	define("GENERIC_ERROR_MESSAGE", "There was an error. Please try again later.");
	define("SIGN_UP_PRICE", 78.99);
	define("MONTHLY_PRICE", 38.50);
	define("ACCESS_PERIOD", 30); //Access period for each payment, in days
	$accessInterval = new DateInterval("P" . ACCESS_PERIOD . "D");

	$flasher = new Flasher();
	
	$loader = new Twig_Loader_Filesystem(
		array(
			__DIR__ . "/templates/layout",
			__DIR__ . "/templates/pages",
			__DIR__ . "/templates/partials",
			__DIR__ . "/templates/partials/letters",
			__DIR__ . "/templates/partials/emails",
			__DIR__ . "/templates/partials/macros"
			)
		);
	if (ENVIRONMENT === "DEVELOPMENT"){	
		$twig = new Twig_Environment($loader, array(
			'debug' => true
		    //'cache' => __DIR__ . "/../resources/templates/compilation_cache/",
		));
		$twig->addExtension(new Twig_Extension_Debug());
	}
	elseif (ENVIRONMENT === "PRODUCTION"){
		$twig = new Twig_Environment($loader, array(
		    //'cache' => __DIR__ . "/../resources/templates/compilation_cache/",
		));
	}

	

	ORM::configure('mysql:host=localhost;dbname=credit_db');
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


    $paginatedLetters = array(
    	"section_609_verification:_first_attempt" => 2,
    	"section_609_verification:_second_attempt" => 2,
    	"section_609_verification:_third_attempt" => 2,
    	"section_609_verification:_fourth_attempt" => 2,
    	"request_for_validation" => 2
    );

?>


