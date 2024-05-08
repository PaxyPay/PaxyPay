<?php 
$servername = "https://www.proxy.cmh.it"; // Indirizzo del server MySQL
$username = "proxy_cmh_it"; // Nome utente del database
$password = "okG2fre5CK/+OElb"; // Password del database
$dbname = "proxy_cmh_it"; // Nome del database

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connessione al database fallita: " . $conn->connect_error);
}

echo "Connessione al database riuscita";

$conn->close();
?>