<?php

$orderId = filter_input(INPUT_POST, "hiddenId");
$orderIdShort = filter_input(INPUT_POST, "hiddenIdShort");
$paidStr = filter_input(INPUT_POST, "hiddenPaid");
$comment = filter_input(INPUT_POST, "comment");
$clientId = NULL;

function credsArr($credsStr)
{
    $credsArr = array();
    $linesArr = explode(";", $credsStr);
    $linesArr = explode("\n", $linesArr[0]);
    foreach ($linesArr as $index => $line) {

        $valueSplit = explode(":", $line);
        $credsArr[$valueSplit[0]] = $valueSplit[1];
    }
    return ($credsArr);
}

$credsFile = "../credentials.txt";
$credentials = credsArr(file_get_contents($credsFile));

$connectionR = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNEXION A LA DB READ

$credsFileW = "../credentialsW.txt";
$credentialsW = credsArr(file_get_contents($credsFileW));

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNEXION A LA DB WRITE

function addUnpaidForm($htmlFileName, $orderId, $orderIdShort, $clientId, $paidStr)
{
    // Get file data.
    $htmlFileData = file_get_contents($htmlFileName);
    // Check if order is paid, to choose whether to display the <form> or not.
    if ($paidStr == "") {
        // Uncomment <form> region.
        $htmlFileData = str_replace("<!-- UNPAID", "", $htmlFileData);
        $htmlFileData = str_replace("-->", "", $htmlFileData);
        // Replace fake variables with real values.
        $htmlFileData = str_replace("{orderId}", $orderId, $htmlFileData);
        $htmlFileData = str_replace("{orderIdShort}", $orderIdShort, $htmlFileData);
        $htmlFileData = str_replace("{clientId}", $clientId, $htmlFileData);
    }
    echo $htmlFileData;
}

function findReview($infoId)
{
    $sqlReviewInfo = "SELECT Revue_id FROM webcontrat_info_revue WHERE Info_id='$infoId';";
    if ($resultReviewInfo = $GLOBALS['connectionR']->query($sqlReviewInfo)) {

        $rowReviewInfo = mysqli_fetch_array($resultReviewInfo);
        $finalId = $rowReviewInfo['Revue_id'];
        $sqlReview = "SELECT id,Nom,Annee FROM webcontrat_revue WHERE id='$finalId';";
        if ($resultReview = $GLOBALS['connectionR']->query($sqlReview)) {

            $rowReview = mysqli_fetch_array($resultReview);
            $finalName = $rowReview['Nom'];
            $finalId = $rowReview['id'];
            $finalYear = $rowReview['Annee'];
            $final = array('Name' => $finalName, 'Id' => $finalId, 'Year' => $finalYear);
            return ($final);
        } else {
            echo "Query error: ". $sqlReview ." // ". $GLOBALS['connectionR']->error;
        }
    } else {
        echo "Query error: ". $sqlReviewInfo ." // ". $GLOBALS['connectionR']->error;

    }
}

function getContactName($orderId)
{
    $sqlContactId = "SELECT Client_id FROM webcontrat_contrat WHERE Commande='$orderId' ORDER BY DateEmission DESC;";
    if ($resultContactId = $GLOBALS['connectionR']->query($sqlContactId)) {

        $rowContactId = mysqli_fetch_array($resultContactId);
        $contactId = $rowContactId['Client_id'];
        $sqlContactName = "SELECT NomContact1,NomSociete FROM webcontrat_client WHERE id='$contactId' ORDER BY DateCreation DESC;";
        if ($resultContactName = $GLOBALS['connectionR']->query($sqlContactName)) {

            $rowContactName = mysqli_fetch_array($resultContactName);
            $contactName = $rowContactName['NomSociete'];
            return (array('id' => $contactId, 'name' => $contactName));
        } else {
            echo "Query error: ". $sqlContactName ." // ". $GLOBALS['connectionR']->error;
        }
    } else {
        echo "Query error: ". $sqlContactId ." // ". $GLOBALS['connectionR']->error;
    }
}


function listComments()
{
    $orderIdShort = $GLOBALS['orderIdShort'];
    $sqlComment = "SELECT Commentaire_id,Commentaire,Auteur,Date,AdresseMail,NumTelephone,Prochaine_relance FROM webcontrat_commentaire WHERE Commande_courte='$orderIdShort' ORDER BY Commentaire_id DESC;";
    if ($resultComment = $GLOBALS['connectionW']->query($sqlComment)) {

        while ($rowComment = mysqli_fetch_array($resultComment)) {

            $contact = getContactName($GLOBALS['orderId']);
            echo "<tr><td>" . $rowComment['Commentaire'] . "</td>";
            echo "<td>" . $rowComment['Auteur'] . "</td>";
            echo "<td>" . date("d/m/Y", strtotime($rowComment['Date'])) . "</td>";

            echo "<td>" . $contact['name'] . "</td>";
            $mailHref = "<a id=\"tableSub\" href=\"mailto:" . $rowComment['AdresseMail'] . "\">" . $rowComment['AdresseMail'] . "</a>";
            echo "<td>" . $mailHref . "</td>";
            echo "<td>" . $rowComment['NumTelephone'] . "</td>";
            echo "<td>" . date("d/m/Y", strtotime($rowComment['Prochaine_relance'])) . "</td></tr>";
        }
    } else {
        echo "Query error: ". $sqlComment ." // ". $GLOBALS['connectionR']->error;
    }
    $GLOBALS['connectionR']->close();
    $GLOBALS['connectionW']->close();
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    $style = file_get_contents("../html/allComments.html");

    $style = str_replace("{order}", $orderIdShort, $style);

    echo $style;
    echo "<i><h1>Contrat: " . $orderIdShort . "</h1></i><br>";
    $revue = findReview($orderId);
    echo "PAIDSTR DEBUG $paidStr VALUE";
    echo "<i><h1 " . ($paidStr == "R" ? "style=color:#008800" : "style=color:#880000") . ">" . ($paidStr == "R" ? "Contrat reglé" : "Contrat non-reglé") . "</i></h2>";
    echo "<i><h2>Paru sur: " . $revue['Name'] . "</h2></i>";
    $client = getContactName($orderId);
    echo "<i><h2>Client: " . $client['name'] . " id: " . $client['id'] . "</h2></i>";
    echo "<table>";
    echo "<tr>";
    echo "<th>Commentaire</th>";
    echo "<th>Auteur</th>";
    echo "<th>Date commentaire</th>";
    echo "<th>Nom de l'entreprise</th>";
    echo "<th>E-mail</th>";
    echo "<th>Téléphone</th>";
    echo "<th>Prochaine relance</th>";
    echo "</tr>";

    if (mysqli_set_charset($connectionR, "utf8") === TRUE) {

        listComments();
        addUnpaidForm("../html/addComment.html", $orderId, $orderIdShort, $clientId, $paidStr);
    }
    else
        die("MySQL SET CHARSET error: ". $connection->error);

    echo "</table><br><br><br>";
    echo "</html>";
}

?>
