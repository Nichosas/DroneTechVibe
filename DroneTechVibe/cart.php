<!DOCTYPE html>
<?php
// Inizializza la sessione
session_start();

// Verifica se l'utente è loggato
if (!isset($_SESSION["accesso"]) || $_SESSION["accesso"] !== true) {
    // L'utente non è loggato, reindirizza alla pagina di login
    header("Location: login.html");
    exit;
}

// Inizializza il carrello nella sessione se non esiste
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DroneTechVibe - Carrello</title>
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
                <li><a href="shop.php">Acquista</a></li>
                <li><a href="rental.php">Noleggia</a></li>
                <li><a href="servizi.php">Servizi</a></li>
                <li><a href="cart.php" class="active"><i class="fas fa-shopping-cart"></i> <span id="cartCounter" class="cart-counter">0</span></a></li>
            </ul>
        </nav>
    </header>

    <section class="page-header">
        <h2>Il Tuo Carrello</h2>
        <p>Rivedi i tuoi prodotti e procedi al checkout</p>
    </section>

    <section class="page-content">
        <div class="cart-container">
            <h3 class="cart-title">Prodotti nel Carrello</h3>
            
            <div id="cartItems" class="cart-items">
                <!-- I prodotti nel carrello verranno caricati dinamicamente da JavaScript -->
                <div class="empty-cart-message" id="emptyCartMessage" style="text-align: center; padding: 20px;">
                    <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                    <p>Il tuo carrello è vuoto</p>
                    <a href="shop.php" class="btn-small">Continua lo Shopping</a>
                </div>
            </div>
            
            <div class="cart-summary" id="cartSummary" style="display: none;">
                <h3 class="summary-title">Riepilogo Ordine</h3>
                <div class="summary-row">
                    <span>Subtotale:</span>
                    <span id="subtotal">€0.00</span>
                </div>
                <div class="summary-row">
                    <span>IVA (22%):</span>
                    <span id="tax">€0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Totale:</span>
                    <span id="total">€0.00</span>
                </div>
                <button id="checkoutBtn" class="btn primary">Procedi al Checkout</button>
                <button id="continueShoppingBtn" class="btn secondary">Continua lo Shopping</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Carica i prodotti dalla sessione PHP
            loadCartItems();
            
            // Aggiungi event listener per il pulsante continua shopping
            document.getElementById('continueShoppingBtn').addEventListener('click', function() {
                window.location.href = 'shop.php';
            });
            
            // Aggiungi event listener per il pulsante checkout
            document.getElementById('checkoutBtn').addEventListener('click', function() {
                // Invia richiesta per il checkout
                fetch('php/checkout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ action: 'checkout' })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // Aggiorna la visualizzazione dopo il checkout
                        loadCartItems();
                    } else {
                        alert('Errore: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Errore durante il checkout:', error);
                    alert('Si è verificato un errore durante il checkout. Riprova più tardi.');
                });
            });
        });
        
        function loadCartItems() {
            // Carica i prodotti dalla sessione PHP
            fetch('php/load_cart.php')
            .then(response => response.json())
            .then(data => {
                const cartItems = data.items || [];
                const cartItemsContainer = document.getElementById('cartItems');
                const emptyCartMessage = document.getElementById('emptyCartMessage');
                const cartSummary = document.getElementById('cartSummary');
                const cartCounter = document.getElementById('cartCounter');
                
                // Aggiorna il contatore del carrello
                const totalItems = cartItems.reduce((total, item) => total + (parseInt(item.quantity) || 0), 0);
                cartCounter.textContent = totalItems;
                
                if (cartItems.length === 0) {
                    emptyCartMessage.style.display = 'block';
                    cartSummary.style.display = 'none';
                    return;
                }
                
                // Nascondi il messaggio di carrello vuoto e mostra il riepilogo
                emptyCartMessage.style.display = 'none';
                cartSummary.style.display = 'block';
                
                // Rimuovi tutti gli elementi esistenti
                while (cartItemsContainer.firstChild) {
                    cartItemsContainer.removeChild(cartItemsContainer.firstChild);
                }
                
                // Aggiungi gli elementi del carrello
                let subtotal = 0;
                
                cartItems.forEach((item, index) => {
                    const cartItemElement = document.createElement('div');
                    cartItemElement.className = 'cart-item';
                    
                    // Verifica che le proprietà esistano prima di usarle
                    const itemName = item.name || 'Prodotto';
                    const itemPrice = parseFloat(item.price) || 0;
                    const itemQuantity = parseInt(item.quantity) || 0;
                    const itemImage = item.image || 'images/placeholder.jpg';
                    const itemId = item.id || 0;
                    
                    const itemTotal = itemPrice * itemQuantity;
                    subtotal += itemTotal;
                    
                    cartItemElement.innerHTML = `
                        <div class="item-image">
                            <img src="${itemImage}" alt="${itemName}">
                        </div>
                        <div class="item-details">
                            <h4>${itemName}</h4>
                            <p class="item-price">€${itemPrice.toFixed(2)}</p>
                            <div class="quantity-control">
                                <button class="quantity-btn minus" data-id="${itemId}">-</button>
                                <span class="quantity">${itemQuantity}</span>
                                <button class="quantity-btn plus" data-id="${itemId}">+</button>
                            </div>
                        </div>
                        <div class="item-total">
                            <p>€${itemTotal.toFixed(2)}</p>
                            <button class="remove-btn" data-id="${itemId}"><i class="fas fa-trash"></i></button>
                        </div>
                    `;
                    
                    cartItemsContainer.appendChild(cartItemElement);
                });
                
                // Aggiungi gli event listener per i pulsanti di quantità e rimozione
                document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
                    btn.addEventListener('click', function() {
                        updateQuantity(this.dataset.id, -1);
                    });
                });
                
                document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
                    btn.addEventListener('click', function() {
                        updateQuantity(this.dataset.id, 1);
                    });
                });
                
                document.querySelectorAll('.remove-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        removeItem(this.dataset.id);
                    });
                });
                
                // Aggiorna il riepilogo
                updateSummary(subtotal);
            })
            .catch(error => {
                console.error('Errore durante il caricamento del carrello:', error);
            });
        }
        
        function updateQuantity(itemId, change) {
            // Invia la richiesta al server per aggiornare la quantità
            fetch('php/load_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    action: 'update',
                    id: itemId,
                    change: change
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aggiorna la visualizzazione del carrello
                    loadCartItems();
                } else {
                    alert('Errore: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Errore durante l\'aggiornamento della quantità:', error);
            });
        }
        
        function removeItem(itemId) {
            // Invia la richiesta al server per rimuovere l'elemento
            fetch('php/load_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    action: 'remove',
                    id: itemId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aggiorna la visualizzazione del carrello
                    loadCartItems();
                } else {
                    alert('Errore: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Errore durante la rimozione dell\'elemento:', error);
            });
        }
        
        function updateSummary(subtotal) {
            // Assicurati che subtotal sia un numero valido
            subtotal = parseFloat(subtotal) || 0;
            
            const tax = subtotal * 0.22;
            const total = subtotal + tax;
            
            // Aggiorna gli elementi del DOM con controllo di sicurezza
            const subtotalElement = document.getElementById('subtotal');
            const taxElement = document.getElementById('tax');
            const totalElement = document.getElementById('total');
            
            if (subtotalElement) subtotalElement.textContent = `€${subtotal.toFixed(2)}`;
            if (taxElement) taxElement.textContent = `€${tax.toFixed(2)}`;
            if (totalElement) totalElement.textContent = `€${total.toFixed(2)}`;
            
            console.log('Riepilogo aggiornato:', { subtotal, tax, total });
        }
    </script>
    <script src="js/script.js"></script>
</body>
</html>