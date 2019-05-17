<?php

function fetchContacts($clientId)
{
    $columns = "Prenom,Nom,NumTelephone1,AdresseMail1,NumTelephone2,AdresseMail2,Fonction";
    $sqlContacts = "SELECT $columns FROM webcommercial_contact WHERE Client_id='$clientId' ORDER BY id DESC;";
    $rowsContacts = querySQL($sqlContacts, $GLOBALS['connection']);

    foreach ($rowsContacts as $rowContact) {

        $lname = $rowContact['Nom'];
        $fname = $rowContact['Prenom'];
        $jobTitle = $rowContact['Fonction'];
        $phone1 = $rowContact['NumTelephone1'];
        $email1 = $rowContact['AdresseMail1'];
        $phone2 = $rowContact['NumTelephone2'];
        $email2 = $rowContact['AdresseMail2'];

        $cells = array($lname, $fname, $jobTitle, $phone1, $email1, $phone2, $email2);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require_once "helper.php";

session_start();

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE WRITE

$clientId = filter_input(INPUT_GET, "id");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $charset = mysqli_set_charset($connection, "utf8");

        if ($charset === FALSE)
            die("MySQL SET CHARSET error: ". $connection->error);

        $clientName = getClientName($clientId);

        $style = file_get_contents("../html/search.html");
        $style = str_replace("Recherche {type}: {query}", "Contacts de $clientName", $style);
        echo $style;

        echo "<h2>Liste des contacts pour l'entreprise: $clientName</h2>";
        echo "<table>";

        $createImage = generateImage("../png/add.png", "Nouveau contact", 24, 24);
        $createLink = generateLink("contactCreate.php?id=" . $clientId, $createImage);
        echo $createLink;

        $cells = array("Nom","Prénom","Fonction","Tel 1","Email 1","Tel 2","Email 2");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        fetchContacts($clientId);

        echo "</table><br><br><br>";
    } else
        header("Location: index.php");

    $connection->close();
}

?>
