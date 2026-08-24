<?php
require 'config/db.php';
require 'config/functions.php';

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $auteur = trim($_POST['auteur'] ?? '');
    $annee = $_POST['annee_edition'] ?: null;
    $date_achat = $_POST['date_achat'] ?: null;
    $genre = trim($_POST['genre'] ?? '') ?: null;
    $editeur = trim($_POST['editeur'] ?? '') ?: null;
    $description = trim($_POST['description'] ?? '') ?: null;

    if ($titre === '') $erreurs[] = "Le titre est obligatoire.";
    if ($auteur === '') $erreurs[] = "L'auteur est obligatoire.";
    if ($annee !== null && !preg_match('/^\d{4}$/', $annee)) $erreurs[] = "L'année d'édition doit être une année sur 4 chiffres.";

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("INSERT INTO livres (titre, auteur, annee_edition, date_achat, genre, editeur, description)
                                VALUES (:titre, :auteur, :annee, :date_achat, :genre, :editeur, :description)");
        $stmt->execute([
            ':titre' => $titre,
            ':auteur' => $auteur,
            ':annee' => $annee,
            ':date_achat' => $date_achat,
            ':genre' => $genre,
            ':editeur' => $editeur,
            ':description' => $description,
        ]);
        header('Location: index.php?msg=' . urlencode('Livre ajouté avec succès.'));
        exit;
    }
}

require 'includes/header.php';
?>

<div class="card">
    <h2>Ajouter un livre</h2>

    <?php foreach ($erreurs as $err): ?>
        <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post">
        <div class="form-grid">
            <div>
                <label for="titre">Titre *</label>
                <input type="text" id="titre" name="titre" required value="<?= e($_POST['titre'] ?? '') ?>">
            </div>
            <div>
                <label for="auteur">Auteur *</label>
                <input type="text" id="auteur" name="auteur" required value="<?= e($_POST['auteur'] ?? '') ?>">
            </div>
            <div>
                <label for="annee_edition">Année d'édition</label>
                <input type="number" id="annee_edition" name="annee_edition" min="0" max="2100" value="<?= e($_POST['annee_edition'] ?? '') ?>">
            </div>
            <div>
                <label for="date_achat">Date d'achat</label>
                <input type="date" id="date_achat" name="date_achat" value="<?= e($_POST['date_achat'] ?? '') ?>">
            </div>
            <div>
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" value="<?= e($_POST['genre'] ?? '') ?>">
            </div>
            <div>
                <label for="editeur">Éditeur</label>
                <input type="text" id="editeur" name="editeur" value="<?= e($_POST['editeur'] ?? '') ?>">
            </div>
            <div class="full">
                <label for="description">Description / notes</label>
                <textarea id="description" name="description" rows="3"><?= e($_POST['description'] ?? '') ?></textarea>
            </div>
        </div>
        <br>
        <button type="submit">Ajouter le livre</button>
        <a href="index.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php require 'includes/footer.php'; ?>
