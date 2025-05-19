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
    <title>DroneTechVibe - Noleggio Droni</title>
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
                
                <li><a href="rental.php" class="active">Noleggia</a></li>
                <li><a href="servizi.php">Servizi</a></li>
                <li><a href="shop.php">Acquista</a></li>
            </ul>
        </nav>
    </header>

    <section class="page-header">
        <h2>Noleggio Droni</h2>
        <p>Noleggia i nostri droni professionali per i tuoi progetti</p>
    </section>

    <section class="page-content">
        <div class="rental-container" style="display: flex; flex-wrap: wrap; justify-content: space-between; max-width: 1200px; margin: 0 auto;">
            <!-- Informazioni sul noleggio -->
            <div class="rental-info" style="flex-basis: 100%; margin-bottom: 40px;">
                <h3 class="section-title">Come Funziona il Noleggio</h3>
                <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                    <div style="flex-basis: 30%; text-align: center; padding: 20px;">
                        <i class="fas fa-calendar-alt" style="font-size: 3rem; color: #0066cc; margin-bottom: 15px;"></i>
                        <h4>1. Prenota</h4>
                        <p>Seleziona il drone che desideri noleggiare e le date di inizio e fine noleggio.</p>
                    </div>
                    <div style="flex-basis: 30%; text-align: center; padding: 20px;">
                        <i class="fas fa-drone" style="font-size: 3rem; color: #0066cc; margin-bottom: 15px;"></i>
                        <h4>2. Ritira</h4>
                        <p>Ritira il drone presso il nostro negozio nella data di inizio noleggio.</p>
                    </div>
                    <div style="flex-basis: 30%; text-align: center; padding: 20px;">
                        <i class="fas fa-undo" style="font-size: 3rem; color: #0066cc; margin-bottom: 15px;"></i>
                        <h4>3. Restituisci</h4>
                        <p>Restituisci il drone nella data di fine noleggio nelle stesse condizioni in cui l'hai ricevuto.</p>
                    </div>
                </div>
            </div>

            <!-- Catalogo droni disponibili per il noleggio -->
            <div class="rental-catalog" style="flex-basis: 60%;">
                <h3 class="section-title">Droni Disponibili per il Noleggio</h3>
                <div class="product-grid">
                    <!-- I droni verranno caricati dinamicamente dal database tramite PHP -->
                    <?php include 'php/carica_noleggi.php'; ?>
                </div>
            </div>
            </div>

            <!-- Form di prenotazione -->
            <div class="form-container" style="flex-basis: 35%;">
                <h3 class="form-title">Prenota il Tuo Noleggio</h3>
                <form id="rentalForm" action="php/prenotazione.php" method="post">
                    <input type="hidden" name="tipo_prenotazione" value="noleggio">
                    <div class="form-group">
                        <label for="drone">Drone Selezionato</label>
                        <input type="text" id="droneDisplay" readonly>
                        <input type="hidden" id="drone" name="drone_id" required>
                    </div>
                    <div class="form-group">
                        <label for="startDate">Data di Inizio</label>
                        <input type="date" id="startDate" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="endDate">Data di Fine</label>
                        <input type="date" id="endDate" name="end_date" required>
                    </div>
                    <div class="form-group">
                        <label for="notes">Note Aggiuntive</label>
                        <textarea id="notes" name="notes"></textarea>
                    </div>
                    <div id="rentalSummary" style="margin-bottom: 20px; display: none;">
                        <h4>Riepilogo Noleggio</h4>
                        <p>Drone: <span id="summaryDrone"></span></p>
                        <p>Periodo: <span id="summaryPeriod"></span></p>
                        <p>Totale Stimato: <span id="summaryTotal"></span></p>
                    </div>
                    <button type="submit" class="form-btn">Prenota Ora</button>
                </form>
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
        // Script per la selezione del drone e il calcolo del prezzo
        function selectDrone(droneId, droneName) {
            document.getElementById('drone').value = droneId;
            document.getElementById('droneDisplay').value = droneName;
            document.getElementById('summaryDrone').textContent = droneName;
            updateRentalSummary();
        }
        
        // Aggiungi event listener ai pulsanti 'Prenota Ora' dei droni caricati dinamicamente
        document.addEventListener('DOMContentLoaded', function() {
            // Seleziona tutti i pulsanti con classe 'book-rental'
            const bookButtons = document.querySelectorAll('.book-rental');
            
            // Aggiungi event listener a ciascun pulsante
            bookButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const droneId = this.getAttribute('data-id');
                    const droneName = this.getAttribute('data-name');
                    selectDrone(droneId, droneName);
                    
                    // Scorri fino al form di prenotazione
                    document.getElementById('rentalForm').scrollIntoView({ behavior: 'smooth' });
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('startDate');
            const endDateInput = document.getElementById('endDate');
            
            // Imposta la data minima come oggi
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todayString = `${yyyy}-${mm}-${dd}`;
            
            startDateInput.min = todayString;
            endDateInput.min = todayString;
            
            // Aggiorna il riepilogo quando le date cambiano
            startDateInput.addEventListener('change', updateRentalSummary);
            endDateInput.addEventListener('change', updateRentalSummary);
        });

        function updateRentalSummary() {
            const droneId = document.getElementById('drone').value;
            const startDate = new Date(document.getElementById('startDate').value);
            const endDate = new Date(document.getElementById('endDate').value);
            
            if (droneId && !isNaN(startDate) && !isNaN(endDate) && endDate >= startDate) {
                // Calcola il numero di giorni
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // +1 per includere il giorno di inizio
                
                // Ottieni il prezzo giornaliero dal pulsante che ha selezionato il drone
                let dailyPrice = 0;
                const bookButton = document.querySelector(`.book-rental[data-id="${droneId}"]`);
                if (bookButton) {
                    // Estrai il prezzo dal testo visualizzato nella card del drone
                    const priceElement = bookButton.closest('.rental-card').querySelector('.rental-price');
                    if (priceElement) {
                        // Estrai il numero dal formato "€XX,XX al giorno"
                        const priceText = priceElement.textContent;
                        const priceMatch = priceText.match(/€([0-9]+,[0-9]+)/);
                        if (priceMatch && priceMatch[1]) {
                            // Converti il prezzo da formato italiano (virgola) a formato numerico
                            dailyPrice = parseFloat(priceMatch[1].replace(',', '.'));
                        }
                    }
                }
                
                const totalPrice = dailyPrice * diffDays;
                
                // Aggiorna il riepilogo
                document.getElementById('summaryPeriod').textContent = `${diffDays} giorni (dal ${startDate.toLocaleDateString('it-IT')} al ${endDate.toLocaleDateString('it-IT')})`;
                document.getElementById('summaryTotal').textContent = `€${totalPrice.toFixed(2).replace('.', ',')}`;
                document.getElementById('rentalSummary').style.display = 'block';
            }
        }
    </script>
    <script src="js/script.js"></script>
</body>
</html>