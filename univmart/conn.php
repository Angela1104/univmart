<?php
$servername = "localhost";
$password = "kaell";
$username = "root";
$dbase = "univmart";

$conn = new mysqli($servername, $username, $password, $dbase);

if($conn->connect_error)
{
    die("Connection Failed: ".$conn->connect_error);
}
?>