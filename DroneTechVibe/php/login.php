<?php
// Previeni qualsiasi output prima degli header
ob_start();

// Imposta l'header per JSON
header('Content-Type: application/json; charset=utf-8');

// Includi il file di configurazione del database
require_once 'config.php';

// Inizializza la sessione se non è già attiva
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera i dati dal form
    $email = $_POST["email"];
    $password = $_POST["password"];
    
    // Prepara la query SQL
    $sql = "SELECT id_cliente, nome, cognome, email, password FROM Clienti WHERE email = ?";
    
    // Prepara lo statement
    if ($stmt = $conn->prepare($sql)) {
        // Associa i parametri
        $stmt->bind_param("s", $email);
        
        // Esegui la query
        if ($stmt->execute()) {
            // Memorizza il risultato
            $stmt->store_result();
            
            // Verifica se l'email esiste
            if ($stmt->num_rows == 1) {
                // Associa le variabili di risultato
                $stmt->bind_result($idCliente, $nome, $cognome, $emailDb, $passwordCriptata);
                
                if ($stmt->fetch()) {
                    // Verifica la password
                    if (password_verify($password, $passwordCriptata)) {
                        // Password corretta, memorizza i dati nella sessione
                        $_SESSION["accesso"] = true;
                        $_SESSION["id"] = $idCliente;
                        $_SESSION["nome"] = $nome;
                        $_SESSION["cognome"] = $cognome;
                        $_SESSION["email"] = $emailDb;
                        
                        // Pulisci qualsiasi output in buffer
                        ob_end_clean();
                        
                        // Restituisci una risposta di successo
                        $risposta = ["successo" => true, "messaggio" => "Accesso effettuato con successo!"];
                        echo json_encode($risposta);
                    } else {
                        // Password errata
                        $risposta = ["successo" => false, "messaggio" => "La password inserita non è corretta."];
                        echo json_encode($risposta);
                    }
                }
            } else {
                // Email non trovata
                $risposta = ["successo" => false, "messaggio" => "Nessun account trovato con questa email."];
                echo json_encode($risposta);
            }
        } else {
            // Errore nell'esecuzione della query
            $risposta = ["successo" => false, "messaggio" => "Errore durante l'accesso: " . $stmt->error];
            echo json_encode($risposta);
        }
        
        // Chiudi lo statement
        $stmt->close();
    } else {
        // Errore nella preparazione della query
        $risposta = ["successo" => false, "messaggio" => "Errore nella preparazione della query: " . $conn->error];
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