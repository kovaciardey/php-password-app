<?php

echo "<table style='border: solid 1px black;'>";
echo "<tr><th>User ID</th><th>Password Hash</th></tr>";

$db_server = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'passwords_test';

$passwordsList = [];

###
define('SALT','ThisIs-A-Salt123');
function salter($string){
    return md5($string . SALT);
}
###

try
{
    $conn = new PDO("mysql:host=$db_server;dbname=$db_name", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("Select user_id, password FROM not_so_smart_users");
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

    foreach ($stmt->fetchAll() as $k => $v) {
//        echo "<tr><td>" . $v["user_id"] . "</td><td>" . $v["password"] . "</td></tr>";
        $passwordsList[$v["user_id"]] = $v["password"];
    }
}
catch (PDOException $e)
{
    echo "Error: " . $e->getMessage();
}

$conn = null;
echo "</table>";

echo "List of passwords: " . count($passwordsList) . " items<br>";


function checkHashAgainstPasswordHash($hash, $passwordsList) {
    foreach ($passwordsList as $key => $value) {
        if ($hash === $value) {
            return true;
        }
    }

    return false;
}

echo "<br><br><br>";
echo "Passwords made out of 5 numbers<br>";
// get the 5 number passwords
//for ($i = 10000; $i < 100000; $i++) {
//    $passToGuess = salter($i);
//
//    if (checkHashAgainstPasswordHash($passToGuess, $passwordsList)) {
//        echo "Found " . $i . "  " . $passToGuess . "<br>";
//    }
//
//}

echo "<br><br><br>";
echo "Passwords made out of 3 capital letters and 1 number<br>";

const LOWERCASE_LETTERS = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
const UPPERCASE_LETTERS = 'A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
const DIGITS = '0,1,2,3,4,5,6,7,8,9';

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

//var_dump(explode(',', UPPERCASE_LETTERS));

// example
$output = sampling(explode(',', UPPERCASE_LETTERS), 3);

//var_dump($output);

foreach (explode(',', DIGITS) as $digit) {
    foreach ($output as $combination) {
        $passToCheck = $combination . $digit;

        echo $passToCheck . "<br>";

        if (checkHashAgainstPasswordHash($passToCheck, $passwordsList)) {
            echo "Found " . $passToCheck . "<br>";
        }
    }
}
?>



