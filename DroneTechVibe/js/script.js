// Funzioni generali
document.addEventListener('DOMContentLoaded', function() {
    // Inizializzazione del sito
    console.log('DroneTechVibe - Sito caricato');
    
    // Gestione form di login/registrazione
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            
            // Invia i dati al server per l'autenticazione
            loginUser(email, password);
        });
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const nome = document.getElementById('nome').value;
            const cognome = document.getElementById('cognome').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            const telefono = document.getElementById('telefono').value;
            const indirizzo = document.getElementById('indirizzo').value;
            
            // Invia i dati al server per la registrazione
            registerUser(nome, cognome, email, password, telefono, indirizzo);
        });
    }
    
    // Gestione carrello acquisti
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    if (addToCartButtons.length > 0) {
        addToCartButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name');
            const productPrice = this.getAttribute('data-price');
            
            addToCart(productId, productName, productPrice);
            alert('Prodotto aggiunto al carrello!');
        });
    });
    }
    
    // Gestione prenotazione noleggio
    const rentalForm = document.getElementById('rentalForm');
    if (rentalForm) {
        rentalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const droneId = document.getElementById('drone').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            // Invia i dati al server per la prenotazione
            bookRental(droneId, startDate, endDate);
        });
    }
    
    // Gestione prenotazione servizi
    const serviceForm = document.getElementById('serviceForm');
    if (serviceForm) {
        serviceForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const serviceType = document.getElementById('serviceType').value;
            const serviceDate = document.getElementById('serviceDate').value;
            const serviceTime = document.getElementById('serviceTime').value;
            const notes = document.getElementById('notes').value;
            
            // Invia i dati al server per la prenotazione del servizio
            bookService(serviceType, serviceDate, notes, serviceTime);
        });
    }
});

// Funzioni per l'autenticazione
function loginUser(email, password) {
    const formData = new URLSearchParams({ email, password });

    fetch('php/login.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData
    })
    .then(function(res) {
        return res.json();
    })
    .then(function(data) {
        const ok = data.successo || data.success;
        if (ok) {
            window.location.href = 'rental.php';
        } else {
            const msg = data.messaggio || data.message || 'Errore sconosciuto';
            alert('Errore di login: ' + msg);
        }
    })
    .catch(function(err) {
        console.error('Errore:', err);
        alert('Si è verificato un errore durante il login');
    });
}

function registerUser(nome, cognome, email, password, telefono, indirizzo) {
    const formData = new URLSearchParams({ nome, cognome, email, password, telefono, indirizzo });

    fetch('php/register.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: formData
    })
    .then(function(res) {
        return res.json();
    })
    .then(function(data) {
        const ok = data.successo || data.success;
        if (ok) {
            alert('Registrazione completata con successo! Ora puoi accedere.');
            document.getElementById('registerContainer').style.display = 'none';
            document.querySelector('#loginContainer').style.display = 'block';
            document.getElementById('registerForm').reset();
        } else {
            const msg = data.messaggio || data.message || 'Errore sconosciuto';
            alert('Errore di registrazione: ' + msg);
        }
    })
    .catch(function(err) {
        console.error('Errore:', err);
        alert('Si è verificato un errore durante la registrazione');
    });
}

// Funzioni per il carrello
function addToCart(productId, productName, productPrice) {
    // Invia la richiesta al server per aggiungere il prodotto al carrello nella sessione
    fetch('php/load_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'add',
            product: {
                id: productId,
                name: productName,
                price: productPrice,
                quantity: 1,
                image: 'images/placeholder.jpg' // Immagine predefinita
            }
        })
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            // Aggiorna il contatore del carrello
            updateCartCounter();
        } else {
            alert('Errore: ' + data.message);
        }
    })
    .catch(function(error) {
        console.error('Errore durante l\'aggiunta al carrello:', error);
    });
}

function updateCartCounter() {
    // Ottieni il numero di prodotti nel carrello dalla sessione
    fetch('php/load_cart.php')
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        const cart = data.items || [];
        const cartCounter = document.getElementById('cartCounter');
        
        if (cartCounter) {
            // Calcola il numero totale di prodotti nel carrello
            let totalItems = 0;
            for (let i = 0; i < cart.length; i++) {
                totalItems += parseInt(cart[i].quantity || 0);
            }
            cartCounter.textContent = totalItems;
            
            if (totalItems > 0) {
                cartCounter.style.display = 'inline-block';
            } else {
                cartCounter.style.display = 'none';
            }
        }
    })
    .catch(function(error) {
        console.error('Errore durante il caricamento del contatore del carrello:', error);
    });
}

// Funzioni per il noleggio
function bookRental(droneId, startDate, endDate) {
    // Creazione di una richiesta AJAX per la prenotazione del noleggio
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'php/prenotazione.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if (this.status === 200) {
            try {
                const response = JSON.parse(this.responseText);
                if (response.successo) {
                    alert('Noleggio prenotato con successo!');
                    // Reindirizza o aggiorna la pagina
                    window.location.reload();
                } else {
                    alert('Errore nella prenotazione: ' + (response.messaggio || 'Errore sconosciuto'));
                }
            } catch (e) {
                console.error('Errore nel parsing della risposta:', e);
                alert('Errore durante la prenotazione: La risposta del server non è valida');
            }
        } else {
            alert('Errore durante la prenotazione: ' + this.status);
        }
    };
    
    xhr.send(`tipo_prenotazione=noleggio&drone_id=${encodeURIComponent(droneId)}&start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`);
}

// Funzioni per i servizi
function bookService(serviceType, serviceDate, notes, serviceTime) {
    // Creazione di una richiesta AJAX per la prenotazione del servizio
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'php/prenotazione.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    // Utilizziamo il serviceTime passato come parametro
    
    xhr.onload = function() {
        if (this.status === 200) {
            try {
                const response = JSON.parse(this.responseText);
                if (response.successo) {
                    alert('Servizio prenotato con successo!');
                    // Reindirizza o aggiorna la pagina
                    window.location.reload();
                } else {
                    alert('Errore nella prenotazione: ' + (response.messaggio || 'Errore sconosciuto'));
                }
            } catch (e) {
                console.error('Errore nel parsing della risposta:', e);
                alert('Errore durante la prenotazione: La risposta del server non è valida');
            }
        } else {
            alert('Errore durante la prenotazione: ' + this.status);
        }
    };
    
    xhr.send(`tipo_prenotazione=servizio&service_id=${encodeURIComponent(serviceType)}&service_date=${encodeURIComponent(serviceDate)}&service_time=${encodeURIComponent(serviceTime)}&notes=${encodeURIComponent(notes)}`);
}

// Inizializza il contatore del carrello quando la pagina si carica
document.addEventListener('DOMContentLoaded', function() {
    updateCartCounter();
    
    // Verifica se siamo nella pagina del carrello e carica gli elementi
    const cartItemsContainer = document.getElementById('cartItems');
    if (cartItemsContainer) {
        // Se siamo nella pagina del carrello, carica gli elementi
        if (typeof loadCartItems === 'function') {
            loadCartItems();
        } else {
            // Se la funzione loadCartItems non è definita, implementala qui
            const cartSummary = document.getElementById('cartSummary');
            const emptyCartMessage = document.getElementById('emptyCartMessage');
            
            // Recupera il carrello dal localStorage
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Se il carrello è vuoto, mostra il messaggio
            if (cart.length === 0) {
                cartItemsContainer.innerHTML = '';
                if (emptyCartMessage) {
                    cartItemsContainer.appendChild(emptyCartMessage);
                    emptyCartMessage.style.display = 'block';
                }
                if (cartSummary) cartSummary.style.display = 'none';
                return;
            }
            
            // Nascondi il messaggio di carrello vuoto
            if (emptyCartMessage) emptyCartMessage.style.display = 'none';
            
            // Svuota il contenitore degli elementi del carrello
            cartItemsContainer.innerHTML = '';
            
            // Variabili per il calcolo del totale
            let subtotal = 0;
            
            // Aggiungi ogni prodotto al carrello
            cart.forEach(item => {
                const itemTotal = item.price * item.quantity;
                subtotal += itemTotal;
                
                const cartItem = document.createElement('div');
                cartItem.className = 'cart-item';
                cartItem.innerHTML = `
                    <div class="cart-item-image">
                        <img src="images/${item.id <= 3 ? 'drone' + item.id : 'accessory' + (item.id - 3)}.jpg" alt="${item.name}">
                    </div>
                    <div class="cart-item-details">
                        <h3 class="cart-item-title">${item.name}</h3>
                        <p class="cart-item-price">€${parseFloat(item.price).toFixed(2)}</p>
                    </div>
                    <div class="cart-item-quantity">
                        <button class="decrease-quantity" data-id="${item.id}">-</button>
                        <span>${item.quantity}</span>
                        <button class="increase-quantity" data-id="${item.id}">+</button>
                    </div>
                    <div class="cart-item-total">
                        €${itemTotal.toFixed(2)}
                    </div>
                    <div class="cart-item-remove" data-id="${item.id}">
                        <i class="fas fa-trash"></i>
                    </div>
                `;
                
                cartItemsContainer.appendChild(cartItem);
            });
            
            // Aggiungi gli event listener per i pulsanti di quantità e rimozione
            document.querySelectorAll('.decrease-quantity').forEach(button => {
                button.addEventListener('click', function() {
                    updateItemQuantity(this.getAttribute('data-id'), -1);
                });
            });
            
            document.querySelectorAll('.increase-quantity').forEach(button => {
                button.addEventListener('click', function() {
                    updateItemQuantity(this.getAttribute('data-id'), 1);
                });
            });
            
            document.querySelectorAll('.cart-item-remove').forEach(button => {
                button.addEventListener('click', function() {
                    removeItem(this.getAttribute('data-id'));
                });
            });
            
            // Calcola e mostra il riepilogo del carrello
            if (cartSummary) {
                const tax = subtotal * 0.22;
                const total = subtotal + tax;
                
                const subtotalElement = document.getElementById('subtotal');
                const taxElement = document.getElementById('tax');
                const totalElement = document.getElementById('total');
                
                if (subtotalElement) subtotalElement.textContent = `€${subtotal.toFixed(2)}`;
                if (taxElement) taxElement.textContent = `€${tax.toFixed(2)}`;
                if (totalElement) totalElement.textContent = `€${total.toFixed(2)}`;
                
                // Mostra il riepilogo del carrello
                cartSummary.style.display = 'block';
            }
        }
    }
});

// Funzione per aggiornare la quantità di un prodotto nel carrello
function updateItemQuantity(itemId, change) {
    // Recupera il carrello dal localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Trova l'elemento nel carrello
    const itemIndex = cart.findIndex(item => item.id === itemId);
    
    if (itemIndex !== -1) {
        // Aggiorna la quantità
        cart[itemIndex].quantity += change;
        
        // Se la quantità è 0 o meno, rimuovi l'elemento dal carrello
        if (cart[itemIndex].quantity <= 0) {
            cart.splice(itemIndex, 1);
        }
        
        // Salva il carrello aggiornato nel localStorage
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Aggiorna la visualizzazione se siamo nella pagina del carrello
        const cartItemsContainer = document.getElementById('cartItems');
        if (cartItemsContainer && typeof loadCartItems === 'function') {
            loadCartItems();
        } else if (cartItemsContainer) {
            // Ricarica la pagina per aggiornare la visualizzazione
            window.location.reload();
        }
        
        // Aggiorna il contatore del carrello nell'header
        updateCartCounter();
    }
}

// Funzione per rimuovere un prodotto dal carrello
function removeItem(itemId) {
    // Recupera il carrello dal localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    
    // Rimuovi l'elemento dal carrello
    cart = cart.filter(item => item.id !== itemId);
    
    // Salva il carrello aggiornato nel localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Aggiorna la visualizzazione se siamo nella pagina del carrello
    const cartItemsContainer = document.getElementById('cartItems');
    if (cartItemsContainer && typeof loadCartItems === 'function') {
        loadCartItems();
    } else if (cartItemsContainer) {
        // Ricarica la pagina per aggiornare la visualizzazione
        window.location.reload();
    }
    
    // Aggiorna il contatore del carrello nell'header
    updateCartCounter();
}