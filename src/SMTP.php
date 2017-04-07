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
	private $host = "localhost";

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
	public $debug_mode = true;

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
			if ($this->debug_mode) {
				print_r($mx_host);
				print_r($mx_priority);
			}
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
		$mx_count = 0;
		while (isset($this->mx_records[$mx_count])) {
			$this->socket = fsockopen(
				$this->mx_records[$mx_count]['host'],
				$this->port,
				$err_code,
				$err_message,
				(float) $this->connection_timeout
			);
			var_dump($err_code);
			stream_set_timeout($this->socket, $this->read_timeout);
			break;
			$mx_count++;
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
			$this->SendMessage("HELO $my_address");
			$this->SendMessage("MAIL FROM: <$sender>");
			$reply = $this->SendMessage("RCPT TO: <$sender>");
			var_dump($reply);
			//parse response code from reply message
			preg_match('/^([0-9]{3})/ims', $reply, $matches);
			$rcpt_response_code = isset($matches[1]) ? (int) $matches[1] : 0;
			$email_valid = (in_array($rcpt_response_code, $acceptence_codes)) ? true : false;
			$this->SendMessage("quit");
			fclose($this->socket);
			return $email_valid;
		}
	}

	public function SendMessage($message) {
		fwrite($this->socket, $message);
		$reply_message = fread($this->socket, 2082);
		if ($this->debug_mode) {
			echo "Send: \t $message \n Rply: \t $reply_message";
		}
		return $reply_message;
	}
}

//Test

$email = "meraj.siddiqui@rankwatch.com";

$abc = new SMTP();
$ab = $abc->validate($email);
var_dump($ab);