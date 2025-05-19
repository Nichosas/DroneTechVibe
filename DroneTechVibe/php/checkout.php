<?php
// Includi il file di configurazione del database
require_once 'config.php';

// Verifica se la richiesta è di tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Inizializza la sessione
    session_start();
    
    // Verifica se l'utente è loggato
    if (!isset($_SESSION['accesso']) || $_SESSION['accesso'] !== true) {
        echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
        exit;
    }
    
    // Verifica se ci sono prodotti nel carrello
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode(['success' => false, 'message' => 'Nessun prodotto nel carrello']);
        exit;
    }
    
    // Ottieni l'ID utente dalla sessione
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default a 1 se non disponibile
    $orderDate = date('Y-m-d H:i:s');
    $totalAmount = 0;
    
    // Calcola l'importo totale
    foreach ($_SESSION['cart'] as $item) {
        $totalAmount += (float)$item['price'] * (int)$item['quantity'];
    }
    
    // Inizia una transazione per garantire l'integrità dei dati
    $conn->begin_transaction();
    
    try {
        // Inserisci ogni prodotto del carrello nella tabella Ordine
        $sql = "INSERT INTO Ordini (id_cliente, id_prodotto, quantita, importo_totale, data_ordine, stato) VALUES (?, ?, ?, ?, ?, 'Completato')";
        $stmt = $conn->prepare($sql);
        
        foreach ($_SESSION['cart'] as $item) {
            $productId = $item['id'];
            $quantity = $item['quantity'];
            $itemTotal = (float)$item['price'] * (int)$quantity;
            
            // Inserisci nella tabella Ordine
            $stmt->bind_param("iiids", $userId, $productId, $quantity, $itemTotal, $orderDate);
            $stmt->execute();
        
            // Decrementa quantità da Fornitura
            $update = $conn->prepare("UPDATE Forniture SET quantita = quantita - ? WHERE id_prodotto = ?");
            $update->bind_param("ii", $quantity, $productId);
            $update->execute();
        }
        
        
        // Commit della transazione
        $conn->commit();
        
        // Svuota il carrello dopo il checkout
        $_SESSION['cart'] = [];
        
        echo json_encode(['success' => true, 'message' => 'Ordine completato con successo!']);
    } catch (Exception $e) {
        // Rollback in caso di errore
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Errore durante il checkout: ' . $e->getMessage()]);
    }
} else {
    // Metodo non consentito
    echo json_encode(['success' => false, 'message' => 'Metodo non consentito']);
}
?> 
