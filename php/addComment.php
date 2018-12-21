<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Factures impayées</title>
  </head>
  <body>
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

function skip_accents($str, $charset = "utf-8")
{
    $str = htmlentities($str, ENT_NOQUOTES, $charset);

    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
    $str = preg_replace('#&[^;]+;#', '', $str);

    $str = str_replace(" ", "_", $str);
    return ($str);
}

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

function getLastId($orderId)
{
    $sqlComment = "SELECT Commentaire_id FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    if ($resultComment = $GLOBALS['connectionW']->query($sqlComment)) {
        $rowComment = mysqli_fetch_array($resultComment);
        return ($rowComment['Commentaire_id']);
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

    $lastId = getLastId($orderId);
    $sqlNewLast = "UPDATE webcontrat_commentaire SET DernierCom=0 WHERE Commentaire_id='$lastId';";
    if ($resultNewLast = $GLOBALS['connectionW']->query($sqlNewLast)) {

        // UPDATE output doesn't need to be fetched.
    } else {
        echo "Query error: ". $sqlNewLast ." // ". $GLOBALS['connectionW']->error; 
    }

    $author = $_COOKIE['author'];
    $newFile = uploadFile($tmpFile, $file, $orderId);
    $rowNames = "Commentaire,Auteur,Date,Commande,Commande_courte,Prochaine_relance,NumTelephone,AdresseMail,Fichier,DernierCom";
    $rowValues = "\"$unpaidReason\",'$author','$today','$orderId','$orderIdShort','$nextDueDate','$phone','$email','$newFile',1";
    $sqlNewComment = "INSERT INTO webcontrat_commentaire ($rowNames) VALUES ($rowValues);";
    if ($resultNewComment = $GLOBALS['connectionW']->query($sqlNewComment)) {

        // INSERT output doesn't need to be fetched.
    } else {
        echo "Query error: ". $sqlNewComment ." // ". $GLOBALS['connectionW']->error; 
    }
    header("Location: allComments.php");
}

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
    $file = skip_accents($file);
    newComment($orderId, $orderIdShort, $phone, $email, $nextDueDate, $unpaidReason, $clientId, $tmpFile, $file);

    $connectionR->close();
    $connectionW->close();
}

?>
</body>
</html>
