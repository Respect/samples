Using Respect\Validation on Forms
=================================

General Instructions on [the main README.md](https://github.com/Respect/samples/blob/master/README.md)

About
-----

This sample shows how to use pure PHP and only Respect\Validation to handle
form validation, state and error messages. It's contained on the index.php file.

Most of the code is boilerplate and it's entirely commented after the 80th
column of text in each line so you can follow the logic.

Code is split into MVC components but presented in a single file. They could be
separated into different files, but we kept it like this for brevity. Each
layer is marked similar to `/* Controller */` for educational purpouses.

The Respect\Validation part is responsible for validating the submitted form 
data and if the validation fails report specific error messages. This is done
by these two snippets that can be found on the index.php file:

### Validating

```php
<?php
$validAccount = v::arr()                                                        // We're gonna assert an array...
                 ->key('first', $n = v::string()->notEmpty()->length(3, 32))    // With a string key "first" from 3 to 32 chars.
                 ->key('last',  $n)                                             // Reusing the same rule for "last" key
                 ->key('day', v::notEmpty())                                    // Must have a key "date" not empty
                 ->key('month', v::notEmpty())                                  // Must have a key "month" not empty
                 ->key('year', v::notEmpty())                                   // Must have a key "year" not empty
                 ->call(function ($acc) {                                       // Calls this function on the passed array  (will be $_POST)
                    return sprintf(                                             // Formats a string...
                        '%04d-%02d-%02d',                                       // To this date format, padding the numbers with zeroes
                        $acc['year'],
                        $acc['month'], 
                        $acc['day']
                    );
                 }, v::date('Y-m-d')->minimumAge($minimumAge))                  // Then get the fomatted string and validate date and minimum age.
                 ->setName('the New Account');                                  // Naming this rule!
```


### Getting Errors

```php
<?php
    try {                                                                       // Starts an assertion to be used on Respect\Validation
        $validAccount->assert($account);
        $account['messages'] = array("Success!");                               // In case of success, say it!
        
    } catch (ValidationException $invalidAccount) {                             // In case of fail...
        $account['messages'] = array_filter(
            array_values($invalidAccount->findMessages(                         // Get messages for these keys
                array(
                    $validAccount->getName(),                                   // Message for the name we set up there
                    'first.length',                                             // finds the "length" validator for the "first" key
                    'first.notEmpty' => 'First name must not be empty',         // You can override the error message if you want
                    'last.length',
                    'last.notEmpty'  => 'Last name must not be empty',
                    'day.notEmpty'   => 'Birth day name must not be empty',
                    'year.notEmpty'  => 'Birth month name must not be empty',
                    'month.notEmpty' => 'Birth year name must not be empty',
                    'date',
                    'minimumAge'
                )
            ))
        );
    }
```

Feel free to dig the code, make changes and open issues if you want. Any feedback is apreciated.
