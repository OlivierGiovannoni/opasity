<?php

/*
** ***********************
** ** Generic functions **
** ***********************
*/

/*
** Parameters: String
** Return: String
*/
function sanitizeInput($data) {

    $data = trim($data);
    $data = addslashes($data);
    $data = htmlspecialchars($data);
    return ($data);
}

/*
** Parameters: String
** Return: Array
*/
function getCredentials($credsFile)
{
    $credsStr = file_get_contents($credsFile);
    $credsArr = array();
    $linesArr = explode(";", $credsStr);
    $linesArr = explode("\n", $linesArr[0]);
    foreach ($linesArr as $index => $line) {

        $valueSplit = explode(":", $line);
        $credsArr[$valueSplit[0]] = $valueSplit[1];
    }
    return ($credsArr);
}

/*
** Parameters: String, Object
** Return: Integer
*/
function numberSQL($query, $connection)
{
    $result = $connection->query($query);
    if ($result) {

        $number = mysqli_num_rows($result);
        return ($number);
    }
    die("MySQL query error:<br>Query: " . $query . "<br>Error: " . $connection->error . "<br>");
    return (0);
}

/*
** Parameters: String, Object, Bool, Bool
** Return: Array
*/
function querySQL($query, $connection, $results = true, $first = false)
{
    $result = $connection->query($query);
    if ($result && $results === true) {

        if ($first === false)
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        else
            $rows = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return ($rows);
    }
    else if ($result && $results === false)
        return (NULL);
    die("MySQL query error:<br>Query: " . $query . "<br>Error: " . $connection->error . "<br>");
    return (NULL);
}

/*
** Parameters: String, String, String, String
** Return: String
*/
function generateInput($type, $name, $id, $value)
{
    $input = "<input type=\"" . $type . "\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\">";
    return ($input);
}

/*
** Parameters: String, String, String, Array
** Return: Array
*/
function generateForm($target, $action, $method, $inputs)
{
    $form = array();
    $formOpen = "<form target=\"" . $target . "\" action=\"" . $action . "\" method=\"" . $method . "\">";
    array_push($form, $formOpen);
    foreach ($inputs as $input) {

        array_push($form, $input);
    }
    $formClose = "</form>";
    array_push($form, $formClose);
    return ($form);
}

/*
** Parameters: Array, Bool
** Return: Array
*/
function generateRow($cells, $header = false)
{
    $row = array();
    $rowOpen = "<tr>";
    array_push($row, $rowOpen);
    foreach ($cells as $cell) {

        $open = ($header === true ? "<th>" : "<td>");
        $close = ($header === true ? "</th>" : "</td>");

        $statusLength = strlen("Le contrat à été passé");
        $status = substr($cell, 0, $statusLength);

        if ($cell === "Oui" && !$header)
            $open = "<td id=\"isPaid\">";
        else if ($cell === "Non" && !$header)
            $open = "<td id=\"isNotPaid\">";
        if ($status === "Le contrat à été passé") {

            $paid = substr($cell, 0, 33);
            $unpaid = substr($cell, 0, 37);
            if ($paid === "Le contrat à été passé en pay" && !$header)
                $open = "<td style=\"color:#008800\">";
            else if ($unpaid === "Le contrat à été passé en non-pay" && !$header)
                $open = "<td style=\"color:#FF0000\">";
        }
        $cell = $open . $cell . $close;
        array_push($row, $cell);
    }
    $rowClose = "</tr>";
    array_push($row, $rowClose);
    return ($row);
}

/*
** Parameters: Array, Array
** Return: Array
*/
function generateSelect($rows, $value, $text)
{
    $options = array();
    array_push($options, "<select>");
    foreach ($rows as $row) {

        $option = "<option value\"" . $row[$value] . "\">" . $row[$text] . "</option>";
        array_push($options, $option);
    }
    array_push($options, "</select>");
    return ($options);
}

/*
** Parameters: String, String, String
** Return: String
*/
function generateLink($href, $text, $target = "_blank", $onclick = null)
{
    $link = "<a href=\"" . $href . "\" target=\"" . $target . "\" onclick=\"" . $onclick . "\">" . $text . "</a>";
    return ($link);
}

/*
** Parameters: String, String, Integer, Integer
** Return: String
*/
function generateImage($src, $desc, $width = 32, $height = 32)
{
    $image = "<img src=\"" . $src . "\" alt=\"" . $desc ."\" width=\"" . $width . "\" height=\"" . $height. "\">";
    return ($image);
}

/*
** Parameters: String, String
** Return: String
*/
function skipAccents($str, $charset = "utf-8")
{
    $str = htmlentities($str, ENT_NOQUOTES, $charset);

    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
    $str = preg_replace('#&[^;]+;#', '', $str);

    $str = str_replace(" ", "_", $str);
    return ($str);
}

/*
** ***************************
** ** App-specific function **
** ***************************
*/

/*
** Parameters: Void
** Return: Bool
*/
function isLogged()
{
    if (!isset($_COOKIE['author']))
        return (false);
    return (true);
}

/*
** Parameters: Void
** Return: Bool
*/
function isAdmin()
{
    if (isset($_COOKIE['connection']) && $_COOKIE['connection'] == 1)
        return (true);
    return (false);
}

/*
** Parameters: String
** Return: Bool
*/
function isAuthor($author)
{
    if ($author === $_COOKIE['author'])
        return (true);
    return (false);
}

/*
** Parameters: String
** Return: String
*/
function getOrderIdShort($orderId)
{
    $orderIdShort = substr($orderId, 2, 2) . substr($orderId, 10, 4);
    return ($orderIdShort);
}

/*
** Parameters: String
** Return: String
*/
function getUserId($username)
{
    $sqlUser = "SELECT id FROM webcontrat_utilisateurs WHERE username='$username';";
    $rowUser = querySQL($sqlUser, $GLOBALS['connectionW'], true, true);
    $userId = $rowUser['id'];
    return ($userId);
}

/*
** Parameters: String, String, String
** Return: String
*/
function uploadFile($tmpFile, $fileName, $orderId)
{
    $fileDirectory = "files/" . $orderId . "/";

    $uniqueId = uniqid();
	$newFile = $fileDirectory . $uniqueId . "_" . $fileName;

    if ($tmpFile === NULL || $fileName === NULL)
        return ("NULL");
    if (is_dir($fileDirectory) === FALSE && $fileName != NULL)
        mkdir($fileDirectory, 0755, TRUE);
	
    if (move_uploaded_file($tmpFile, $newFile))
		return ($newFile);
    return ("NULL");
}

/*
** Parameters: Bool
** Return: Void
*/
function displayLogin($message, $root = false)
{
    $file = ($root === false ? "../html/login.html" : "html/login.html");
    $loginHTML = file_get_contents($file);
    echo $message;
    echo $loginHTML;
}

/*
** Parameters: Bool
** Return: Void
*/
function displayRegister($message)
{
    $file = "../html/register.html";
    $registerHTML = file_get_contents($file);
    echo $message;
    echo $registerHTML;
}

/*
** Parameters: String
** Return: Array
*/
function getOrderDetails($orderId)
{
    $sqlOrder = "SELECT Client_id,PrixHT,Reglement,DateEmission FROM webcontrat_contrat WHERE Commande='$orderId';";
    $rowsOrder = querySQL($sqlOrder, $GLOBALS['connectionR']);

    foreach ($rowsOrder as $rowOrder) {

        $clientId = $rowOrder['Client_id'];
        $priceRaw = $rowOrder['PrixHT'];
        $sqlClient = "SELECT id,NomSociete,NomContact1 FROM webcontrat_client WHERE id='$clientId';";
        $rowClient = querySQL($sqlClient, $GLOBALS['connectionR'], true, true);

        $companyName = $rowClient['NomSociete'];
        $contactName = $rowClient['NomContact1'];
        $orderCreation = $rowOrder['DateEmission'];

        $details = array('clientId' => $clientId, 'companyName' => $companyName, 'contactName' => $contactName, 'priceRaw' => $priceRaw, 'creation' => $orderCreation);
        return ($details);
    }
}

/*
** Parameters: String
** Return: Array
*/
function findReview($infoId)
{
    $sqlInfoReview = "SELECT Revue_id FROM webcontrat_info_revue WHERE Info_id='$infoId';";
    $rowInfoReview = querySQL($sqlInfoReview, $GLOBALS['connectionR'], true, true);
    $finalId = $rowInfoReview['Revue_id'];

    $sqlReview = "SELECT id,Nom,Annee,Paru FROM webcontrat_revue WHERE id='$finalId';";
    $rowReview = querySQL($sqlReview, $GLOBALS['connectionR'], true, true);

    $finalName = $rowReview['Nom'];
    $finalId = $rowReview['id'];
    $finalYear = $rowReview['Annee'];
    $finalPub = $rowReview['Paru'];
    $final = array('Name' => $finalName, 'Id' => $finalId, 'Year' => $finalYear, 'Pub' => $finalPub);
    return ($final);
}

/*
** Parameters: String
** Return: Array
*/
function isItPaid($orderId)
{
    $sqlPaidCompta = "SELECT Reglement FROM webcontrat_contrat WHERE Commande='$orderId';";
    $rowPaidCompta = querySQL($sqlPaidCompta, $GLOBALS['connectionR'], true, true);

    $sqlPaidBase = "SELECT Reglement FROM webcontrat_commentaire WHERE Commande='$orderId';";
    $rowPaidBase = querySQL($sqlPaidBase, $GLOBALS['connectionW'], true, true);

    $paid = array('compta' => $rowPaidCompta['Reglement'], 'base' => $rowPaidBase['Reglement']);
    return ($paid);
}

/*
** Parameters: String, String
** Return: String
*/
function getPhoneNumber($orderId, $clientId)
{
    $sqlComment = "SELECT NumTelephone FROM webcontrat_commentaire WHERE Commande='$orderId' AND DernierCom=1 ORDER BY Commentaire_id DESC;";
    $rowComment = querySQL($sqlComment, $GLOBALS['connectionW'], true, true);

    if ($rowComment['NumTelephone'] == "") {
        $sqlPhone = "SELECT Tel FROM webcontrat_client WHERE id='$clientId' ORDER BY id DESC;";
        $rowPhone = querySQL($sqlPhone, $GLOBALS['connectionR'], true, true);
        return ($rowPhone['Tel']);
    }
    return ($rowComment['NumTelephone']);
}

/*
** Parameters: String
** Return: Array
*/
function getContactName($orderId)
{
    $sqlContactId = "SELECT Client_id,PrixHT FROM webcontrat_contrat WHERE Commande='$orderId' ORDER BY DateEmission DESC;";
    $rowContactId = querySQL($sqlContactId, $GLOBALS['connectionR'], true, true);
    $contactId = $rowContactId['Client_id'];

    $sqlContactName = "SELECT NomContact1,NomSociete FROM webcontrat_client WHERE id='$contactId' ORDER BY DateCreation DESC;";
    $rowContactName = querySQL($sqlContactName, $GLOBALS['connectionR'], true, true);
    $contactName = $rowContactName['NomSociete'];

    $contact = array('id' => $contactId, 'name' => $contactName, 'price' => $rowContactId['PrixHT']);
    return ($contact);
}

/*
** Parameters: String, String, String, String, String, String
** Return: String
*/
function addUnpaidForm($htmlFileName, $orderId, $orderIdShort, $clientId, $phone, $paidStr)
{
    // Get file data.
    $htmlFileData = file_get_contents($htmlFileName);
    // Check if order is paid, to choose whether to display the <form> or not.
    /* if ($paidStr == "") { */
    // Uncomment <form> region.
    $htmlFileData = str_replace("<!-- UNPAID", "", $htmlFileData);
    $htmlFileData = str_replace("-->", "", $htmlFileData);
    // Replace fake variables with real values.
    $htmlFileData = str_replace("{phone}", $phone, $htmlFileData);
    $htmlFileData = str_replace("{orderId}", $orderId, $htmlFileData);
    $htmlFileData = str_replace("{orderIdShort}", $orderIdShort, $htmlFileData);
    $htmlFileData = str_replace("{clientId}", $clientId, $htmlFileData);
    /* } */
    return ($htmlFileData);
}

/*
**
**
*/
function getAuthor($commId)
{
    $sqlAuthor = "SELECT Auteur FROM webcontrat_commentaire WHERE Commentaire_id='$commId';";
    $rowAuthor = querySQL($sqlAuthor, $GLOBALS['connectionW'], true, true);
    $author = $rowAuthor['Auteur'];
    return ($author);
}

/*
** Parameters: String, Bool
** Return: Array
*/
function selectLastComment($orderId, $dmy = false)
{
    $sqlComment = "SELECT Date,Commentaire,Prochaine_relance,AdresseMail FROM webcontrat_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    $rowComment = querySQL($sqlComment, $GLOBALS['connectionW'], true, true);

    $text = $rowComment['Commentaire'];
    $date = $rowComment['Date'];
    $reminder = $rowComment['Prochaine_relance'];

    if ($date == NULL)
        $date = "Aucune";
    if ($reminder == NULL)
        $reminder = "Aucune";
    else if ($dmy === true) {

        $date = date("d/m/Y", strtotime($date));
        $reminder = date("d/m/Y", strtotime($reminder));
    }
    $comment = array('text' => $text, 'date' => $date, 'reminder' => $reminder, 'email' => $rowComment['AdresseMail']);
    return ($comment);
}

/*
** Parameters: String
** Return: Bool
*/
function isDateValid($date)
{
    if ($date === "1970-01-01" || $date === "0000-00-00")
        return (false);
    return (true);
}

/*
** Parameters: String
** Return: String
*/
function getCompanyName($clientId)
{
    $sqlCompany = "SELECT NomSociete FROM webcontrat_client WHERE id='$clientId'";
    $rowCompany = querySQL($sqlCompany, $GLOBALS['connectionR'], true, true);
    $companyName = $rowCompany['NomSociete'];
    return ($companyName);
}

/*
** Parameters: String
** Return: Array
*/
function getReviewInfo($reviewId)
{
    $sqlReview = "SELECT Nom,Annee,Paru FROM webcontrat_revue WHERE id='$reviewId'";
    $rowReview = querySQL($sqlReview, $GLOBALS['connectionR'], true, true);
    $review = array('name' => $rowReview['Nom'], 'year' => $rowReview['Annee'], 'published' => $rowReview['Paru']);
    return ($review);
}

/*
** Parameters: String
** Return: Integer
*/
function checkEmpty($orderId)
{
    $sqlComment = "SELECT Commentaire_id FROM webcontrat_commentaire WHERE Commande='$orderId';";
    $total = numberSQL($sqlComment, $GLOBALS['connectionW']);
    if ($total === 0)
        return (true);
    return (false);
}

/*
** Parameters: String
** Return: Integer
*/
function getNbOrders($reviewId)
{
    $sqlOrder = "SELECT Info_id FROM webcontrat_info_revue WHERE Revue_id='$reviewId';";
    $totalOrders = numberSQL($sqlOrder, $GLOBALS['connectionR']);
    return ($totalOrders);
}

/*
** Parameters: String
** Return: Integer
*/
function getUnitPrice($orderId)
{
    $sqlPrice = "SELECT PrixHT FROM webcontrat_contrat WHERE Commande='$orderId';";
    $rowPrice = querySQL($sqlPrice, $GLOBALS['connectionR'], true, true);
    $unitPrice = $rowPrice['PrixHT'];
    return ($unitPrice);
}

/*
** Parameters: String
** Return: Integer
*/
function getTotalPrice($reviewId)
{
    $sqlOrder = "SELECT Info_id FROM webcontrat_info_revue WHERE Revue_id='$reviewId';";
    $rowsOrder = querySQL($sqlOrder, $GLOBALS['connectionR']);
    sort($rowsOrder);
    $totalPrice = 0;
    foreach ($rowsOrder as $order) {

        $orderId = $order['Info_id'];
        $totalPrice += getUnitPrice($orderId);
    }
    return ($totalPrice);
}

?>
