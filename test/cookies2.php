<?php

foreach ($_POST as $name => $value) {

    setcookie($name, $value, time()+10);
}

echo "<form action=\"cookies3.php\">";
echo "<input type=\"submit\">";
echo "</form>";

?>
