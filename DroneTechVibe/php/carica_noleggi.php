<?php
// Connessione al database
require_once 'config.php';

// Query per selezionare solo i droni disponibili per il noleggio (non assegnati a clienti)
// Escludiamo anche i droni che sono attualmente in noleggio
$sql = "SELECT p.* FROM Prodotti p 
       WHERE p.tipo = 'Drone' 
       AND NOT EXISTS (
           SELECT 1 FROM Noleggi n 
           WHERE n.id_prodotto = p.id_prodotto 
           AND n.stato IN ('Confermata', 'In attesa') 
           AND CURDATE() BETWEEN n.data_inizio AND n.data_fine
       )
       ORDER BY p.id_prodotto DESC";
$result = $conn->query($sql);

// Verifica se ci sono droni disponibili
if ($result->num_rows > 0) {
    // Output dei dati di ciascun drone
    while($row = $result->fetch_assoc()) {
        // Determina l'immagine casuale per il drone
        $imagePath = "immagini/default-product.jpg";
        
        // Calcola il prezzo di noleggio giornaliero (30% del prezzo di vendita)
        $rentalPrice = $row['prezzo'] * 0.1;
        
        // Crea la card del drone per il noleggio
        echo '<div class="product-card">';
        echo '    <div class="product-image">';
        echo '        <img src="' . $imagePath . '" alt="' . $row['nome'] . '">';
        echo '    </div>';
        echo '    <div class="rental-info">';
        echo '        <h3 class="rental-title">' . $row['nome'] . '</h3>';
        echo '        <p class="rental-description">' . substr($row['descrizione'], 0, 100) . '...</p>';
        echo '        <p class="rental-price">€' . number_format($rentalPrice, 2, ',', '.') . ' al giorno</p>';
        echo '        <button class="btn-small book-rental" data-id="' . $row['id_prodotto'] . '" data-name="' . $row['nome'] . '">Prenota Ora</button>';
        echo '    </div>';
        echo '</div>';
    }
} else {
    echo "<p>Nessun drone disponibile per il noleggio al momento.</p>";
}

// Chiudi la connessione
$conn->close();
?>