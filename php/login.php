<?php

function login($username, $password)
{
    $sqlLogin = "SELECT passwordhash,superuser FROM webcontrat_utilisateurs WHERE username='$username' OR email='$username';";
    if ($resultLogin = $GLOBALS['connectionW']->query($sqlLogin)) {
        $total = mysqli_num_rows($resultLogin);
        if ($total === 0) {
            echo "L'utilisateur n'existe pas";
            return ;
        }
        $rowLogin = mysqli_fetch_array($resultLogin);
        /* $check = password_verify($password, $rowLogin['passwordhash']); // Check if password hash corresponds */
        /* if ($check === FALSE) { // If not, throw */
        if ($password !== $rowLogin['passwordhash']) {
            echo "Mot de passe incorrect";
            return ;
        }
        $superuser = $rowLogin['superuser'];
        $now = date("Y-m-d h:i:s");
        $sqlRefresh = "UPDATE webcontrat_utilisateurs SET lastLogin='$now' WHERE username='$username';";
        if ($resultRefresh = $GLOBALS['connectionW']->query($sqlRefresh)) {

            // UPDATE output doesn't need to be fetched.
        } else {
            echo "Query error: ". $sqlRefresh ." // ". $GLOBALS['connection']->error;
        }
        setcookie("author", $username, time() + 10800, "/");
        setcookie("connection", $superuser, time() + 10800, "/");
        header("Location: ../index.php");
    }
}

require_once "helperFunctions.php";

$credentialsW = getCredentials("../credentialsW.txt");

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNEXION A LA DB WRITE

$username = filter_input(INPUT_POST, "username");
$password = filter_input(INPUT_POST, "password");

$username = sanitizeInput($username);
$password = sanitizeInput($password);

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    login($username, $password);
    $connectionW->close();
}
?>
