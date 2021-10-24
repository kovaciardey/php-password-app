<?php

// the passwords are loaded from the database on page load

// the database was manually created and populated with the data from the 'not_so_smart_users.sql'

$db_server = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'passwords_test';

$passwordsList = [];

// some useful constants
const FORM_SUBMIT_VALUE = "crack";

const LOWERCASE_LETTERS = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
const UPPERCASE_LETTERS = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
const DIGITS = '0,1,2,3,4,5,6,7,8,9';

try
{
    // use PDO to connect to the databse
    $conn = new PDO("mysql:host=$db_server;dbname=$db_name", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // prepare a statement to load the data from the passwords table
    $stmt = $conn->prepare("Select user_id, password FROM not_so_smart_users");
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

    // save all the passwords in memory
    foreach ($stmt->fetchAll() as $k => $v) {
        $passwordsList[$v["user_id"]] = $v["password"];
    }
}
catch (PDOException $e)
{
    echo "Error: " . $e->getMessage();
}

// close the connection
$conn = null;


### given hash function
define('SALT','ThisIs-A-Salt123');
function salter($string){
    return md5($string . SALT);
}
###

/**
 * @param $hash - the generated hash
 * @param $passwordsList - the list of passwords
 * @return bool - true if the hash matches
 */
function checkHashAgainstPasswordHash($hash, $passwordsList) {
    foreach ($passwordsList as $key => $value) {
        if ($hash === $value) {
            return true;
        }
    }

    return false;
}

/**
 * This function has been taken from
 * https://stackoverflow.com/questions/19067556/php-algorithm-to-generate-all-combinations-of-a-specific-size-from-a-single-set
 *
 * @param $chars
 * @param $size
 * @param array $combinations
 * @return array|mixed
 */
function sampling($chars, $size, $combinations = array()) {

    # if it's the first iteration, the first set
    # of combinations is the same as the set of characters
    if (empty($combinations)) {
        $combinations = $chars;
    }

    # we're done if we're at size 1
    if ($size == 1) {
        return $combinations;
    }

    # initialise array to put new values in
    $new_combinations = array();

    # loop through existing combinations and character set to create strings
    foreach ($combinations as $combination) {
        foreach ($chars as $char) {
            $new_combinations[] = $combination . $char;
        }
    }

    # call same function again for the next iteration
    return sampling($chars, $size - 1, $new_combinations);

}


function calculateFiveDigitPasswords($passwordsList) {
    // calculate all the possible passwords made out of 5 numbers
    $output = sampling(explode(',', DIGITS), 5);

    $solutions = [];

    foreach ($output as $combination) {
        $possiblePassword = salter($combination);

        if (checkHashAgainstPasswordHash($possiblePassword, $passwordsList)) {
            $userId = array_search($possiblePassword, $passwordsList);
            $solutions[] = [
                "userID" => $userId,
                "passwordHash" => $possiblePassword,
                "actualPassword" => $combination
            ];
        }
    }

    return $solutions;
}

function calculateThreeUppercaseAndOneDigitPasswords($passwordsList) {
    // create all possible combinations of 3 uppercase letters
    $output = sampling(explode(',', UPPERCASE_LETTERS), 3);

    $solutions = [];

    // add the digit at the end and salt the password
    foreach (explode(',', DIGITS) as $digit) {
        foreach ($output as $combination) {
            $possiblePassword = salter($combination . $digit);

            if (checkHashAgainstPasswordHash($possiblePassword, $passwordsList)) {
                $userId = array_search($possiblePassword, $passwordsList);
                $solutions[] = [
                    "userID" => $userId,
                    "passwordHash" => $possiblePassword,
                    "actualPassword" => $combination . $digit
                ];
            }
        }
    }

    return $solutions;
}


function getResultsTable($resultsArray) {
    $output = "<table class='table'><tr><th>User ID</th><th>Password Hash</th><th>Actual Password</th></tr>";

    foreach ($resultsArray as $resultRow) {
        $output .= getTableRow($resultRow);
    }

    $output .= "</table>";
    return $output;
}


function getTableRow($result) {
    return "<tr><td>" . $result["userID"] . "</td><td>" . $result["passwordHash"] . "</td><td>" . $result["actualPassword"] . "</td></tr>";
}


$formSubmitted = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["form_submit"]) && $_POST["form_submit"] === FORM_SUBMIT_VALUE) {
        $formSubmitted = true;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Cracker</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="description" content="Password Hasher">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <style type="text/css"></style>
</head>
<body>


<div class="container">
    <h1>Password Cracker</h1>
    <div class="alert alert-dark">
        The program loads the passwords from the database on page load.
    </div>
    <h3>Loaded <span class="badge badge-secondary"><?= count($passwordsList); ?></span> passwords</h3>

    <form action="index.php" method="POST" id="password_submit_form">
        <input type="hidden" name="form_submit" value="<?= FORM_SUBMIT_VALUE ?>">
    </form>

    <button class="btn btn-primary" type="submit" form="password_submit_form" value="Submit">Crack Passwords</button>

    <?php
    if ($formSubmitted)
    {
        echo "<h3>Passwords Made of 5 numbers</h3>";

        $result = calculateFiveDigitPasswords($passwordsList);

        echo getResultsTable($result);

        echo "<h3>Passwords Made of 3 Uppercase Letters and 1 Digit</h3>";

        $result = calculateThreeUppercaseAndOneDigitPasswords($passwordsList);

        echo getResultsTable($result);

    }
    ?>

</div>



<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
