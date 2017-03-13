<?php
	require_once __DIR__ . "/../resources/config.php";
	// Set your secret key: remember to change this to your live secret key in production
	// See your keys here: https://dashboard.stripe.com/account/apikeys
	/*
	\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

	// Token is created using Stripe.js or Checkout!
	// Get the payment token submitted by the form:
	$token = $_POST['stripeToken'];

	// Charge the user's card:
	$charge = \Stripe\Charge::create(array(
	  "amount" => 50,
	  "currency" => "usd",
	  "description" => "Example charge",
	  "source" => $token,
	));
	var_dump($charge->paid);
	*/
?>