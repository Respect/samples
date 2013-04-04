<?php

// Using Respect\Validation on Forms

// This is a sample on how to use the bare Respect\Validation and pure PHP to
// validate forms and return sensible feedback about the errors.

// Additional comments are available after the 80th column of text per line.
// Keep up with the $validAccount and $invalidAccount, they're the real samples.

/* Configuration, responsible for loading and preparing */

require '../../vendor/autoload.php';                                            // Requiring libraries from the composer installation.

use Respect\Validation\Validator as v;                                          // Aliasing the validator with a short name for easy usage.
use Respect\Validation\Exceptions\ValidationException;                          // Aliasing the exception we use to catch validation errors.

$method     = 'POST';
$minimumAge = 18;                                                               // Configuring minimum age.
$data       = array(                                                            // $data array. These are the only variables passed to template.
    'messages'   => array(),                                                    // Validation messages, if any. Starts empty.
    'method'     => strtolower($method),                                        // Method used on form, must be lowercase by the spec.
    'minimumAge' => $minimumAge                                                 // This is used to render a proper range of years in the template.
);

/* Model, contains the domain logic (in this case, just validating input) */

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

/* Controller, handles requests and binds layers */
                
extract(call_user_func(function ($validAccount, $method) {                      // Sandbox. Calls the function and only extract $data to template.
    $account = array();

    if ($method !== $_SERVER['REQUEST_METHOD']) {                               // Check form method if matches our configuration
        return $account;                                                        // If doesn't match, get out.
    }
    $account = filter_input_array(                                              // We're gonna get the form data using the filter extension
        INPUT_POST,                                                             // We want POST input data...
        array(
            'first' => FILTER_SANITIZE_STRING,                                  // The first name should be a sanitized string
            'last'  => FILTER_SANITIZE_STRING,                                  // The last name should be a sanitized string
            'year'  => FILTER_SANITIZE_INT,                                     // The year should be a sanitized integer
            'month' => FILTER_SANITIZE_INT,                                     // The month should be a sanitized integer
            'day'   => FILTER_SANITIZE_INT                                      // The day should be a sanitized integer
        )
    );

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
    return $account;                                                            // All done. Return to the sandbox and kthxbai.
}, $validAccount, $method) + $data);                                            // Concatenates data from model from initial config.

/* View, displays data */
?>
<!doctype html>
<meta charset=utf-8>
<title>Using Respect\Validation on Forms</title>
<h1>Using Respect\Validation on Forms</h1>
<?php if ($messages): ?>
  <ul>
    <?php foreach ($messages as $n => $message):?>
      <li>
        <?php if (0 === $n) :?><strong><?php endif;?>
        <?php echo $message?>
        <?php if (0 === $n) :?></strong><?php endif;?>
      </li>
    <?php endforeach;?>
  </ul>
<?php endif;?>
<form action="" method=<?php echo $method?>>
  <fieldset>
    <legend>New Account</legend>
    <label>
      First Name 
      <input 
        type=text 
        name=first 
        value="<?php echo filter_var($first, FILTER_SANITIZE_FULL_SPECIAL_CHARS); ?>" 
        maxlength=32>
    </label>
    <label>
      Last Name 
      <input 
        type=text 
        name=last 
        value="<?php echo filter_var($last, FILTER_SANITIZE_FULL_SPECIAL_CHARS); ?>" 
        maxlength=32>
    </label>
    <fieldset>
      <legend>Birthdate</legend>
      <label>
        Year
        <select name=year>
            <?php foreach(range(date('Y') - $minimumAge+10, 1900, -1) as $y) :?>
              <option
                <?php if ($y == $year) echo 'selected' ;?>>
                <?php echo $y;?>
              </option>
            <?endforeach;?>
        </select>
      </label>
      <label>
        Month
        <select name=month>
            <?php foreach(range(1,12) as $m) :?>
              <option
                value="<?php echo $m;?>"
                <?php if ($m == $month) echo 'selected' ;?>>
                <?php echo date('F', strtotime("2001-$m-01"));?>
              </option>
            <?endforeach;?>
        </select>
      </label>
      <label>
        Day
        <select name=day>
            <?php foreach(range(1,31) as $d) :?>
              <option
                <?php if ($d == $day) echo 'selected' ;?>>
                <?php echo $d;?>
              </option>
            <?endforeach;?>
        </select>
      </label>
    </fieldset>
    <button type=submit>Send</button>
  </fieldset>
</form>
