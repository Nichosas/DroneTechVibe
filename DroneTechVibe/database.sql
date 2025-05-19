-- Creazione del database
CREATE DATABASE IF NOT EXISTS dronetechvibe;
USE dronetechvibe;

-- CLIENTE
CREATE TABLE Clienti (
    id_cliente INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50),
    cognome VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(100),
    telefono VARCHAR(20),
    indirizzo VARCHAR(200)
);

-- MAGAZZINO
CREATE TABLE Magazzini (
    id_magazzino INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    indirizzo VARCHAR(200),
    capacita_massima INT
);

-- PRODOTTO
CREATE TABLE Prodotti (
    id_prodotto INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    descrizione TEXT,
    tipo ENUM('Drone', 'Accessorio', 'Componente'),
    prezzo DECIMAL(10,2),
    id_magazzino INT,
    FOREIGN KEY (id_magazzino) REFERENCES Magazzini(id_magazzino)
);

-- FORNITORE
CREATE TABLE Fornitori (
    id_fornitore INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100),
    email VARCHAR(100),
    telefono VARCHAR(20),
    indirizzo VARCHAR(255)
);

-- FORNITURA (relazione N:M tra Fornitore e Prodotto)
CREATE TABLE Forniture (
    id_fornitore INT,
    id_prodotto INT,
    quantita INT,
    PRIMARY KEY (id_fornitore, id_prodotto),
    FOREIGN KEY (id_fornitore) REFERENCES Fornitori(id_fornitore),
    FOREIGN KEY (id_prodotto) REFERENCES Prodotti(id_prodotto)
);

-- DIPENDENTE
CREATE TABLE Dipendenti (
    id_dipendente INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50),
    cognome VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    telefono VARCHAR(20),
    posizione ENUM('Istruttore', 'Tecnico', 'Magazziniere', 'Amministrativo')
);

-- SERVIZIO
CREATE TABLE Servizi (
    id_servizio INT PRIMARY KEY AUTO_INCREMENT,
    tipo_servizio ENUM('Corso Pilotaggio', 'Riparazione', 'Photoset', 'Personalizzazione'),
    descrizione TEXT,
    prezzo DECIMAL(10,2),
    durata INT,
    id_dipendente INT,
    FOREIGN KEY (id_dipendente) REFERENCES Dipendenti(id_dipendente)
);

-- PRENOTAZIONE SERVIZIO
CREATE TABLE Prenotazione_Servizi (
    id_prenotazione INT PRIMARY KEY AUTO_INCREMENT,
    data_prenotazione DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_inizio DATE,
    data_fine DATE,
    ora VARCHAR(10),
    stato ENUM('Confermata', 'In attesa', 'Completata', 'Annullata') DEFAULT 'In attesa',
    note TEXT,
    id_cliente INT,
    id_servizio INT NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES Clienti(id_cliente)
    FOREIGN KEY (id_cliente) REFERENCES Clienti(id_cliente)
);

-- NOLEGGIO
CREATE TABLE Noleggi (
    id_noleggio INT PRIMARY KEY AUTO_INCREMENT,
    data_prenotazione DATETIME DEFAULT CURRENT_TIMESTAMP,
    data_inizio DATE,
    data_fine DATE,
    stato ENUM('Confermata', 'In attesa', 'Completata', 'Annullata') DEFAULT 'In attesa',
    note TEXT,
    id_cliente INT,
    id_prodotto INT NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES Clienti(id_cliente),
    FOREIGN KEY (id_prodotto) REFERENCES Prodotti(id_prodotto)
);

CREATE TABLE Ordini (
    id_ordine INT PRIMARY KEY AUTO_INCREMENT,
    data_ordine DATETIME NOT NULL,
    importo_totale DECIMAL(10,2) NOT NULL,
    id_cliente INT NOT NULL,
    id_prodotto INT NOT NULL,
    quantita INT NOT NULL,
    stato VARCHAR(50) NOT NULL,
    FOREIGN KEY (id_cliente) REFERENCES Clienti(id_cliente),
    FOREIGN KEY (id_prodotto) REFERENCES Prodotti(id_prodotto)
);
-- Inserimento di dati di esempio per il Magazzino
INSERT INTO Magazzini (nome, indirizzo, capacita_massima) VALUES
('Magazzino Centrale', 'Via Roma 123, Milano', 1000);

-- Inserimento di dati di esempio per i Dipendenti
INSERT INTO Dipendenti (nome, cognome, email, telefono, posizione) VALUES
('Mario', 'Rossi', 'mario.rossi@dronetechvibe.com', '3331234567', 'Istruttore'),
('Laura', 'Bianchi', 'laura.bianchi@dronetechvibe.com', '3339876543', 'Tecnico'),
('Giovanni', 'Verdi', 'giovanni.verdi@dronetechvibe.com', '3335678901', 'Magazziniere'),
('Francesca', 'Neri', 'francesca.neri@dronetechvibe.com', '3332345678', 'Amministrativo');

-- Inserimento di dati di esempio per i Fornitori
INSERT INTO Fornitori (nome, email, telefono, indirizzo) VALUES
('DroneTech SpA', 'info@dronetech.com', '0212345678', 'Via Industria 10, Milano'),
('ComponentiDrone Srl', 'ordini@componentidrone.it', '0387654321', 'Via Elettronica 25, Roma'),
('AccessoriTech', 'vendite@accessoritech.com', '0456789012', 'Via Commercio 5, Torino');

-- Inserimento di dati di esempio per i Prodotti (Droni)
INSERT INTO Prodotti (nome, descrizione, tipo, prezzo, id_magazzino) VALUES
('DJI Mavic Air 2', 'Drone professionale con fotocamera 4K, autonomia di 34 minuti e sensori di ostacoli omnidirezionali.', 'Drone', 849.99, 1),
('DJI Mini 3 Pro', 'Drone ultraleggero sotto i 249g con fotocamera 4K, ideale per principianti e viaggiatori.', 'Drone', 759.99, 1),
('Autel Robotics EVO II', 'Drone professionale con fotocamera 8K, autonomia di 40 minuti e rilevamento ostacoli a 360°.', 'Drone', 1499.99, 1),
('Parrot Anafi', 'Drone compatto con fotocamera 4K HDR e zoom digitale 3x, perfetto per riprese creative.', 'Drone', 699.99, 1),
('Skydio 2', 'Drone autonomo con tecnologia di tracciamento avanzata e evitamento ostacoli AI.', 'Drone', 999.99, 1);


INSERT INTO Forniture (id_fornitore, id_prodotto, quantita) VALUES
(1, 1, 7),  -- DJI Mavic Air 2
(1, 2, 9),  -- DJI Mini 3 Pro
(2, 3, 6),  -- Autel Robotics EVO II
(2, 4, 8),  -- Parrot Anafi
(3, 5, 5);  -- Skydio 2


-- Inserimento di dati di esempio per i Servizi
INSERT INTO Servizi (tipo_servizio, descrizione, prezzo, durata, id_dipendente) VALUES
('Corso Pilotaggio', 'Corso base di pilotaggio droni con certificazione ENAC, 10 ore di teoria e 5 ore di pratica.', 299.99, 15, 1),
('Riparazione', 'Servizio di riparazione e manutenzione per tutti i tipi di droni con diagnosi completa.', 149.99, 5, 2),
('Photoset', 'Servizio professionale di riprese aeree per eventi, matrimoni e pubblicità con editing incluso.', 499.99, 8, 1),
('Personalizzazione', 'Personalizzazione hardware e software del drone secondo le esigenze specifiche del cliente.', 249.99, 10, 2);


-- Inserimento di dati di esempio per le Prenotazioni
INSERT INTO Prenotazione_Servizi (data_inizio, data_fine, ora, note, id_cliente, id_servizio, stato) VALUES
('2023-12-15', '2023-12-15', '10:00', 'Prima lezione di pilotaggio', 1, 1, 'Confermata'),
('2023-12-20', '2023-12-20', '14:30', 'Riparazione drone DJI Mini 3', 1, 2, 'In attesa');

INSERT INTO Noleggi (data_inizio, data_fine, note, id_cliente, id_prodotto, stato) VALUES
('2023-12-25', '2023-12-28', 'Noleggio per vacanze natalizie', 1, 1, 'Confermata');