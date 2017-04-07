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
	private $host;

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

	public function __construct() {
		$ip_api = "http://ipinfo.io/json";
		$content = file_get_contents($ip_api);
		$content_decoded = json_decode($content);
		$this->host = $content_decoded->ip;
	}

	public function validate($email_id) {
		if ($email_id) {
			$this->email = $email_id;
			$this->domain = explode("@", $email_id)[1];
			$this->queryMXRecords();
			return $this->communicateThroughSocket();
		} else {
			throw new \Exception("Email id expected", 1);
		}
	}

	protected function queryMXRecords() {
		$mx_host = [];
		$mx_priority = [];
		if (function_exists("getmxrr")) {
			getmxrr($this->domain, $mx_host, $mx_priority);
		} else {
			//do something
		}
		for ($i = 0; $i < count($mx_host); $i++) {
			$this->mx_records[$i]['host'] = $mx_host[$i];
			$this->mx_records[$i]['priority'] = $mx_priority[$i];
		}
		$this->mx_records[$i]['host'] = $this->domain;
	}
	/**
	 * This method will communicate to the server mx record and find out email
	 * @return bool true or false on basis of email exist or not
	 */
	protected function communicateThroughSocket() {
		$acceptence_codes = [250, 451, 452];
		//Starting socket connection to read
		foreach ($this->mx_records as $mx_record) {
			$this->socket = fsockopen(
				$mx_record['host'],
				$this->port,
				$err_code,
				$err_message,
				(float) $this->connection_timeout
			);
			stream_set_timeout($this->socket, $this->read_timeout);
		}
		//Reading response from socket
		if ($this->socket) {
			$reply_message = fread($this->socket, 2082);
			//parse response code from reply message
			preg_match('/^([0-9]{3})/ims', $reply_message, $matches);
			$response_code = isset($matches[1]) ? (int) $matches[1] : 0;
			if ($response_code != 220) {
				$email_valid = false;
			}
			//communicate send message to server
			$my_address = $this->host;
			$sender = "webmaster@$my_address";
			fwrite($this->socket, "HELO $my_address");
			fwrite($this->socket, "MAIL FROM: <$sender>");
			fwrite($this->socket, "RCPT TO: <$sender>");
			$reply = fread($this->socket, 2082);
			var_dump($reply);
			//parse response code from reply message
			preg_match('/^([0-9]{3})/ims', $reply, $matches);
			$rcpt_response_code = isset($matches[1]) ? (int) $matches[1] : 0;
			$email_valid = (in_array($rcpt_response_code, $acceptence_codes)) ? true : false;
			fwrite($this->socket, "quit");
			fclose($this->socket);
			var_dump($email_valid);
			return $email_valid;
		}
	}
}
