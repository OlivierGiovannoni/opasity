<?php

/*
** Parameters: String
** Return: String
*/
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
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
** Parameters: String, Object, Bool, Bool
** Return: Array
*/
function querySQL($query, $connection, $first = false, $results = true)
{
    $result = $connection->query($query);
    if ($result && $results === true) {

        if ($first === false)
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        else
            $rows = mysqli_fetch_array($result, MYSQLI_ASSOC);
        return ($rows);
    }
    echo "MySQL query error:<br>Query: " . $sql . "<br>Error: " . $connection->error . "<br>";
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
** Parameters: String
** Return: String
*/
function getOrderIdShort($orderId)
{
    $orderIdShort = substr($orderId, 2, 2) . substr($orderId, 10, 4);
    return ($orderIdShort);
}

/*
** Parameters: String, Bool
** Return: String
*/
function generateCell($data, $header = false)
{
    $open = ($header === true ? "<th>" : "<td>");
    $close = ($header === true ? "</th>" : "</td>");
    $cell = $open . $data . $close;
    return ($cell);
}

/*
** Parameters: Array, Bool
** Return: Array
*/
function generateRow($cells, $header)
{
    $row = array();
    $rowOpen = "<tr>";
    array_push($row, $rowOpen);
    foreach ($cells as $cell) {

        array_push($row, $cell);
    }
    $rowClose = "</tr>";
    array_push($form, $rowClose);
    return ($row);

}

/*
** Parameters: String, String, String
** Return: String
*/
function generateLink($href, $target, $text)
{
    $link = "<a href=\"" . $href . "\" target=\"" . $target . "\">" . $text . "</a>";
    return ($link);
}

?>
