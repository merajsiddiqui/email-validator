<?php

include dirname(__DIR__) . "/vendor/autoload.php";

use Email\Validator;

$email_validator = new Validator();
$email_address = "merajsiddiqui@outlook.com"; // Avalid Email Id
$is_email = $email_validator->validate($email_address);
if ($is_email['valid']) {
	echo "Valid Email ID";
} else {
	echo "\n We found this as Invalid : \n Debugging Why it was invalid \n";
	$email_validator->debug($email_address);
}
