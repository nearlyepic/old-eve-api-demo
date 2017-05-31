<?php
function loginRedirect($type, $redirect) {
/* This is a function that checks if the user's login is valid.
   It has two modes, supplied at the first argument: 1 or 2.
   When supplied with mode 1, the function will redirect to
   the page supplied in the second argument if the user's
   login is valid. When supplied with mode 2, the function
   will redirect to the page supplied if the user's login
   is invalid. In addition, the function logs the users out if
   the current time has passed the end of their desired session
   time.
*/

if ($type == 1) {
	if ((isset($_SESSION['validated'])) && (date('U') < $_SESSION['logout'])) {
		$dir = sprintf("Location: %s", $redirect);
		header($dir);
		die();
	} else {
		unset($_SESSION['validated']);
	}
}

if ($type == 2) {
	if ((empty($_SESSION['validated'])) || (date('U') > $_SESSION['logout'])) {
		$dir = sprintf("Location: %s", $redirect);
		header($dir);
		unset($_SESSION['validated']);
	}
}
}
?>	
