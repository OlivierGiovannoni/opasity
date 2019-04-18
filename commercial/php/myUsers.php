<?php

function userAccess($userId)
{
    $sqlAccess = "SELECT Acces_id,DateAcces FROM webcommercial_multiacces WHERE User_id='$userId';";
    $rowsAccess = querySQL($sqlAccess, $GLOBALS['connection']);

    foreach ($rowsAccess as $rowAccess) {

        $accessId = $rowAccess['Acces_id'];
        $sqlUser = "SELECT username FROM webcontrat_utilisateurs WHERE id='$accessId' ORDER BY id DESC;";
        $rowUser = querySQL($sqlUser, $GLOBALS['connection'], true, true);
        $username = $rowUser['username'];
        $accessDateYMD = $rowAccess['DateAcces'];

        $cells = array($accessId, $username, $accessDateYMD);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require_once "helper.php";

session_start();

$credentialsW = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNECT TO DATABASE WRITE

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $username = $_SESSION['author'];
        $userId = getUserId($username);

        $style = file_get_contents("../html/search.html");
        $style = str_replace("Recherche {type}: {query}", "Mes utilisateurs", $style);
        echo $style;

        echo "<h2>Liste de mes utilisateurs</h2>";
        echo "<table>";

        $cells = array("ID","Nom d'utilisateur","Date d'acces");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        userAccess($userId);
    } else
        header("Location: index.php");
    $connection->close();
}

?>
