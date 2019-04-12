<?php

function userCreate($fname, $lname, $username, $email, $password, $superuser)
{
    $sqlRegister = "SELECT username FROM webcontrat_utilisateurs WHERE username='$username' OR email='$username';";
    $total = numberSQL($sqlRegister, $GLOBALS['connection']);
    if ($total !== 0) {
        displayRegister("L'utilisateur $username existe déjà !");
        return ;
    }
    /* $pwdhash = password_hash($password, PASSWORD_DEFAULT); // Hash password */
    $created = date("Y-m-d H:i:s");
    $rowNames = "username,passwordhash,email,fname,lname,created,lastLogin,superuser";
    $rowValues = "'$username',\"$password\",'$email','$fname','$lname','$created','$created','$superuser'";
    $sqlRegister = "INSERT INTO webcontrat_utilisateurs ($rowNames) VALUES ($rowValues);";
    querySQL($sqlRegister, $GLOBALS['connection'], false); // INSERT output doesn't need to be fetched

    header("Location: userList.php");
}

require_once "helper.php";

session_start();

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$fname = filter_input(INPUT_POST, "fname");
$lname = filter_input(INPUT_POST, "lname");
$email = filter_input(INPUT_POST, "email");
$username = filter_input(INPUT_POST, "username");
$password = filter_input(INPUT_POST, "password");
$superuser = filter_input(INPUT_POST, "superuser");

$fname = sanitizeInput($fname);
$lname = sanitizeInput($lname);
$email = sanitizeInput($email);
$username = sanitizeInput($username);
$password = sanitizeInput($password);
$superuser = ($superuser == "on" ? 1 : 0);

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged() && isAdmin())
        userCreate($fname, $lname, $username, $email, $password, $superuser);
    else
        displayLogin("Veuillez vous connecter.");

    $connection->close();
}

?>
