<?php

function logout()
{
    setcookie("author", null, 0, "/");
    setcookie("connection", null, 0, "/");
    header("Location: index.php");
}

logout();

?>
