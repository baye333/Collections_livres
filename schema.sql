-- Base de données : gestion de collection de livres
-- A importer via phpMyAdmin ou : mysql -u root -p < schema.sql

CREATE DATABASE IF NOT EXISTS gestion_livres CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_livres;

-- Table principale des livres
CREATE TABLE IF NOT EXISTS livres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    auteur VARCHAR(255) NOT NULL,
    annee_edition YEAR NULL,
    date_achat DATE NULL,
    genre VARCHAR(100) NULL,
    editeur VARCHAR(150) NULL,
    description TEXT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table des prêts (un livre peut avoir plusieurs prêts au fil du temps)
CREATE TABLE IF NOT EXISTS emprunts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    livre_id INT NOT NULL,
    emprunteur VARCHAR(150) NOT NULL,
    date_pret DATE NOT NULL,
    date_retour_prevue DATE NULL,
    date_retour_effective DATE NULL,
    FOREIGN KEY (livre_id) REFERENCES livres(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Quelques données d'exemple (facultatif)
INSERT INTO livres (titre, auteur, annee_edition, date_achat, genre, editeur) VALUES
('Le Petit Prince', 'Antoine de Saint-Exupéry', 1943, '2020-05-12', 'Conte', 'Gallimard'),
('1984', 'George Orwell', 1949, '2021-01-03', 'Science-fiction', 'Secker & Warburg'),
('Dune', 'Frank Herbert', 1965, '2022-08-20', 'Science-fiction', 'Chilton Books');

INSERT INTO emprunts (livre_id, emprunteur, date_pret, date_retour_prevue) VALUES
(1, 'Marie Diop', '2024-03-01', '2024-04-01');
