<?php

// the passwords are loaded from the database on page load

$db_server = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'passwords_test';

$passwordsList = [];

try
{
    $conn = new PDO("mysql:host=$db_server;dbname=$db_name", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("Select user_id, password FROM not_so_smart_users");
    $stmt->execute();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);

    foreach ($stmt->fetchAll() as $k => $v) {
        $passwordsList[$v["user_id"]] = $v["password"];
    }
}
catch (PDOException $e)
{
    echo "Error: " . $e->getMessage();
}

$conn = null;

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

    <form action="index.php" method="get" id="password_submit_form">
        <input type="submit" name="">
    </form>
</div>



<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
