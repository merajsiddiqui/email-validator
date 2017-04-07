# Email Validator

An email validation library written in PHP

## Getting Started

This email-validator library is to verify if a given email id exists or not without sending them an email. To verify the owner of the email id you have to send them a mail with some verification parameters.

### Prerequisites


```
PHP > 5.5
getmmxr

```

### Installing

You can download or clone this library directly from github

Clone Library

```
git clone https://github.com/merajsiddiqui/email-validator.git
```
Or install via composer 

```
composer require merajsiddiqui/email-validator
```


## Running the tests

`phpUnit` Unit Test has been written and performed. But i advise you to always run the test case before implenting in a big application or making it as a dependency.


## Deployment and Running

An example has been provided in the examples folder. But lets get it up and running.

```
<?php

include dirname(__DIR__) . "/vendor/autoload.php";

use Email\Validator;

$email_validator = new Validator();

$email_id = "merajsiddiqui@outlook.com";

$result = $email_validator->validate($email_id);

if($result['valid']) {
	echo "Congrats this email id exist";
} else {
	echo "Sorry we were unable to verify, You may retry or send them an email";	
}

```



## Authors

* **Meraj Ahmad Siddiqui** - [Meraj Ahmad Siddiqui](https://github.com/merajsiddiqui)

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE.md](LICENSE.md) file for details
