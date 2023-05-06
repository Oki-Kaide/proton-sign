<?php
function randomalphanum($num) {
  $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUV
WXYZ';
  // probably not great perf, clean up later:
  return substr(str_shuffle($permitted_chars), 0, $num);
}

function randomseq($numchars = 1) {
	$chars = "";
	for ($i = 0; $i < $numchars; $i++) {
		$num = 65 + rand()%52;
		if ($num > 90) { $num += 6; }
		$chars .= chr($num);
	}
	return $chars;
}

// exit if document request has expired, e.g. more than 7 days have passed
function exit_if_expired($time) {
  global $expiry_days;
  if ( (time() - $time) > $expiry_days*86400) {
    return_error("expired"); // echo "We're sorry, more than ".$expiry_days." have passed and this is no longer available.";
    exit();
  }
}

// return JSON error code and exit
function return_error($code) {
  $return["error"] = $code;
  echo json_encode($return);
  exit();
}

// filter input to only include alphanumeric characters
function filter_alphanum($input) {
    return preg_replace("/[^a-zA-Z0-9]+/", "", $input);
}

// encoding for use in form value in a form
function encodeFormValue ($s) {
    return htmlentities($s, ENT_COMPAT|ENT_QUOTES,'ISO-8859-1', true); 
}

/* exit_if_expired() unit tests:
echo time();
echo "\n1603275053\n";
exit_if_expired(1603275053);
echo "\n160327505\n";
exit_if_expired(160327505);
 */
?>
