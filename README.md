# Email Validator

This email-validator library is to verify if a given email id exists or not without sending them an email. To verify the owner of the email id you have to send them a mail with some verification parameters.

## Getting Started

Are you using any third party mail service to send mail to users or you have seen enormous fake email IDs by robots or some spammer, Now Validate the email ID if it exist or not before sending them mail. 

### Prerequisites

```
PHP > 5.5
```

### Installing

Colne or download  from github , To clone 
```
git clone https://github.com/merajsiddiqui/email-validator.git
```
Or install via composer 
```
composer require merajsiddiqui/email-validator
```


## Running the tests

`phpUnit` Unit Test has been written and performed. But i advise you to always run the test case before implenting in a big application or making it as a dependency.


## Up nad Running


```
<?php

### If downloaded via composer.
include dirname(__DIR__) . "/vendor/autoload.php";

### Downloaded from github not using Composer.
require dirname(__DIR__) . "/src/EmailValidator.php";


use Email\Validator;
$email_validator = new Validator();
$email_id = "merajsiddiqui@outlook.com";
$result = $email_validator->validate($email_id);
if($result['valid']) {
	echo "Congrats this email id exist";
} else {
	echo "Sorry we were unable to verify, You may retry or send them an email";	
	/**
	 * Debug Why we failed 
	 * Create an issue on github if you find anything
	 * Or mail at < merajsiddiqui@outlook.com >
	 */
	$email_validator->debug($email_id);
}
```

## Authors

* **Meraj Ahmad Siddiqui** - [Meraj Ahmad Siddiqui](https://github.com/merajsiddiqui)

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE.md](LICENSE.md) file for details
