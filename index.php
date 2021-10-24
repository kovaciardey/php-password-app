<?php

echo "<table style='border: solid 1px black;'>";
echo "<tr><th>User ID</th><th>Password Hash</th></tr>";

$db_server = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'passwords_test';

try
{
    $conn = new PDO("mysql:host=$db_server;dbname=$db_name", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("Select user_id, password FROM not_so_smart_users");
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

    foreach ($stmt->fetchAll() as $k => $v) {
        echo "<tr><td>" . $v["user_id"] . "</td><td>" . $v["password"] . "</td></tr>";
    }
}
catch (PDOException $e)
{
    echo "Error: " . $e->getMessage();
}

$conn = null;
echo "</table>";

