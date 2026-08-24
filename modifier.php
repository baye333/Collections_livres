<?php
require 'config/db.php';
require 'config/functions.php';

$id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$id) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM livres WHERE id = :id");
$stmt->execute([':id' => $id]);
$livre = $stmt->fetch();

if (!$livre) {
    header('Location: index.php?msg=' . urlencode('Livre introuvable.'));
    exit;
}

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
        $stmt = $pdo->prepare("UPDATE livres SET titre=:titre, auteur=:auteur, annee_edition=:annee,
                                date_achat=:date_achat, genre=:genre, editeur=:editeur, description=:description
                                WHERE id=:id");
        $stmt->execute([
            ':titre' => $titre, ':auteur' => $auteur, ':annee' => $annee,
            ':date_achat' => $date_achat, ':genre' => $genre, ':editeur' => $editeur,
            ':description' => $description, ':id' => $id,
        ]);
        header('Location: index.php?msg=' . urlencode('Livre modifié avec succès.'));
        exit;
    }
    // en cas d'erreur, on garde les valeurs saisies à l'affichage
    $livre = array_merge($livre, $_POST);
}

require 'includes/header.php';
?>

<div class="card">
    <h2>Modifier « <?= e($livre['titre']) ?> »</h2>

    <?php foreach ($erreurs as $err): ?>
        <div class="alert alert-error"><?= e($err) ?></div>
    <?php endforeach; ?>

    <form method="post">
        <input type="hidden" name="id" value="<?= $livre['id'] ?>">
        <div class="form-grid">
            <div>
                <label for="titre">Titre *</label>
                <input type="text" id="titre" name="titre" required value="<?= e($livre['titre']) ?>">
            </div>
            <div>
                <label for="auteur">Auteur *</label>
                <input type="text" id="auteur" name="auteur" required value="<?= e($livre['auteur']) ?>">
            </div>
            <div>
                <label for="annee_edition">Année d'édition</label>
                <input type="number" id="annee_edition" name="annee_edition" min="0" max="2100" value="<?= e($livre['annee_edition']) ?>">
            </div>
            <div>
                <label for="date_achat">Date d'achat</label>
                <input type="date" id="date_achat" name="date_achat" value="<?= e($livre['date_achat']) ?>">
            </div>
            <div>
                <label for="genre">Genre</label>
                <input type="text" id="genre" name="genre" value="<?= e($livre['genre']) ?>">
            </div>
            <div>
                <label for="editeur">Éditeur</label>
                <input type="text" id="editeur" name="editeur" value="<?= e($livre['editeur']) ?>">
            </div>
            <div class="full">
                <label for="description">Description / notes</label>
                <textarea id="description" name="description" rows="3"><?= e($livre['description']) ?></textarea>
            </div>
        </div>
        <br>
        <button type="submit">Enregistrer les modifications</button>
        <a href="index.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php require 'includes/footer.php'; ?>
