<?php

require dirname(__DIR__) . "/src/EmailValidator.php";

use Email\Validator;
$email_validator = new Validator();
$email_address = "info@omanzo.com"; // A valid Email Id
$is_email = $email_validator->validate($email_address);
if ($is_email['valid']) {
	echo "Valid Email ID";
} else {
	echo "\n We found this as Invalid : \n Debugging Why it was invalid \n";
	$email_validator->debug($email_address);
}
