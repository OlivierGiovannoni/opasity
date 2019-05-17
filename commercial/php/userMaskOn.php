<?php

function maskOn($userId, $accessId)
{
    $columns = "username,superuser";

    $sqlUser = "SELECT $columns FROM webcontrat_utilisateurs WHERE id='$userId';";
    $rowUser = querySQL($sqlUser, $GLOBALS['connection'], true, true);
    $realUser = $rowUser['username'];
    $realRoot = $rowUser['superuser'];

    $sqlMask = "SELECT $columns FROM webcontrat_utilisateurs WHERE id='$accessId';";
    $rowMask = querySQL($sqlMask, $GLOBALS['connection'], true, true);
    $maskUser = $rowMask['username'];
    $maskRoot = $rowMask['superuser'];

    $_SESSION['author'] = $maskUser;
    $_SESSION['superuser'] = $maskRoot;
    $_SESSION['realAuthor'] = $realUser;
    $_SESSION['realSuperuser'] = $realRoot;

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
        maskOn($userId, $accessId);
    else
        header("Location: index.php");
    $connection->close();
}

?>
