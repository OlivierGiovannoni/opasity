<?php

function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$clientId = filter_input(INPUT_POST, "clientId");
$orderId = filter_input(INPUT_POST, "hiddenId");
$orderIdShort = filter_input(INPUT_POST, "hiddenIdShort");
$phone = filter_input(INPUT_POST, "numPhone");
$email = filter_input(INPUT_POST, "emailAddr");
$nextDueDate = filter_input(INPUT_POST, "nextDueDate");
$unpaidReason = filter_input(INPUT_POST, "unpaidReason");

$unpaidReason = testInput($unpaidReason);

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

function uploadFile($tmpFile, $fileName, $orderIdWhole)
{
    $fileDirectory = "files/" . $orderIdWhole . "/";
    $newFile = $fileDirectory . str_replace(" ", "_", $fileName);
    //$newFile = $fileDirectory . $fileName;

    if ($tmpFile === NULL || $fileName === NULL)
        return ("NULL");
    if (is_dir($fileDirectory) === FALSE)
        mkdir($fileDirectory, 0755, TRUE);
    if (move_uploaded_file($tmpFile, $newFile)) {
        //$newFile = $fileDirectory . rawurlencode($fileName);
        return ($newFile);
        /* echo basename($_FILES['fileUpload']['name']). " à été mis en ligne."; */
    } else {
        return ("NULL");
        /* echo "Il y a eu un problème pendant la mise en ligne du fichier..."; */
    }
}

function getPhoneNumber($orderId, $clientId)
{
    $sqlComment = "SELECT NumTelephone FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    if ($resultComment = $GLOBALS['connectionW']->query($sqlComment)) {
        $rowComment = mysqli_fetch_array($resultComment);

        if ($rowComment['NumTelephone'] == "") {
            $sqlPhone = "SELECT Tel FROM webcontrat_client WHERE id='$clientId' ORDER BY id DESC;";
            if ($resultPhone = $GLOBALS['connectionR']->query($sqlPhone)) {
                $rowPhone = mysqli_fetch_array($resultPhone);
                return ($rowPhone['Tel']);
            } else {
                echo "Query error: ". $sqlPhone ." // ". $GLOBALS['connectionR']->error;
            }
        }
        return ($rowComment['NumTelephone']);
    } else {
        echo "Query error: ". $sqlComment ." // ". $GLOBALS['connectionR']->error;
    }
}

function newComment($orderId, $orderIdShort, $phone, $email, $nextDueDate, $unpaidReason, $clientId, $tmpFile, $file)
{
    $orderIdShort = $GLOBALS['orderIdShort'];
    $today = date("Y-m-d");

    if ($nextDueDate == "")
        $nextDueDate = "1970-01-01";
    if ($phone === "")
        $phone = getPhoneNumber($orderId, $clientId);

    //fetch comment id and set dernier to 0
    $newFile = uploadFile($tmpFile, $file, $orderId);
    $rowNames = "Commentaire,Auteur,Date,Commande,Commande_courte,Prochaine_relance,NumTelephone,AdresseMail,Fichier,DernierCom";
    $rowValues = "\"$unpaidReason\",'dev','$today','$orderId','$orderIdShort','$nextDueDate','$phone','$email','$newFile',1";
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
    
    if (mysqli_set_charset($connectionW, "utf8") === TRUE) {
        $tmpFile = $_FILES['fileUpload']['tmp_name'];
        $file = $_FILES['fileUpload']['name'];
        newComment($orderId, $orderIdShort, $phone, $email, $nextDueDate, $unpaidReason, $clientId, $tmpFile, $file);
        echo "Le commentaire à été envoyé. Vous pouvez désormais fermer cette page. ";
        echo "<a  href=\"../index.php\">Retourner au menu</a>";
    }
    else
        die("MySQL SET CHARSET error: ". $connection->error);
}

?>
