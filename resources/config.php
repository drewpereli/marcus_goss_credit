<?php
	define("ENVIRONMENT", "DEVELOPMENT");
	define("PROTOCOL", "http");
	define("HOST_URL", PROTOCOL . "://marcus-credit.drewpereli.com/");
	require_once __DIR__ . "/library/vendor/autoload.php";
	require_once __DIR__ . "/secrets.php";
	$loader = new Twig_Loader_Filesystem(
		array(
			__DIR__ . "/templates/layout",
			__DIR__ . "/templates/pages",
			__DIR__ . "/templates/partials/letters",
			__DIR__ . "/templates/partials/macros"
			)
		);
	if (ENVIRONMENT === "DEVELOPMENT"){	
		$twig = new Twig_Environment($loader, array(
		    //'cache' => __DIR__ . "/../resources/templates/compilation_cache/",
		));
	}
	elseif (ENVIRONMENT === "PRODUCTION"){
		$twig = new Twig_Environment($loader, array(
		    'cache' => __DIR__ . "/../resources/templates/compilation_cache/",
		));
	}
?>