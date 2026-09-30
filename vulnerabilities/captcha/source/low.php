<?php

if( isset( $_POST[ 'Change' ] ) && ( $_POST[ 'step' ] == '1' ) ) {
	// Hide the CAPTCHA form
	$hide_form = true;

	// Get input
	$pass_new  = $_POST[ 'password_new' ];
	$pass_conf = $_POST[ 'password_conf' ];

	// Check CAPTCHA from 3rd party
	$resp = recaptcha_check_answer(
		$_DVWA[ 'recaptcha_private_key'],
		$_POST['g-recaptcha-response']
	);

	// Did the CAPTCHA fail?
	if( !$resp ) {
		// What happens when the CAPTCHA was entered incorrectly
		$html     .= "<pre><br />The CAPTCHA was incorrect. Please try again.</pre>";
		$hide_form = false;
		return;
	}
	else {
		// CAPTCHA was correct. Do both new passwords match?
		if( $pass_new == $pass_conf ) {
			// Mark that this session passed the CAPTCHA, and store the
			// passwords server-side instead of trusting hidden fields later
			$_SESSION[ 'captcha_verified' ] = true;
			$_SESSION[ 'pass_new' ]         = $pass_new;
			$_SESSION[ 'pass_conf' ]        = $pass_conf;

			// Show next stage for the user
			$html .= "
				<pre><br />You passed the CAPTCHA! Click the button to confirm your changes.<br /></pre>
				<form action=\"#\" method=\"POST\">
					<input type=\"hidden\" name=\"step\" value=\"2\" />
					<input type=\"submit\" name=\"Change\" value=\"Change\" />
				</form>";
		}
		else {
			// Both new passwords do not match.
			$html     .= "<pre>Both passwords must match.</pre>";
			$hide_form = false;
		}
	}
}

if( isset( $_POST[ 'Change' ] ) && ( $_POST[ 'step' ] == '2' ) ) {
	// Hide the CAPTCHA form
	$hide_form = true;

	// Require that step 1 actually completed the CAPTCHA in THIS session
	if( empty( $_SESSION[ 'captcha_verified' ] ) || $_SESSION[ 'captcha_verified' ] !== true ) {
		$html .= "<pre><br />CAPTCHA verification is required before changing your password. Please start again.</pre>";
		$hide_form = false;
		return;
	}

	// Use the passwords captured in step 1, never trust client-supplied
	// hidden fields for the sensitive action
	$pass_new  = $_SESSION[ 'pass_new' ];
	$pass_conf = $_SESSION[ 'pass_conf' ];

	// Check to see if both password match
	if( $pass_new == $pass_conf ) {
		// They do!
		$pass_new = ((isset($GLOBALS["___mysqli_ston"]) && is_object($GLOBALS["___mysqli_ston"])) ? mysqli_real_escape_string($GLOBALS["___mysqli_ston"],  $pass_new ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
		$pass_new = md5( $pass_new );

		// Update database
		$insert = "UPDATE `users` SET password = '$pass_new' WHERE user = '" . dvwaCurrentUser() . "';";
		$result = mysqli_query($GLOBALS["___mysqli_ston"],  $insert ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>' );

		// Feedback for the end user
		$html .= "<pre>Password Changed.</pre>";

		// Clear the one-time verification and stored passwords
		unset( $_SESSION[ 'captcha_verified' ] );
		unset( $_SESSION[ 'pass_new' ] );
		unset( $_SESSION[ 'pass_conf' ] );
	}
	else {
		// Issue with the passwords matching
		$html .= "<pre>Passwords did not match.</pre>";
		$hide_form = false;
	}

	((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
}

?>