<?php

$clientId = filter_input(INPUT_POST, "clientId");
$orderId = filter_input(INPUT_POST, "hiddenId");
$orderIdShort = filter_input(INPUT_POST, "hiddenIdShort");
$phone = filter_input(INPUT_POST, "numPhone");
$email = filter_input(INPUT_POST, "emailAddr");
$nextDueDate = filter_input(INPUT_POST, "nextDueDate");
$unpaidReason = filter_input(INPUT_POST, "unpaidReason");
$paidConfirm = filter_input(INPUT_POST, "paidConfirm");
//$darkBool = filter_input(INPUT_POST, "darkBool");

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
    $credentials['hostname'],
    $credentials['username'],
    $credentials['password'],
    $credentials['database']); // CONNEXION A LA DB WRITE

function uploadFile($tmpFile, $fileName, $orderIdWhole)
{
    $fileDirectory = "files/" . $orderIdWhole . "/";
    $newFile = $fileDirectory . basename($fileName);

    mkdir($fileDirectory, 0755, true);
    if (move_uploaded_file($tmpFile, $newFile)) {
        return ($newFile);
        /* echo basename($_FILES['fileUpload']['name']). " à été mis en ligne."; */
    } else {
        return ("NULL");
        /* echo "Il y a eu un problème pendant la mise en ligne du fichier..."; */
    }
}

function newComment($orderId, $orderIdShort, $phone, $email, $nextDueDate, $unpaidReason, $paidConfirm, $clientId, $tmpFile, $file)
{
    $orderIdShort = $GLOBALS['orderIdShort'];
    $today = date("Y-m-d");
    $paidBool = ($paidConfirm == "on" ? 1 : 0 );

    $sqlContactInfo = "SELECT Tel FROM webcontrat_client WHERE id='$clientId' ORDER BY DateCreation DESC;";
    if ($resultContactInfo = $GLOBALS['connectionR']->query($sqlContactInfo)) {

        $rowContactInfo = mysqli_fetch_array($resultContactInfo);

        if ($phone == "")
            $phone = $rowContactInfo['Tel'];
    } else {
        echo "Query error: ". $sqlContactInfo ." // ". $GLOBALS['connectionR']->error;
    }

    if ($paidBool == 1) {
        $sqlPaid = "UPDATE Reglement='R' FROM webcontrat_contrat WHERE Commande='$orderId';";
        if ($resultPaid = $GLOBALS['connectionR']->query($sqlPaid)) {

            // UPDATE output doesn't need to be fetched.
        } else {
            echo "Query error: ". $sqlPaid ." // ". $GLOBALS['connectionR']->error;
        }
    }

    $newFile = uploadFile($tmpFile, $file, $orderId);
    $rowNames = "Commentaire,Auteur,Date,Commande,Commande_courte,Prochaine_relance,NumTelephone,AdresseMail,Fichier";
    $rowValues = "\"$unpaidReason\",'dev','$today','$orderId','$orderIdShort','$nextDueDate','$phone','$email','$newFile'";
    $sqlNewComment = "INSERT INTO webcontrat_commentaire ($rowNames) VALUES ($rowValues);";
    if ($resultNewComment = $GLOBALS['connectionW']->query($sqlNewComment)) {

        // INSERT output doesn't need to be fetched.
    } else {
        echo "Query error: ". $sqlNewComment ." // ". $GLOBALS['connectionW']->error; 
    }
    $GLOBALS['connectionR']->close();
    $GLOBALS['connectionW']->close();
}

if (mysqli_connect_error()) {
    die('Connection error. Code: '. mysqli_connect_errno() .' Reason: ' . mysqli_connect_error());
} else {
    
    if (mysqli_set_charset($connectionR, "utf8") === TRUE) {
        $tmpFile = $_FILES['fileUpload']['tmp_name'];
        $file = $_FILES['fileUpload']['name'];
        newComment($orderId, $orderIdShort, $phone, $email, $nextDueDate, $unpaidReason, $paidConfirm, $clientId, $tmpFile, $file);
    }
    else
        die("MySQL SET CHARSET error: ". $connection->error);
}

?>
