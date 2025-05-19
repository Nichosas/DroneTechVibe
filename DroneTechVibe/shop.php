<?php
// Inizializza la sessione
session_start();

// Verifica se l'utente è loggato
if (!isset($_SESSION["accesso"]) || $_SESSION["accesso"] !== true) {
    // L'utente non è loggato, reindirizza alla pagina di login
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DroneTechVibe - Acquista Droni e Accessori</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>DroneTechVibe</h1>
        </div>
        <nav>
            <ul>
                
                <li><a href="rental.php">Noleggia</a></li>
                <li><a href="servizi.php">Servizi</a></li>
                <li><a href="shop.php">Acquista</a></li>
                <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> <span id="cartCounter" class="cart-counter">0</span></a></li>
            </ul>
        </nav>
    </header>

    <section class="page-header">
        <h2>Acquista Droni e Accessori</h2>
        <p>Esplora la nostra selezione di droni professionali e accessori di alta qualità</p>
        
    </section>

    <section class="page-content">
        <div class="filter-container" style="margin-bottom: 30px;">
            <div class="filter-group">
                <label for="categoryFilter">Categoria:</label>
                <select id="categoryFilter">
                    <option value="all">Tutte le categorie</option>
                    <option value="Drone">Droni</option>
                    <option value="Accessorio">Accessori</option>
                    <option value="Componente">Componenti</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="priceFilter">Prezzo:</label>
                <select id="priceFilter">
                    <option value="all">Tutti i prezzi</option>
                    <option value="0-100">0€ - 100€</option>
                    <option value="100-500">100€ - 500€</option>
                    <option value="500-1000">500€ - 1000€</option>
                    <option value="1000+">1000€ +</option>
                </select>
            </div>
            <button id="applyFilters" class="btn-small">Applica Filtri</button>
        </div>

        <div class="product-grid" id="productGrid">
            <!-- I prodotti verranno caricati dinamicamente dal database tramite PHP -->
            <?php include 'php/carica_prodotti.php'; ?>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>DroneTechVibe</h3>
                <p>La tua destinazione per droni di alta qualità e servizi professionali.</p>
                <p>Pilotiamo il futuro insieme.</p>
            </div>
            <div class="footer-section">
                <h3>Collegamenti Rapidi</h3>
                <ul>
                    <li><a href="shop.php">Acquista</a></li>
                    <li><a href="rental.php">Noleggia</a></li>
                    <li><a href="servizi.php">Servizi</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contattaci</h3>
                <p>Email: info@dronetechvibe.com</p>
                <p>Telefono: +39 123 456 7890</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 DroneTechVibe. Tutti i diritti riservati.</p>
        </div>
    </footer>

    <script>
        // Script per filtrare i prodotti
        document.addEventListener('DOMContentLoaded', function() {
            const applyFiltersBtn = document.getElementById('applyFilters');
            const productGrid = document.getElementById('productGrid');
            const productCards = document.querySelectorAll('.product-card');
            
            applyFiltersBtn.addEventListener('click', function() {
                const categoryFilter = document.getElementById('categoryFilter').value;
                const priceFilter = document.getElementById('priceFilter').value;
                
                productCards.forEach(card => {
                    const category = card.getAttribute('data-category');
                    const price = parseFloat(card.getAttribute('data-price'));
                    
                    let categoryMatch = categoryFilter === 'all' || category === categoryFilter;
                    let priceMatch = true;
                    
                    if (priceFilter !== 'all') {
                        if (priceFilter === '0-100' && (price < 0 || price > 100)) priceMatch = false;
                        if (priceFilter === '100-500' && (price < 100 || price > 500)) priceMatch = false;
                        if (priceFilter === '500-1000' && (price < 500 || price > 1000)) priceMatch = false;
                        if (priceFilter === '1000+' && price < 1000) priceMatch = false;
                    }
                    
                    if (categoryMatch && priceMatch) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
    <script src="js/script.js"></script>
</body>
</html>