<?php

include dirname(__DIR__) . "/vendor/autoload.php";

use Email\Validator;

$email_validator = new Validator();

$single_email = "msiddiqui.jmi@gmail.com";
$is_email_valid = $email_validator->validate($single_email);
var_dump($is_email_valid);

$multiple_emails = [
	"merajsiddiqui.jmi@gmail.com",
	"merajsiddiqui@outlook.com",
];

$are_emails_valid = $email_validator->validate($multiple_emails);
var_dump($are_emails_valid);