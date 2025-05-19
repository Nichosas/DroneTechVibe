<?php
// Previeni qualsiasi output prima dell'invio della risposta JSON
ob_start();

// Includi il file di configurazione del database
require_once 'config.php';
require_once 'auth.php';

// Verifica se l'utente è loggato
Autenticazione::richiediAccesso();

// Classe per la gestione delle prenotazioni
class GestorePrenotazioni {
    private $connessione;
    private $idCliente;
    
    public function __construct($connessione) {
        $this->connessione = $connessione;
        $this->idCliente = $_SESSION["id"];
    }
    
    // Metodo per prenotare un noleggio drone
    public function prenotaNoleggio($idDrone, $dataInizio, $dataFine, $note = "") {
        // Validazione dei dati
        $erroreInput = false;
        $messaggioErrore = "";
        
        // Verifica se il drone è disponibile per le date selezionate
        // Questa è una versione semplificata, in un'applicazione reale dovresti verificare
        // se il drone è già prenotato per le date richieste
        
        // Se non ci sono errori, procedi con la prenotazione
        if (!$erroreInput) {
            // Prepara la query di inserimento nella tabella Noleggio
            $sql = "INSERT INTO Noleggi (data_inizio, data_fine, note, id_cliente, id_prodotto, stato) 
                    VALUES (?, ?, ?, ?, ?, 'Confermata')";
            
            if ($stmt = $this->connessione->prepare($sql)) {
                // Associa i parametri
                $stmt->bind_param("sssii", $dataInizio, $dataFine, $note, $this->idCliente, $idDrone);
                
                // Esegui la query
                if ($stmt->execute()) {
                    // Prenotazione completata con successo
                    return ["successo" => true, "messaggio" => "Noleggio prenotato con successo!"];
                } else {
                    // Errore nell'esecuzione della query
                    return ["successo" => false, "messaggio" => "Errore nella prenotazione: " . $stmt->error];
                }
                
                // Chiudi lo statement
                $stmt->close();
            } else {
                // Errore nella preparazione della query
                return ["successo" => false, "messaggio" => "Errore nella preparazione della query: " . $this->connessione->error];
            }
        } else {
            // Ci sono errori di validazione
            return ["successo" => false, "messaggio" => $messaggioErrore];
        }
    }
    
    // Metodo per prenotare un servizio
    public function prenotaServizio($idServizio, $dataServizio, $oraServizio = "", $note = "") {
        // Validazione dei dati
        $erroreInput = false;
        $messaggioErrore = "";
        
        // Verifica se il servizio esiste
        $sqlVerifica = "SELECT id_servizio FROM servizi WHERE id_servizio = ?";
        if ($stmtVerifica = $this->connessione->prepare($sqlVerifica)) {
            $stmtVerifica->bind_param("i", $idServizio);
            $stmtVerifica->execute();
            $stmtVerifica->store_result();
            
            if ($stmtVerifica->num_rows == 0) {
                $erroreInput = true;
                $messaggioErrore = "Servizio non trovato.";
            }
            
            $stmtVerifica->close();
        } else {
            $erroreInput = true;
            $messaggioErrore = "Errore nella verifica del servizio.";
        }
        
        // Se non ci sono errori, procedi con la prenotazione
        if (!$erroreInput) {
            // Prepara la query di inserimento nella tabella Prenotazione_Servizio
            $sql = "INSERT INTO Prenotazione_Servizi (data_inizio, data_fine, ora, note, id_cliente, id_servizio, stato) 
                    VALUES (?, ?, ?, ?, ?, ?, 'Confermata')";
            
            if ($stmt = $this->connessione->prepare($sql)) {
                // Imposta la data di fine uguale alla data di inizio per i servizi (non sono noleggi)
                $dataFine = $dataServizio;
                
                // Associa i parametri
                $stmt->bind_param("ssssii", $dataServizio, $dataFine, $oraServizio, $note, $this->idCliente, $idServizio);
                
                // Esegui la query
                if ($stmt->execute()) {
                    // Prenotazione completata con successo
                    return ["successo" => true, "messaggio" => "Servizio prenotato con successo!"];
                } else {
                    // Errore nell'esecuzione della query
                    return ["successo" => false, "messaggio" => "Errore nella prenotazione: " . $stmt->error];
                }
                
                // Chiudi lo statement
                $stmt->close();
            } else {
                // Errore nella preparazione della query
                return ["successo" => false, "messaggio" => "Errore nella preparazione della query: " . $this->connessione->error];
            }
        } else {
            // Ci sono errori di validazione
            return ["successo" => false, "messaggio" => $messaggioErrore];
        }
    }

}

// Verifica se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gestore = new GestorePrenotazioni($conn);
    $risposta = [];
    
    // Determina il tipo di prenotazione
    if (isset($_POST["tipo_prenotazione"])) {
        $tipoPrenotazione = $_POST["tipo_prenotazione"];
        
        if ($tipoPrenotazione === "noleggio") {
            // Recupera i dati dal form per il noleggio
            $idDrone = $_POST["drone_id"];
            $dataInizio = $_POST["start_date"];
            $dataFine = $_POST["end_date"];
            $note = isset($_POST["notes"]) ? $_POST["notes"] : "";
            
            // Effettua la prenotazione del noleggio
            $risposta = $gestore->prenotaNoleggio($idDrone, $dataInizio, $dataFine, $note);
        } 
        elseif ($tipoPrenotazione === "servizio") {
            // Recupera i dati dal form per il servizio
            $idServizio = $_POST["service_id"];
            $dataServizio = $_POST["service_date"];
            $oraServizio = isset($_POST["service_time"]) ? $_POST["service_time"] : "";
            $note = isset($_POST["notes"]) ? $_POST["notes"] : "";
            
            // Effettua la prenotazione del servizio
            $risposta = $gestore->prenotaServizio($idServizio, $dataServizio, $oraServizio, $note);
        }
        else {
            $risposta = ["successo" => false, "messaggio" => "Tipo di prenotazione non valido."];
        }
    } else {
        $risposta = ["successo" => false, "messaggio" => "Tipo di prenotazione non specificato."];
    }
    
    // Pulisci qualsiasi output in buffer
    ob_clean();
    
    // Imposta l'header Content-Type a application/json
    header('Content-Type: application/json');
    
    // Restituisci la risposta come JSON
    echo json_encode($risposta);
    
    // Chiudi la connessione
    $conn->close();
    exit;
} else {
    // Il metodo di richiesta non è POST
    // Pulisci qualsiasi output in buffer
    ob_clean();
    
    // Imposta l'header Content-Type a application/json
    header('Content-Type: application/json');
    
    $risposta = ["successo" => false, "messaggio" => "Metodo di richiesta non valido."];
    echo json_encode($risposta);
    exit;
}
?>