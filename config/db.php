<?php
// Connexion à la base de données via PDO (paramètres à adapter si besoin)
$host = 'localhost';
$dbname = 'gestion_livres';
$user = 'root';
$password = ''; // mot de passe par défaut vide sous XAMPP

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données : ' . $e->getMessage());
}
