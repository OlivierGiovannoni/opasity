<?php

function createContact($clientId, $fname, $lname, $phone1, $email1, $phone2, $email2, $jobTitle)
{
    $rowNames = "Client_id,Prenom,Nom,NumTelephone1,AdresseMail1,NumTelephone2,AdresseMail2,Fonction";
    $rowValues = "'$clientId','$fname','$lname','$phone1','$email1','$phone2','$email2','$jobTitle'";
    $sqlContact = "INSERT INTO webcommercial_contact ($rowNames) VALUES ($rowValues);";
    querySQL($sqlContact, $GLOBALS['connection'], false); // INSERT output doesn't need to be fetched.
    header("Location: clientContacts.php?id=" . $clientId);
}

require "helper.php";

$credentials = getCredentials("../credentials.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$id = filter_input(INPUT_GET, "id");

$clientId = filter_input(INPUT_POST, "clientId");
$fname = filter_input(INPUT_POST, "fname");
$lname = filter_input(INPUT_POST, "lname");
$phone1 = filter_input(INPUT_POST, "phone1");
$email1 = filter_input(INPUT_POST, "email1");
$phone2 = filter_input(INPUT_POST, "phone2");
$email2 = filter_input(INPUT_POST, "email2");
$jobTitle = filter_input(INPUT_POST, "jobTitle");

$fname = sanitizeInput($fname);
$lname = sanitizeInput($lname);
$phone1 = sanitizeInput($phone1);
$email1 = sanitizeInput($email1);
$phone2 = sanitizeInput($phone2);
$email2 = sanitizeInput($email2);
$jobTitle = sanitizeInput($jobTitle);

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $style = file_get_contents("../html/contactCreate.html");
        $style = str_replace("{clientId}", $id, $style);

        $filled = (
            isset($clientId) &&
            isset($lname));

        if ($filled === false)
            echo $style;
        else
            createContact($clientId, $fname, $lname, $phone1, $email1, $phone2, $email2, $jobTitle);
    } else
        header("Location: index.php");

    $connection->close();
}

?>
