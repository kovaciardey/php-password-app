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
        if ($hash == $value) {
            return true;
        }
    }

    return false;
}

echo "<br><br><br>";
echo "Passwords made out of 5 numbers<br>";
// get the 5 number passwords
for ($i = 10000; $i < 100000; $i++) {
    $passToGuess = salter($i);

    if (checkHashAgainstPasswordHash($passToGuess, $passwordsList)) {
        echo "Found " . $i . "  " . $passToGuess . "<br>";
    }

}

echo "<br><br><br>";
echo "Passwords made out of 3 capital letters and 1 number<br>";


