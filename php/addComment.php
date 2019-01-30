<?php

function uploadFile($tmpFile, $fileName, $orderId)
{
    $fileDirectory = "files/" . $orderId . "/";
	
	$newFile = $fileDirectory . $fileName;

    if ($tmpFile === NULL || $fileName === NULL)
        return ("NULL");
    if (is_dir($fileDirectory) === FALSE)
        mkdir($fileDirectory, 0755, TRUE);
	
    if (move_uploaded_file($tmpFile, $newFile))
		return ($newFile);
    return ("NULL");
}

function getLastId($orderId)
{
    $sqlComment = "SELECT Commentaire_id FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    $rowComment = querySQL($sqlComment, $GLOBALS['connectionW'], true, true);
    $commentId = $rowComment['Commentaire_id'];
    return ($commentId);
}

function newComment($orderId, $orderIdShort, $phone, $email, $nextDueDate, $unpaidReason, $clientId, $tmpFile, $file)
{
    $today = date("Y-m-d");

    if ($nextDueDate == "")
        $nextDueDate = "1970-01-01";
    if ($phone === "")
        $phone = getPhoneNumber($orderId, $clientId);

    $lastId = getLastId($orderId);
    $sqlNewLast = "UPDATE webcontrat_commentaire SET DernierCom=0 WHERE Commentaire_id='$lastId';";
    $rowNewLast = querySQL($sqlNewLast, $GLOBALS['connectionW'], false); // UPDATE output doesn't need to be fetched.

    $author = $_COOKIE['author'];
    $newFile = uploadFile($tmpFile, $file, $orderId);
    $rowNames = "Commentaire,Auteur,Date,Commande,Commande_courte,Prochaine_relance,NumTelephone,AdresseMail,Fichier,DernierCom";
    $rowValues = "\"$unpaidReason\",'$author','$today','$orderId','$orderIdShort','$nextDueDate','$phone','$email','$newFile',1";
    $sqlNewComment = "INSERT INTO webcontrat_commentaire ($rowNames) VALUES ($rowValues);";
    $rowNewComment = querySQL($sqlNewComment, $GLOBALS['connectionW'], false); // INSERT output doesn't need to be fetched.
    header("Location: allComments.php?id=" . $orderId);
}

require_once "helperFunctions.php";

$credentials = getCredentials("../credentials.txt");

$connectionR = new mysqli(
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNEXION A LA DB READ

$credentialsW = getCredentials("../credentialsW.txt");

$connectionW = new mysqli(
    $credentialsW['hostname'],
    $credentialsW['username'],
    $credentialsW['password'],
    $credentialsW['database']); // CONNEXION A LA DB WRITE

$clientId = filter_input(INPUT_POST, "clientId");
$orderId = filter_input(INPUT_POST, "hiddenId");
$orderIdShort = getOrderIdShort($orderId);
$phone = filter_input(INPUT_POST, "numPhone");
$email = filter_input(INPUT_POST, "emailAddr");
$nextDueDate = filter_input(INPUT_POST, "nextDueDate");
$unpaidReason = filter_input(INPUT_POST, "unpaidReason");

$unpaidReason = sanitizeInput($unpaidReason);

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {

    $charsetR = mysqli_set_charset($connectionR, "utf8");
    $charsetW = mysqli_set_charset($connectionW, "utf8");

    if ($charsetR === FALSE)
        die("MySQL SET CHARSET error: ". $connectionR->error);
    else if ($charsetW === FALSE)
        die("MySQL SET CHARSET error: ". $connectionW->error);

    $tmpFile = $_FILES['fileUpload']['tmp_name'];
    $file = $_FILES['fileUpload']['name'];
    $file = skipAccents($file);
    newComment($orderId, $orderIdShort, $phone, $email, $nextDueDate, $unpaidReason, $clientId, $tmpFile, $file);

    $connectionR->close();
    $connectionW->close();
}

?>
