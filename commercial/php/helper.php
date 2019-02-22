<?php

/*
** ***********************
** ** Generic functions **
** ***********************
**
*/

/*
** Parameters: String
** Return: String
**
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
**
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
**
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
**
*/
function querySQL($query, $connection, $results = true, $first = false)
{
    $result = $connection->query($query);
    if ($results === true && $result) {

        if ($first === false)
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        else
            $rows = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return ($rows);
    } else if ($results === false && $result)
        return (NULL);
    die("MySQL query error:<br>Query: " . $query . "<br>Error: " . $connection->error . "<br>");
    return (NULL);
}

/*
** Parameters: String, String, String, String
** Return: String
**
*/
function generateInput($type, $name, $value = null, $id = null)
{
    $input = "<input type=\"" . $type . "\" name=\"" . $name . "\" value=\"" . $value . "\" id=\"" . $id . "\">";
    return ($input);
}

/*
** Parameters: String, String, String, Array
** Return: Array
**
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
**
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

        if ($header === false && $cell === "Oui")
            $open = "<td id=\"isPaid\">";
        else if ($header === false && $cell === "Non")
            $open = "<td id=\"isNotPaid\">";
        if ($status === "Le contrat à été passé") {

            $paid = substr($cell, 0, 33);
            $unpaid = substr($cell, 0, 37);
            if ($header === false && $paid === "Le contrat à été passé en pay")
                $open = "<td style=\"color:#008800\">";
            else if ($header === false && $unpaid === "Le contrat à été passé en non-pay")
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
**
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
**
*/
function generateLink($href, $text, $target = "_blank", $onclick = null)
{
    $link = "<a href=\"" . $href . "\" target=\"" . $target . "\" onclick=\"" . $onclick . "\">" . $text . "</a>";
    return ($link);
}

/*
** Parameters: String, String, Integer, Integer
** Return: String
**
*/
function generateImage($src, $desc, $width = 32, $height = 32)
{
    $image = "<img src=\"" . $src . "\" alt=\"" . $desc ."\" title=\"" . $desc ."\" width=\"" . $width . "\" height=\"" . $height. "\">";
    return ($image);
}

/*
** Parameters: String, String
** Return: String
**
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
**
*/

/*
** Parameters: Void
** Return: Bool
**
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
**
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
**
*/
function isAuthor($author)
{
    if ($author === $_COOKIE['author'])
        return (true);
    return (false);
}

/*
** Parameters: String
** Return: Void
**
*/
function displayLogin($message)
{
    $file = "../html/userLogin.html";
    $loginHTML = file_get_contents($file);
    echo $message;
    echo $loginHTML;
}

/*
** Parameters: Bool
** Return: Void
**
*/
function displayRegister($message)
{
    $file = "../html/userCreate.html";
    $registerHTML = file_get_contents($file);
    echo $message;
    echo $registerHTML;
}

/*
** Parameters: String
** Return: String
**
*/
function getUserId($username)
{
    $sqlUser = "SELECT id FROM webcommercial_utilisateurs WHERE username='$username';";
    $rowUser = querySQL($sqlUser, $GLOBALS['connection'], true, true);
    $userId = $rowUser['id'];
    return ($userId);
}

/*
** Parameters: String
** Return: String
**
*/
function getUsername($userId)
{
    $sqlUser = "SELECT username FROM webcommercial_utilisateurs WHERE id='$userId';";
    $rowUser = querySQL($sqlUser, $GLOBALS['connection'], true, true);
    $username = $rowUser['username'];
    return ($username);
}

/*
** Parameters: String, String, String
** Return: String
**
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
** Parameters: String, String, String, String, String, String
** Return: String
**
*/
function addCommentForm($htmlFileName, $orderId, $orderIdShort, $clientId, $phone)
{
    // Get file data.
    $htmlFileData = file_get_contents($htmlFileName);
    // Replace fake variables with real values.
    $htmlFileData = str_replace("{phone}", $phone, $htmlFileData);
    $htmlFileData = str_replace("{orderId}", $orderId, $htmlFileData);
    $htmlFileData = str_replace("{orderIdShort}", $orderIdShort, $htmlFileData);
    $htmlFileData = str_replace("{clientId}", $clientId, $htmlFileData);
    return ($htmlFileData);
}

/*
**
**
**
*/
function getAuthor($commId)
{
    $sqlAuthor = "SELECT Auteur FROM webcommercial_commentaire WHERE Commentaire_id='$commId';";
    $rowAuthor = querySQL($sqlAuthor, $GLOBALS['connection'], true, true);
    $author = $rowAuthor['Auteur'];
    return ($author);
}

/*
** Parameters: String, Bool
** Return: Array
**
*/
function selectLastComment($orderId, $dmy = false)
{
    $sqlComment = "SELECT Date,Commentaire,Prochaine_relance,AdresseMail FROM webcommercial_commentaire WHERE Commande='$orderId' ORDER BY Commentaire_id DESC;";
    $rowComment = querySQL($sqlComment, $GLOBALS['connection'], true, true);

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
**
*/
function isDateValid($date)
{
    if ($date === "Aucune")
        return (false);
    else if ($date === "1970-01-01")
        return (false);
    else if ($date === "0000-00-00")
        return (false);
    return (true);
}

/*
** Parameters: String
** Return: String
**
*/
function getCompanyName($clientId)
{
    $sqlCompany = "SELECT NomSociete FROM webcommercial_client WHERE id='$clientId'";
    $rowCompany = querySQL($sqlCompany, $GLOBALS['connection'], true, true);
    $companyName = $rowCompany['NomSociete'];
    return ($companyName);
}

/*
** Parameters: String
** Return: Integer
**
*/
function checkEmpty($orderId)
{
    $sqlComment = "SELECT Commentaire_id FROM webcommercial_commentaire WHERE Commande='$orderId';";
    $total = numberSQL($sqlComment, $GLOBALS['connection']);
    if ($total === 0)
        return (true);
    return (false);
}

/*
** Parameters: String
** Return: String
*/
function getClientName($clientId)
{
    $sqlClient = "SELECT NomSociete FROM webcommercial_client WHERE id='$clientId';";
    $rowClient = querySQL($sqlClient, $GLOBALS['connection'], true, true);
    $clientName = $rowClient['NomSociete'];
    return ($clientName);
}

?>
