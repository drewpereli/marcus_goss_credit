<?php
	require_once __DIR__ . "/../resources/config.php";
	requireLogIn();
	//IF we're making a payment
	if (isset($_POST["stripeToken"])){
		$expectedException = false; //This is set to true if we break out of the try statement intentionaly and expectedly
		try{
			$currentUser = getCurrentUser();
			$amount;
			if ($currentUser->has_made_first_payment == 1){
				$amount = MONTHLY_PRICE;
			}
			else if ($currentUser->has_made_first_payment == 0){
				$amount = SIGN_UP_PRICE;
			}
			else{
				throw new Exception("User " . $currentUser->id . " has an invalid value for has_made_first_payment.");
			}
			\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
			// Token is created using Stripe.js or Checkout!
			// Get the payment token submitted by the form:
			$token = $_POST['stripeToken'];
			// Charge the user's card:
			$charge = \Stripe\Charge::create(array(
			  "amount" => $amount * 100,
			  "currency" => "usd",
			  "description" => "Charge from " . $currentUser->first_name . " " . $currentUser->last_name,
			  "source" => $token,
			));
			if (!$charge->paid){
				$flasher->danger = $charge->failure_message;
				$flasher->warning = "There was an error with your payment.";
				$expectedException = true;
				throw new Exception();
			}
			//Payment was succesful. Add a payment entry to the database, and add 30 days to the current user's "has_access_until" field
			$payment = ORM::forTable("payments")->create();
			$payment->user_id = $currentUser->id;
			$payment->amount = $amount;
			$payment->save();
			//Update user field
			//If the date the user has access to services until is in the past, set it to now + 30 days
			//Else, set it to when they have access + 30 days.
			$currentUserAccessTime = new DateTime($currentUser->has_access_until);
			$now = new DateTime();
			$newAccessTime;
			if (!userHasPaidAccess($currentUser)){
				$newAccessTime = $now->add($accessInterval);
			}
			else{
				$newAccessTime = $currentUserAccessTime->add($accessInterval);
			}
			$currentUser->has_access_until = $newAccessTime->format("Y-m-d H:i:s");
			//If this is the current user's sign up payment, mark them as having made their first payment.
			$currentUser->has_made_first_payment = 1;
			$currentUser->save();
			$flasher->success = "Thank you for your payment of \$" . number_format($amount, 2) . "!";
		}
		catch (Exception $e){
			if (!$expectedException){
				$flasher->danger = GENERIC_ERROR_MESSAGE;
				error_log($e->getMessage());
			}
		}
	}
	//Render page
	echo $twig->render("make_payment.twig", array(
		"flash_messages" => $flasher->getAll(),
		"current_user" => getCurrentUserAsArray(),
		"sign_up_price" => SIGN_UP_PRICE,
		"monthly_price" => MONTHLY_PRICE,
		"stripe_public_key" => STRIPE_PUBLIC_KEY,
		"user_has_access" => userHasPaidAccess(getCurrentUser())
		));
?>

