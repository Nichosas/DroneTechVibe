<?php
// Includi il file di configurazione del database
require_once 'config.php';

// Inizializza la sessione
session_start();

// Verifica se l'utente è loggato
if (!isset($_SESSION['accesso']) || $_SESSION['accesso'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

// Inizializza il carrello nella sessione se non esiste
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Gestisci le richieste POST (aggiunta, rimozione, aggiornamento)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        switch ($data['action']) {
            case 'add':
                // Aggiungi un prodotto al carrello
                if (isset($data['product'])) {
                    $product = $data['product'];
                    $productId = $product['id'];
                    
                    $stmt = $conn->prepare("SELECT prezzo FROM Prodotti WHERE id_prodotto = ?");
                    $stmt = $conn->prepare("SELECT nome, prezzo FROM Prodotti WHERE id_prodotto = ?");
                    $stmt->bind_param("i", $productId);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($row = $result->fetch_assoc()) {
                        // Usa il prezzo dal database invece di quello fornito dal client
                        $product['name'] = $row['nome'];
                        $product['price'] = $row['prezzo'];
                    } else {
                        // Prodotto non trovato nel database
                        echo json_encode(['success' => false, 'message' => 'Prodotto non trovato']);
                        exit;
                    }

                    // Verifica se il prodotto è già nel carrello
                    $found = false;
                    foreach ($_SESSION['cart'] as &$item) {
                        if ($item['id'] == $productId) {
                            $item['quantity'] += isset($product['quantity']) ? $product['quantity'] : 1;
                            $found = true;
                            break;
                        }
                    }
                    
                    // Se il prodotto non è nel carrello, aggiungilo
                    if (!$found) {
                        if (!isset($product['quantity'])) {
                            $product['quantity'] = 1;
                        }
                        $_SESSION['cart'][] = $product;
                    }
                    
                    echo json_encode(['success' => true, 'message' => 'Prodotto aggiunto al carrello', 'items' => $_SESSION['cart']]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Dati del prodotto mancanti']);
                }
                break;
                
            case 'update':
                // Aggiorna la quantità di un prodotto nel carrello
                if (isset($data['id']) && isset($data['change'])) {
                    $productId = $data['id'];
                    $change = $data['change'];
                    
                    foreach ($_SESSION['cart'] as $key => &$item) {
                        if ($item['id'] == $productId) {
                            $item['quantity'] += $change;
                            
                            // Rimuovi il prodotto se la quantità è 0 o meno
                            if ($item['quantity'] <= 0) {
                                unset($_SESSION['cart'][$key]);
                                // Reindexing dell'array
                                $_SESSION['cart'] = array_values($_SESSION['cart']);
                            }
                            break;
                        }
                    }
                    
                    echo json_encode(['success' => true, 'message' => 'Carrello aggiornato', 'items' => $_SESSION['cart']]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Dati per l\'aggiornamento mancanti']);
                }
                break;
                
            case 'remove':
                // Rimuovi un prodotto dal carrello
                if (isset($data['id'])) {
                    $productId = $data['id'];
                    
                    foreach ($_SESSION['cart'] as $key => $item) {
                        if ($item['id'] == $productId) {
                            unset($_SESSION['cart'][$key]);
                            // Reindexing dell'array
                            $_SESSION['cart'] = array_values($_SESSION['cart']);
                            break;
                        }
                    }
                    
                    echo json_encode(['success' => true, 'message' => 'Prodotto rimosso dal carrello', 'items' => $_SESSION['cart']]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'ID prodotto mancante']);
                }
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Azione non valida']);
                break;
        }
        exit;
    }
}

// Per le richieste GET, restituisci il contenuto del carrello
echo json_encode(['success' => true, 'items' => $_SESSION['cart']]);
exit;
?>