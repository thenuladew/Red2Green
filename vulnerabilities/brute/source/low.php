<?php

if( isset( $_GET[ 'Login' ] ) ) {

	// Brute Force Fix: Track failed login attempts in session
	$session_key = 'brute_login_attempts';
	if( !isset( $_SESSION[$session_key] ) ) {
		$_SESSION[$session_key] = 0;
	}

	// Lockout after 3 failed attempts
	if( $_SESSION[$session_key] >= 3 ) {
		$html .= "<pre><br />Account locked. Too many failed login attempts. Please try again later.</pre>";
	} else {
		// Get username
		$user = $_GET[ 'username' ];

		// Get password
		$pass = $_GET[ 'password' ];
		$pass = md5( $pass );

		// Check the database
		$query  = "SELECT * FROM `users` WHERE user = '$user' AND password = '$pass';";
		$result = mysqli_query($GLOBALS["___mysqli_ston"],  $query ) or die( '<pre>' . ((is_object($GLOBALS["___mysqli_ston"])) ? mysqli_error($GLOBALS["___mysqli_ston"]) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false)) . '</pre>' );

		if( $result && mysqli_num_rows( $result ) == 1 ) {
			// Get users details
			$row    = mysqli_fetch_assoc( $result );
			$avatar = $row["avatar"];

			// Login successful - reset counter
			$_SESSION[$session_key] = 0;
			$html .= "<p>Welcome to the password protected area {$user}</p>";
			$html .= "<img src=\"{$avatar}\" />";
		} else {
			// Login failed - increment counter
			$_SESSION[$session_key]++;
			$remaining = 3 - $_SESSION[$session_key];
			if( $remaining > 0 ) {
				$html .= "<pre><br />Username and/or password incorrect. {$remaining} attempt(s) remaining before lockout.</pre>";
			} else {
				$html .= "<pre><br />Username and/or password incorrect. Account is now locked.</pre>";
			}
		}

		((is_null($___mysqli_res = mysqli_close($GLOBALS["___mysqli_ston"]))) ? false : $___mysqli_res);
	}
}
