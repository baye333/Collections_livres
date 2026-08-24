<?php
require 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM livres WHERE id = :id");
    $stmt->execute([':id' => $_POST['id']]);
    header('Location: index.php?msg=' . urlencode('Livre supprimé.'));
    exit;
}

header('Location: index.php');
exit;
