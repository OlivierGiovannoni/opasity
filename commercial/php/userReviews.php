<?php

function userReviews($userId)
{
    $columns = "Nom,Annee,DateAcces";
    $sqlReviews = "SELECT Revue_id,DateAcces FROM webcommercial_permissions_revue WHERE User_id='$userId' AND Autorisation=1;";
    $rowsIds  = querySQL($sqlClients, $GLOBALS['connection']);

    foreach ($rowsIds as $rowId) {

        $reviewId = $rowId['Revue_id'];
        $sqlReview = "SELECT $columns FROM webcontrat_revue WHERE id='$reviewId' AND Paru=0 ORDER BY DateCreation DESC;";
        $rowReview = querySQL($sqlReview, $GLOBALS['connectionR'], true, true);

        $reviewName = $rowReview['Nom'];
        $reviewYear = $rowReview['Annee'];
        $createdAtYMD = $rowReview['DateCreation'];
        $createdAt = date("d/m/Y", strtotime($createdAtYMD));
        $reviewTitle = $reviewName . " " . $reviewYear;

        $cells = array($reviewTitle, $reviewYear, $createdAt);
        $cells = generateRow($cells);
        foreach ($cells as $cell)
            echo $cell;
    }
}

require "helper.php";

$credentials = getCredentials("../credentials.txt");

$connectionR = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE READ

$credentialsW = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNECT TO DATABASE WRITE

$userId = filter_input(INPUT_GET, "id");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $username = getUsername($userId);

        $style = file_get_contents("../html/search.html");
        $style = str_replace("Recherche {type}: {query}", "Revues de $username", $style);
        echo $style;

        echo "<h2>Liste des revues de: $username</h2>";
        echo "<table>";

        $cells = array("Nom","Année","Date création");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

    } else
        header("Location: index.php");
    $connectionR->close();
    $connection->close();
}

?>
