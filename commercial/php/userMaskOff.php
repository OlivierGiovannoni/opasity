<?php

function maskOff()
{
    $author = $_SESSION['realAuthor'];
    $superuser = $_SESSION['realSuperuser'];

    $_SESSION['author'] =  $author;
    $_SESSION['superuser'] =  $superuser;

    unset($_SESSION['realAuthor']);
    unset($_SESSION['realSuperuser']);

    header("Location: index.php");
}

require_once "helper.php";

session_start();

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$userId = filter_input(INPUT_GET, "userId");
$accessId = filter_input(INPUT_GET, "accessId");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged())
        maskOff();
    else
        header("Location: index.php");
    $connection->close();
}

?>
