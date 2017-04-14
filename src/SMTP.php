<?php

/**
 * @copyright Meraj Ahmad Siddiqui
 * @author Meraj Ahmad Siddiqui <merajsiddiqui@outlook.com>
 * @version 1.0 The first version to validate email address without sending email
 * @package Email Validator \ SMTP protocol
 */

namespace Email;

class SMTP {

	/**
	 * Email address to be validated
	 * @var string
	 */
	protected $email;

	/**
	 * Public IP address of your current server
	 * @var string
	 */
	private $sender = "webmaster@emailvalidator.com";

	/**
	 * Domain on which email is hosted
	 * @var string
	 */
	protected $domain;

	/**
	 * Socket to open connection
	 * @var [type]
	 */
	private $socket;

	/**
	 * Port number to open socket connection
	 * @var int
	 */
	private $port = 25;

	/**
	 * MX records for the mail
	 * @var array
	 */
	private $mx_records = [];

	/**
	 * Max connection time out for socket to open
	 * @var integer
	 */
	public $connection_timeout = 60;

	/**
	 * Maximum time to read response from socket
	 * @var integer
	 */
	public $read_timeout = 10;

	/**
	 * Set debug mode as true to debug the response
	 * @var boolean
	 */
	public $debug_mode;

	public function validate($email_id, $debug_mode = false) {
		if ($email_id) {
			$this->debug_mode = $debug_mode;
			$this->email = $email_id;
			$this->domain = explode("@", $email_id)[1];
			$this->queryMXRecords();
			$validation = $this->communicateThroughSocket();
			if ($this->debug_mode) {
				echo "\n Email address : " . $this->email . " : \t $validation";
			}
			return $validation;
		} else {
			throw new \Exception("Email id expected", 1);
		}
	}

	public function queryMXRecords($domain = false) {
		if ($domain) {
			$this->domain = $domain;
		}
		$mx_host = [];
		$mx_priority = [];
		if (function_exists("getmxrr")) {
			getmxrr($this->domain, $mx_host, $mx_priority);
		} else {
			throw new \Exception("getmxrr not found .. check your php module", 1);
		}
		for ($i = 0; $i < count($mx_host); $i++) {
			$this->mx_records[$i]['host'] = $mx_host[$i];
			$this->mx_records[$i]['priority'] = $mx_priority[$i];
		}
		//sorting on the basis of priority
		$priority = array();
		foreach ($this->mx_records as $key => $record) {
			$priority[$key] = $record['priority'];
		}
		array_multisort($priority, SORT_ASC, $this->mx_records);
		if ($this->debug_mode) {
			echo "\n MX Records for mail exchange \n";
			print_r(json_encode($this->mx_records, JSON_PRETTY_PRINT));
		}
	}
	/**
	 * This method will communicate to the server mx record and find out email
	 * @return bool true or false on basis of email exist or not
	 */
	protected function communicateThroughSocket() {
		$email_valid = false;
		$acceptence_codes = [250, 451, 452];
		//Starting socket connection to read
		$mx_count = 0;
		while ($this->mx_records[$mx_count]) {
			if ($this->debug_mode) {
				echo "\n Connecting to : \t " . $this->mx_records[$mx_count]['host'] . ":" . $this->port;
			}
			if ($this->socket = fsockopen(
				$this->mx_records[$mx_count]['host'],
				$this->port,
				$err_code,
				$err_message,
				(float) $this->connection_timeout
			)) {
				stream_set_timeout($this->socket, $this->read_timeout);
				break;
			}
			$mx_count++;
			if (!isset($this->mx_records[$mx_count])) {
				goto talk;
			}
		}
		//Reading response from socket
		talk:if ($this->socket) {
			$reply_message = fread($this->socket, 2082);
			if ($this->debug_mode) {
				echo "\n Reply: \t $reply_message \n";
			}
			//parse response code from reply message
			preg_match('/^([0-9]{3})/ims', $reply_message, $matches);
			$response_code = isset($matches[1]) ? $matches[1] : 0;
			if ($response_code != 220) {
				$email_valid = false;
			}
			//communicate send message to server
			$this->SendMessage("HELO " . explode("@", $this->sender)[1]);
			$this->SendMessage("MAIL FROM: <" . $this->sender . ">");
			$reply = $this->SendMessage("RCPT TO: <" . $this->email . ">");
			//parse response code from reply message
			preg_match('/^([0-9]{3})/ims', $reply, $matches);
			$rcpt_response_code = isset($matches[1]) ? (int) $matches[1] : 0;
			$email_valid = (in_array($rcpt_response_code, $acceptence_codes)) ? true : false;
			$this->SendMessage("quit");
			fclose($this->socket);
		}
		return $email_valid;
	}

	public function SendMessage($message) {
		fwrite($this->socket, $message . "\r\n");
		$reply_message = fread($this->socket, 2082);
		if ($this->debug_mode) {
			echo "\n Send: \t $message \n Reply: \t $reply_message";
		}
		return $reply_message;
	}
}