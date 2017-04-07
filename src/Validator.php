<?php

/**
 * @copyright Meraj Ahmad Siddiqui
 * @author Meraj Ahmad Siddiqui <merajsiddiqui@outlook.com>
 * @version 1.0 The first version to validate email address without sending email
 * @package Email Validator \ SMTP protocol
 */

namespace Email;

class Validator {
	/**
	 * Email id to be validated
	 * @var string
	 */
	public $email;
	/**
	 * valid email id
	 * @var array
	 */
	public $valid_emails = [];

	/**
	 * This method will validate the email id provided
	 * @param  string/array $emails It can take a single or multiple email ids
	 * @return array         Email validation result
	 */
	public function validate($emails) {
		if ($emails) {
			$this->email = $emails;
			if (is_array($emails)) {
				$validation_result = [];
				foreach ($emails as $email) {
					$validation_result[] = $this->startValidation($email);
				}
				return $validation_result;
			} elseif (is_string($emails)) {
				$validation_result = $this->startValidation($emails);
				return $validation_result;
			} else {
				throw new \Exception("a single email id or array of email is  accepted ", 1);
			}
		} else {
			throw new \Exception("Please provide email id for validation", 1);
		}
	}

	public function validateDomainRegex($domain_url) {
		$regex = "#^" .
		// protocol identifier
		"(?:(?://)|(?:https?|ftp):\\/\\/)?" .
		// user:pass authentication
		"(?:\\S+(?::\\S*)?@)?" .
		"(?:" .
		// IP address exclusion
		// private & local networks
		"(?!(?:10|127)(?:\\.\\d{1,3}){3})" .
		"(?!(?:169\\.254|192\\.168)(?:\\.\\d{1,3}){2})" .
		"(?!172\\.(?:1[6-9]|2\\d|3[0-1])(?:\\.\\d{1,3}){2})" .
		// IP address dotted notation octets
		// excludes loopback network 0.0.0.0
		// excludes reserved space >= 224.0.0.0
		// excludes network & broacast addresses
		// (first & last IP address of each class)
		"(?:[1-9]\\d?|1\\d\\d|2[01]\\d|22[0-3])" .
		"(?:\\.(?:1?\\d{1,2}|2[0-4]\\d|25[0-5])){2}" .
		"(?:\\.(?:[1-9]\\d?|1\\d\\d|2[0-4]\\d|25[0-4]))" .
		"|" .
		// host name
		"(?:(?:[a-z\\x{00a1}-\\x{ffff}0-9]-*)*[a-z\\x{00a1}-\\x{ffff}0-9]+)" .
		// domain name
		"(?:\\.(?:[a-z\\x{00a1}-\\x{ffff}0-9]-*)*[a-z\\x{00a1}-\\x{ffff}0-9]+)*" .
		// TLD identifier
		"(?:\\.(?:[a-z\\x{00a1}-\\x{ffff}]{2,8}))" .
		")" .
		// port number
		"(?::\\d{2,5})?" .
		// resource path
		"(?:\\/\\S*)?" .
			"$#ui"; // unicode enabled + case insensitive
		return (preg_match($regex, $domain_url)) ? true : false;
	}

	public function validateEmailSMTP($email) {
		$smtp = new SMTP($email);
		$result = $smtp->validate();
		unset($smtp);
		return $result;
	}

	public function getValidEmails() {
		return $this->valid_emails;
	}

	public function startValidation($email) {
		$result = [
			"email" => [],
			"domain" => [],
			"valid" => false,
			"message" => "",
		];
		$result["email"] = $email;
		$result["domain"] = explode("@", $email)[1];
		if ($this->validateDomainRegex($result["domain"])) {
			//$result["valid"] = $this->validateEmailSMTP($email);
			if ($result["valid"]) {
				$this->valid_emails[] = $email;
				$result["message"] = "Valid email Address";
			} else {
				$result["message"] = "Invalid email Address";
			}
		} else {
			$result["message"] = "Invalid domain pattern";
		}
		return $result;
	}
}