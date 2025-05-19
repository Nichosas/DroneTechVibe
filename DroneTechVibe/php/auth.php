<?php
session_start();

// Classe per la gestione dell'autenticazione
class Autenticazione {
    // Funzione per verificare se l'utente è autenticato
    public static function utenteAutenticato() {
        return isset($_SESSION['accesso']) && $_SESSION['accesso'] === true;
    }
    
    // Funzione per proteggere le pagine
    public static function proteggiPagina() {
        // Lista delle pagine pubbliche
        $paginePubbliche = ['index.html', 'login.html'];
        
        // Ottieni il nome della pagina corrente
        $paginaCorrente = basename($_SERVER['REQUEST_URI']);
        if (empty($paginaCorrente)) {
            $paginaCorrente = 'index.html';
        }
        
        // Se l'utente non è autenticato, permetti l'accesso solo alle pagine pubbliche
        if (!self::utenteAutenticato()) {
            if (!in_array($paginaCorrente, $paginePubbliche)) {
                header('Location: /login.html');
                exit();
            }
        }
    }
    
    // Funzione per richiedere l'accesso
    public static function richiediAccesso() {
        if (!self::utenteAutenticato()) {
            header('Location: /login.html');
            exit();
        }
    }
}

// Esegui il controllo di protezione
Autenticazione::proteggiPagina();
?>