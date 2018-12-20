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

function getPhoneNumber($orderId, $clientId)
{
    $sqlComment = "SELECT NumTelephone FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    $rowComment = querySQL($sqlComment, $GLOBALS['connectionW'], true, true);

    if ($rowComment['NumTelephone'] == "") {
        $sqlPhone = "SELECT Tel FROM webcontrat_client WHERE id='$clientId' ORDER BY id DESC;";
        $rowPhone = querySQL($sqlPhone, $GLOBALS['connectionR'], true, true);
        return ($rowPhone['Tel']);
    }
    return ($rowComment['NumTelephone']);
}

function getLastId($orderId)
{
    $sqlComment = "SELECT Commentaire_id FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    $rowComment = querySQL($sqlComment, $GLOBALS['connectionW'], true, true);
    return ($rowComment['Commentaire_id']);
}

function newComment($orderId, $orderIdShort, $phone, $email, $nextDueDate, $unpaidReason, $clientId, $tmpFile, $file)
{
    $orderIdShort = $GLOBALS['orderIdShort'];
    $today = date("Y-m-d");

    if ($nextDueDate == "")
        $nextDueDate = "1970-01-01";
    if ($phone === "")
        $phone = getPhoneNumber($orderId, $clientId);

    $lastId = getLastId($orderId);
    $sqlNewLast = "UPDATE webcontrat_commentaire SET DernierCom=0 WHERE Commentaire_id='$lastId';";
    querySQL($sqlNewLast, $GLOBALS['connectionW'], false);

    $newFile = uploadFile($tmpFile, $file, $orderId);
    $rowNames = "Commentaire,Auteur,Date,Commande,Commande_courte,Prochaine_relance,NumTelephone,AdresseMail,Fichier,DernierCom";
    $rowValues = "\"$unpaidReason\",'dev','$today','$orderId','$orderIdShort','$nextDueDate','$phone','$email','$newFile',1";
    $sqlNewComment = "INSERT INTO webcontrat_commentaire ($rowNames) VALUES ($rowValues);";
    querySQL($sqlNewComment, $GLOBALS['connectionW'], false);
}

require_once "php/helperFunctions.php";

$clientId = filter_input(INPUT_GET, "clientId");
$orderId = filter_input(INPUT_GET, "hiddenId");
$orderIdShort = filter_input(INPUT_GET, "hiddenIdShort");
$phone = filter_input(INPUT_GET, "numPhone");
$email = filter_input(INPUT_GET, "emailAddr");
$nextDueDate = filter_input(INPUT_GET, "nextDueDate");
$unpaidReason = filter_input(INPUT_GET, "unpaidReason");

$unpaidReason = sanitizeInput($unpaidReason);

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
