<?php

function getLastId($clientId, $reviewId)
{
    $sqlComment = "SELECT Commentaire_id FROM webcommercial_commentaire WHERE Client_id='$clientId' AND Revue_id='$reviewId' ORDER BY Commentaire_id DESC;";
    $rowComment = querySQL($sqlComment, $GLOBALS['connection'], true, true);
    $commentId = $rowComment['Commentaire_id'];
    return ($commentId);
}

function newComment($clientId, $reviewId, $contactId, $nextDueDate, $comment, $tmpFile, $file)
{
    $today = date("Y-m-d");

    if ($nextDueDate == "")
        $nextDueDate = "1970-01-01";

    $lastId = getLastId($clientId, $reviewId);
    $sqlNewLast = "UPDATE webcommercial_commentaire SET DernierCom=0 WHERE Commentaire_id='$lastId';";
    querySQL($sqlNewLast, $GLOBALS['connection'], false); // UPDATE output doesn't need to be fetched.

    $author = $_COOKIE['author'];
    //$newFile = uploadFile($tmpFile, $file, $orderId);
    $newFile = "NULL";
    $rowNames = "Commentaire,Auteur,Date,Client_id,Revue_id,Contact_id,Prochaine_relance,Fichier,DernierCom";
    $rowValues = "'$comment','$author','$today','$clientId','$reviewId','$contactId','$nextDueDate','$newFile',1";
    $sqlNewComment = "INSERT INTO webcommercial_commentaire ($rowNames) VALUES ($rowValues);";
    querySQL($sqlNewComment, $GLOBALS['connection'], false); // INSERT output doesn't need to be fetched.

    header("Location: commentList.php?clientId=" . $clientId . "&reviewId=" . $reviewId);
}

require_once "helper.php";

$credentials = getCredentials("../credentialsW.txt");

$connection = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNECT TO DATABASE

$clientId = filter_input(INPUT_POST, "clientId");
$reviewId = filter_input(INPUT_POST, "reviewId");
$contactId = filter_input(INPUT_POST, "contactId");
$nextDueDate = filter_input(INPUT_POST, "nextDueDate");
$comment = filter_input(INPUT_POST, "unpaidReason");
$newFilename = filter_input(INPUT_POST, "newFilename");

$comment = sanitizeInput($comment);

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    if (isLogged()) {

        $charset = mysqli_set_charset($connection, "utf8");

        if ($charset === FALSE)
            die("MySQL SET CHARSET error: ". $connection->error);

        $tmpFile = $_FILES['fileUpload']['tmp_name'];
        $file = $_FILES['fileUpload']['name'];
        $file = sanitizeInput($file);
        $file = skipAccents($file);

        newComment($clientId, $reviewId, $contactId, $nextDueDate, $comment, $tmpFile, $file);
    } else
        displayLogin("Veuillez vous connecter.");

    $connection->close();
}

?>
