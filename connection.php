
<?php
$host = 'localhost';
$database = 'id21727120_devalprojects';
$username = 'id21727120_devalacademy';
$password = '#@456WEtu&%20';


try {
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}


