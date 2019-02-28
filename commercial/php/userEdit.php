<?php

function editForm($userId, $style)
{
    $columns = "email,fname,lname,username,passwordhash,superuser";
    $sqlUser = "SELECT $columns FROM webcontrat_utilisateurs WHERE id='$userId';";
    $rowUser = querySQL($sqlUser, $GLOBALS['connection'], true, true);

    $email = $rowUser['email'];
    $fname = $rowUser['fname'];
    $lname = $rowUser['lname'];
    $username = $rowUser['username'];
    $password = $rowUser['passwordhash'];
    $superuser = $rowUser['superuser'];

    $style = str_replace("{userId}", $userId, $style);
    $style = str_replace("{fname}", $fname, $style);
    $style = str_replace("{lname}", $lname, $style);
    $style = str_replace("{email}", $email, $style);
    $style = str_replace("{username}", $username, $style);
    $style = str_replace("{password}", $password, $style);
    if ($superuser != 1)
        $style = str_replace("checked>", ">", $style);
    return ($style);
}

function editUser($userId, $fname, $lname, $email, $username, $password, $superuser)
{
    $ruleSET = "fname='$fname',lname='$lname',email='$email',username='$username',passwordhash='$password',superuser='$superuser'";
    $sqlUser = "UPDATE webcontrat_utilisateurs SET $ruleSET WHERE id='$userId';";
    $rowUser = querySQL($sqlUser, $GLOBALS['connection'], false);

    $self = $_COOKIE['author'];
    $myId = getUserId($self);

    if ($myId == $userId)
        header("Location: userLogout.php");
    else
        header("Location: userList.php");
}

require_once "helper.php";

$credentials = getCredentials("../credentials.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$id = filter_input(INPUT_GET, "id");

$userId = filter_input(INPUT_POST, "userId");
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

    if (isLogged() && isAdmin()) {

        $style = file_get_contents("../html/userEdit.html");
        $filled = (
            isset($userId) &&
            isset($fname) &&
            isset($lname) &&
            isset($email) &&
            isset($username) &&
            isset($password) &&
            isset($superuser));

        if ($filled === false) {

            $style = editForm($id, $style); 
            echo $style;
        } else
            editUser($userId, $fname, $lname, $email, $username, $password, $superuser);
    } else
        header("Location: index.php");

    $connection->close();
}

?>
