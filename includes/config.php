<?php
//making config as we need this everytime we can just use it through include_once
//1st step for database php connection
$serverName = "localhost";
$dBUsername = "root";
$dBPassword = "";
$dBName = "web";

// $serverName = "wp-md9n8dlzj-mysql";
// $dBUsername = "sqHIpYlLxZ";
// $dBPassword = "fH0B7qd68lefqa37GJEa";
// $dBName = "RZ15h2O4BH";

//Before we can access data in the MySQL database, we need to be able to connect to the server i.e php
$conn = new mysqli($serverName, $dBUsername, $dBPassword, $dBName);

// Check connection
if (!$conn) {
    die("اتصال ناموفق بود: " . $conn->connect_error());
}
?>