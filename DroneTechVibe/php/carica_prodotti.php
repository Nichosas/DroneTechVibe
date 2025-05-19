<?php
// Connessione al database
require_once 'config.php';

// Verifica se la directory delle immagini esiste, altrimenti la crea
$imageDir = "../images";
if (!file_exists($imageDir)) {
    mkdir($imageDir, 0777, true);
}

// Query per selezionare tutti i prodotti
$sql = "SELECT * FROM Prodotti ORDER BY id_prodotto DESC";
$result = $conn->query($sql);

// Verifica se ci sono prodotti
if ($result->num_rows > 0) {
    // Output dei dati di ciascun prodotto
    while($row = $result->fetch_assoc()) {
        // Determina l'immagine in base al tipo di prodotto
        $imagePath = "";
        $defaultImage = "immagini/default-product.jpg"; // Immagine predefinita
        
        
        
        // Verifica se l'immagine esiste, altrimenti usa l'immagine predefinita
        if (!file_exists("../" . $imagePath) && $imagePath != $defaultImage) {
            $imagePath = $defaultImage;
        }
        
        // Crea la card del prodotto
        echo '<div class="product-card" data-category="' . $row['tipo'] . '" data-price="' . $row['prezzo'] . '">';
        echo '    <div class="product-image">';
        echo '        <img src="' . $imagePath . '" alt="' . $row['nome'] . '" onerror="this.src=\'' . $defaultImage . '\'">';
        echo '    </div>';
        echo '    <div class="product-info">';
        echo '        <h3 class="product-title">' . $row['nome'] . '</h3>';
        echo '        <p class="product-description">' . substr($row['descrizione'], 0, 100) . '...</p>';
        echo '        <p class="product-price">€' . number_format($row['prezzo'], 2, ',', '.') . '</p>';
        echo '        <button class="btn-small add-to-cart" data-id="' . $row['id_prodotto'] . '">Aggiungi al Carrello</button>';
        echo '    </div>';
        echo '</div>';
    }
} else {
    echo "<p>Nessun prodotto disponibile al momento.</p>";
}

// Chiudi la connessione
$conn->close();
?>