<?php

ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
ini_set('log_errors', 0);
include dirname(__DIR__) . '/vendor/autoload.php';

use Email\Validator;
use PHPUnit\Framework\TestCase;
use \Xicrow\PhpDebug\Timer;

class EmailValidationTest extends TestCase {

	/**
	 * Email Exist will check for existing email and should return true
	 * @param  string $email_id email id
	 * @return bool           wheather the test pass or fails
	 * @dataProvider emailExistDataProvider
	 */
	public function testEmailExist($email_id) {
		Timer::start("Initializing Email Validator");
		$emailValidator = new Validator();
		Timer::start("Validating Email ..");
		$result = $emailValidator->validate($email_id);
		Timer::start("Asserting Response ..");
		$this->assertTrue($result['valid']);
		Timer::stop();
		Timer::stop();
		Timer::stop();
		Timer::showAll();
	}

	public function emailExistDataProvider() {
		return [
			"merajsiddiqui@outlook.com",
			"msiddiqui.jmi@gmail.com",
			"merajsiddiqui@icloud.com",
		];
	}

	/**
	 * Email Exist will check for non existing email and should return false
	 * @param  string $email_id email id
	 * @return bool           wheather the test pass or fails
	 * @dataProvider emailNotExistDataProvider
	 */
	public function testEmailNotExist($email_id) {
		Timer::start("Initializing Email Validator");
		$emailValidator = new Validator();
		Timer::start("Validating Email ..");
		$result = $emailValidator->validate($email_id);
		Timer::start("Asserting Response ..");
		$this->assertFalse($result['valid']);
		Timer::stop();
		Timer::stop();
		Timer::stop();
		Timer::showAll();
	}

	public function emailNotExistDataProvider() {
		return [
			"merajsiddiqui@outlook.com",
			"msiddiqui.jmi@gmail.com",
			"merajsiddiqui@icloud.com",
		];
	}
}
