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
    <title>DroneTechVibe - Servizi</title>
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
                <li><a href="servizi.php" class="active">Servizi</a></li>
                <li><a href="shop.php">Acquista</a></li>
            </ul>
        </nav>
    </header>
    
    <section class="page-header">
        <h2>I Nostri Servizi</h2>
        <p>Scopri e prenota i servizi professionali offerti da DroneTechVibe</p>
    </section>
    <section class="about" style="margin-top: 50px;">
        <div class="about-content">
            <h2>Perché Scegliere i Nostri Servizi</h2>
            <p>In DroneTechVibe, ci impegniamo a fornire servizi di alta qualità con personale esperto e attrezzature all'avanguardia. La soddisfazione del cliente è la nostra priorità assoluta.</p>
            <div style="display: flex; flex-wrap: wrap; justify-content: space-between; margin-top: 30px;">
                <div style="flex-basis: 30%; text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-certificate" style="font-size: 2.5rem; color: #0066cc; margin-bottom: 15px;"></i>
                    <h4>Personale Certificato</h4>
                    <p>Tutti i nostri tecnici e istruttori sono certificati e costantemente aggiornati sulle ultime tecnologie.</p>
                </div>
                <div style="flex-basis: 30%; text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-tools" style="font-size: 2.5rem; color: #0066cc; margin-bottom: 15px;"></i>
                    <h4>Attrezzature Professionali</h4>
                    <p>Utilizziamo solo attrezzature e componenti di alta qualità per garantire risultati eccellenti.</p>
                </div>
                <div style="flex-basis: 30%; text-align: center; margin-bottom: 20px;">
                    <i class="fas fa-shield-alt" style="font-size: 2.5rem; color: #0066cc; margin-bottom: 15px;"></i>
                    <h4>Garanzia di Qualità</h4>
                    <p>Offriamo garanzia su tutti i nostri servizi per assicurarti la massima tranquillità.</p>
                </div>
            </div>
        </div>
    </section>
    <section class="page-content">
        <!-- Form di prenotazione servizi -->
        <div class="form-container" style="margin-top: 50px;">
            <h3 class="form-title">Prenota un Servizio</h3>
            <form id="serviceForm" action="php/prenotazione.php" method="post">
                <input type="hidden" name="tipo_prenotazione" value="servizio">
                <div class="form-group">
                    <label for="serviceType">Tipo di Servizio</label>
                    <select id="serviceType" name="service_id" required>
                        <option value="">Seleziona un servizio</option>
                        <option value="1">Corso di Pilotaggio</option>
                        <option value="2">Riparazione</option>
                        <option value="3">Photoset</option>
                        <option value="4">Personalizzazione Drone</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="serviceDate">Data Preferita</label>
                    <input type="date" id="serviceDate" name="service_date" required>
                </div>
                <div class="form-group">
                    <label for="serviceTime">Orario Preferito</label>
                    <select id="serviceTime" name="service_time" required>
                        <option value="">Seleziona un orario</option>
                        <option value="09:00">09:00</option>
                        <option value="10:00">10:00</option>
                        <option value="11:00">11:00</option>
                        <option value="12:00">12:00</option>
                        <option value="14:00">14:00</option>
                        <option value="15:00">15:00</option>
                        <option value="16:00">16:00</option>
                        <option value="17:00">17:00</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="notes">Descrizione / Note</label>
                    <textarea id="notes" name="notes" placeholder="Descrivi le tue esigenze specifiche o fornisci dettagli aggiuntivi sul servizio richiesto"></textarea>
                </div>
                <button type="submit" class="form-btn">Prenota Servizio</button>
            </form>
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
        // Script per la selezione del servizio
        function selectService(serviceType) {
            const serviceTypeSelect = document.getElementById('serviceType');
            for(let i = 0; i < serviceTypeSelect.options.length; i++) {
                if(serviceTypeSelect.options[i].value === serviceType) {
                    serviceTypeSelect.selectedIndex = i;
                    break;
                }
            }
            
            // Scorri fino al form
            document.getElementById('serviceForm').scrollIntoView({ behavior: 'smooth' });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Imposta la data minima come domani
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            const yyyy = tomorrow.getFullYear();
            const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
            const dd = String(tomorrow.getDate()).padStart(2, '0');
            const tomorrowString = `${yyyy}-${mm}-${dd}`;
            
            document.getElementById('serviceDate').min = tomorrowString;
        });
    </script>
    <script src="js/script.js"></script>
</body>
</html>