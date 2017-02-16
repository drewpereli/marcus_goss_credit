<?php
	define("ENVIRONMENT", "DEVELOPMENT");
	require_once __DIR__ . "/library/vendor/autoload.php";
	$loader = new Twig_Loader_Filesystem(
		array(
			__DIR__ . "/templates/layout",
			__DIR__ . "/templates/partials",
			__DIR__ . "/templates/pages"
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