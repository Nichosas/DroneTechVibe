<?php
// Configurazione del database
$host = "localhost"; // Host del database
$username = "root"; // Username del database
$password = ""; // Password del database
$database = "dronetechvibe"; // Nome del database

// Creazione della connessione
$conn = new mysqli($host, $username, $password, $database);

// Verifica della connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Imposta il charset a utf8
$conn->set_charset("utf8");
?>