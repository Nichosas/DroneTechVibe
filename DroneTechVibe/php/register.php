<?php
// Previeni qualsiasi output prima degli header
ob_start();

// Imposta l'header per JSON
header('Content-Type: application/json; charset=utf-8');

// Includi il file di configurazione del database
require_once 'config.php';

// Verifica se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera i dati dal form
    $nome = trim($_POST["nome"]);
    $cognome = trim($_POST["cognome"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $telefono = trim($_POST["telefono"]);
    $indirizzo = trim($_POST["indirizzo"]);
    
    // Validazione dei dati
    $erroreInput = false;
    $messaggioErrore = "";
    
    // Verifica se l'email è già registrata
    $sql = "SELECT id_cliente FROM Clienti WHERE email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        
        if ($stmt->execute()) {
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $erroreInput = true;
                $messaggioErrore = "Questa email è già registrata.";
            }
        } else {
            $erroreInput = true;
            $messaggioErrore = "Oops! Qualcosa è andato storto. Riprova più tardi.";
        }
        
        $stmt->close();
    }
    
    // Se non ci sono errori, procedi con la registrazione
    if (!$erroreInput) {
        // Prepara la query di inserimento
        $sql = "INSERT INTO Clienti (nome, cognome, email, password, telefono, indirizzo) VALUES (?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // Cripta la password
            $passwordCriptata = password_hash($password, PASSWORD_DEFAULT);
            
            // Associa i parametri
            $stmt->bind_param("ssssss", $nome, $cognome, $email, $passwordCriptata, $telefono, $indirizzo);
            
            // Esegui la query
            if ($stmt->execute()) {
                // Registrazione completata con successo, imposta la sessione
                session_start();
                
                // Memorizza i dati nella sessione
                $_SESSION["accesso"] = true;
                $_SESSION["id"] = $conn->insert_id;
                $_SESSION["nome"] = $nome;
                $_SESSION["cognome"] = $cognome;
                $_SESSION["email"] = $email;
                
                // Restituisci una risposta di successo
                $risposta = ["successo" => true, "messaggio" => "Registrazione completata con successo!"];
                echo json_encode($risposta);
            } else {
                // Errore nell'esecuzione della query
                $risposta = ["successo" => false, "messaggio" => "Errore nella registrazione: " . $stmt->error];
                echo json_encode($risposta);
            }
            
            // Chiudi lo statement
            $stmt->close();
        } else {
            // Errore nella preparazione della query
            $risposta = ["successo" => false, "messaggio" => "Errore nella preparazione della query: " . $conn->error];
            echo json_encode($risposta);
        }
    } else {
        // Ci sono errori di validazione
        $risposta = ["successo" => false, "messaggio" => $messaggioErrore];
        echo json_encode($risposta);
    }
    
    // Chiudi la connessione
    $conn->close();
} else {
    // Il metodo di richiesta non è POST
    $risposta = ["successo" => false, "messaggio" => "Metodo di richiesta non valido."];
    echo json_encode($risposta);
}
?>