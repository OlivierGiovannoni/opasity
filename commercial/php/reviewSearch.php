<?php

function findReviews($userId, $published, $name)
{
    $columns = "Nom,Paru,Annee,DateCreation";
    $sqlReviews = "SELECT Revue_id,DateAcces FROM webcommercial_permissions_revue WHERE User_id='$userId' AND Autorisation=1;";
    $rowsIds  = querySQL($sqlReviews, $GLOBALS['connection']);

    foreach ($rowsIds as $rowId) {

        $reviewId = $rowId['Revue_id'];
        if ($published == 0)
            $sqlReview = "SELECT $columns FROM webcontrat_revue WHERE id='$reviewId' AND Paru='0' ORDER BY DateCreation DESC;";
        else
            $sqlReview = "SELECT $columns FROM webcontrat_revue WHERE id='$reviewId' ORDER BY DateCreation DESC;";
        $rowReview = querySQL($sqlReview, $GLOBALS['connectionR'], true, true);

        $reviewName = $rowReview['Nom'];
        $reviewNameChk = skipAccents($reviewName);

        if ($name === "")
            $cludes = TRUE;
        else
            $cludes = stristr($reviewNameChk, $name);

        if ($cludes !== FALSE) {

            $published = ($rowReview['Paru'] == 1 ? "Oui" : "Non");
            $reviewYear = $rowReview['Annee'];
            $createdAtYMD = $rowReview['DateCreation'];
            $createdAt = date("d/m/Y", strtotime($createdAtYMD));
            $reviewTitle = $reviewName . " " . $reviewYear;
            
            $reviewLink = generateLink("reviewClients.php?reviewId=" . $reviewId, $reviewTitle);

            $cells = array($reviewLink, $published, $createdAt);
            $cells = generateRow($cells);
            foreach ($cells as $cell)
                echo $cell;
        }
    }
}

require_once "helper.php";

session_start();

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

$pub = filter_input(INPUT_GET, "pub");
$query = filter_input(INPUT_GET, "name");
$userId = filter_input(INPUT_GET, "userId");

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $charset = mysqli_set_charset($connection, "utf8");
        $charsetR = mysqli_set_charset($connectionR, "utf8");

        if ($charset === FALSE)
            die("MySQL SET CHARSET error: ". $connection->error);
        if ($charsetR === FALSE)
            die("MySQL SET CHARSET error: ". $connectionR->error);


        if ($userId === "{userId}") {

            $username = $_SESSION['author'];
            $userId = getUserId($username);
        } else
            $username = getUsername($userId);

        $input = file_get_contents("../html/reviewSearch.html");
        $input = str_replace("{query}", $query, $input);
        $input = str_replace("{replace}", "false", $input);
        if ($userId !== "{userId}")
            $input = str_replace("{userId}", $userId, $input);
        echo $input;

        echo "<h2>Liste des revues de: $username</h2>";
        echo "<table>";

        $published = ($pub == 1 ? 0 : 1);
        $href = "reviewSearch.php?userId=" . $userId . "&name=" . $query . "&pub=" . $published;
        $text = ($pub == 1 ? "Afficher revues non-parues" : "Afficher toutes les revues");
        $link = generateLink($href, $text, "_self");
        echo $link;

        $cells = array("Nom","Parue","Date création");
        $cells = generateRow($cells, true);
        foreach ($cells as $cell)
            echo $cell;

        findReviews($userId, $pub, $query);
    } else
        header("Location: index.php");
    $connectionR->close();
    $connection->close();
}

?>
