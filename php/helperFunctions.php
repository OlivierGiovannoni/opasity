/*
** Parameters: String
** Return: String
*/
function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return ($data);
}

/*
** Parameters: String
** Return: Array
*/
function getCredentials($credsStr)
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

/*
** Parameters: String, Object
** Return: Array
*/
function querySQL($query, $connection)
{
    $sqlPaid = "SELECT Reglement FROM $table WHERE Commande LIKE '$orderId';";
    $result = $connection->query($sql);
    if ($result) {

        $rows = mysqli_fetch_all($result);
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
