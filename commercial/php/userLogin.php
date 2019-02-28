<?php

function login($username, $password)
{
    $columns = "username,passwordhash,superuser";
    $sqlLogin = "SELECT $columns FROM webcontrat_utilisateurs WHERE username='$username' OR email='$username';";
    $rowLogin = querySQL($sqlLogin, $GLOBALS['connection'], true, true);
    $total = numberSQL($sqlLogin, $GLOBALS['connection']);
    if ($total === 0) {
        displayLogin("L'utilisateur $username n'existe pas !");
        return ;
    }
    /* $check = password_verify($password, $rowLogin['passwordhash']); // Check if password hash corresponds */
    /* if ($check === FALSE) { // If not, throw */
    if ($password !== $rowLogin['passwordhash']) {
        displayLogin("Mot de passe incorrect !");
        return ;
    }
    $username = $rowLogin['username'];
    $superuser = $rowLogin['superuser'];
    $now = date("Y-m-d H:i:s");
    $sqlRefresh = "UPDATE webcontrat_utilisateurs SET lastLogin='$now' WHERE username='$username';";
    querySQL($sqlRefresh, $GLOBALS['connection'], false); // UPDATE output doesn't need to be fetched.
    setcookie("author", $username, time() + 14400, "/");
    setcookie("connection", $superuser, time() + 14400, "/");
    header("Location: index.php");
}

require_once "helper.php";

$credentials = getCredentials("../credentials.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$username = filter_input(INPUT_POST, "username");
$password = filter_input(INPUT_POST, "password");

$username = sanitizeInput($username);
$password = sanitizeInput($password);

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    login($username, $password);
    $connection->close();
}

?>
