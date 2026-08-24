<?php
require 'config/db.php';
require 'config/functions.php';

$livre_id = $_GET['livre_id'] ?? $_POST['livre_id'] ?? null;
if (!$livre_id) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM livres WHERE id = :id");
$stmt->execute([':id' => $livre_id]);
$livre = $stmt->fetch();
if (!$livre) { header('Location: index.php'); exit; }

$erreurs = [];

// --- Nouveau prêt ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'nouveau_pret') {
    $emprunteur = trim($_POST['emprunteur'] ?? '');
    $date_pret = $_POST['date_pret'] ?: date('Y-m-d');
    $date_retour_prevue = $_POST['date_retour_prevue'] ?: null;

    if ($emprunteur === '') $erreurs[] = "Le nom de l'emprunteur est obligatoire.";

    if (empty($erreurs)) {
        $stmt = $pdo->prepare("INSERT INTO emprunts (livre_id, emprunteur, date_pret, date_retour_prevue)
                                VALUES (:livre_id, :emprunteur, :date_pret, :date_retour_prevue)");
        $stmt->execute([
            ':livre_id' => $livre_id,
            ':emprunteur' => $emprunteur,
            ':date_pret' => $date_pret,
            ':date_retour_prevue' => $date_retour_prevue,
        ]);
        header("Location: pret.php?livre_id=$livre_id&msg=" . urlencode('Prêt enregistré.'));
        exit;
    }
}

// --- Marquer comme rendu ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'retour') {
    $stmt = $pdo->prepare("UPDATE emprunts SET date_retour_effective = :today WHERE id = :id");
    $stmt->execute([':today' => date('Y-m-d'), ':id' => $_POST['emprunt_id']]);
    header("Location: pret.php?livre_id=$livre_id&msg=" . urlencode('Retour enregistré.'));
    exit;
}

// Prêt en cours
$stmt = $pdo->prepare("SELECT * FROM emprunts WHERE livre_id = :id AND date_retour_effective IS NULL LIMIT 1");
$stmt->execute([':id' => $livre_id]);
$pret_en_cours = $stmt->fetch();

// Historique complet
$stmt = $pdo->prepare("SELECT * FROM emprunts WHERE livre_id = :id ORDER BY date_pret DESC");
$stmt->execute([':id' => $livre_id]);
$historique = $stmt->fetchAll();

require 'includes/header.php';
?>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= e($_GET['msg']) ?></div>
<?php endif; ?>

<div class="card">
    <h2>Prêts — « <?= e($livre['titre']) ?> »</h2>
    <p><a href="index.php" class="btn btn-secondary">&larr; Retour à la collection</a></p>

    <?php if ($pret_en_cours): ?>
        <div class="alert alert-error">
            Actuellement prêté à <strong><?= e($pret_en_cours['emprunteur']) ?></strong>
            depuis le <?= formatDateFr($pret_en_cours['date_pret']) ?>
            <?php if ($pret_en_cours['date_retour_prevue']): ?>
                (retour prévu le <?= formatDateFr($pret_en_cours['date_retour_prevue']) ?>)
            <?php endif; ?>
        </div>
        <form method="post">
            <input type="hidden" name="action" value="retour">
            <input type="hidden" name="livre_id" value="<?= $livre_id ?>">
            <input type="hidden" name="emprunt_id" value="<?= $pret_en_cours['id'] ?>">
            <button type="submit">Marquer comme rendu</button>
        </form>
    <?php else: ?>
        <p>Ce livre n'est actuellement prêté à personne.</p>

        <?php foreach ($erreurs as $err): ?>
            <div class="alert alert-error"><?= e($err) ?></div>
        <?php endforeach; ?>

        <form method="post">
            <input type="hidden" name="action" value="nouveau_pret">
            <input type="hidden" name="livre_id" value="<?= $livre_id ?>">
            <div class="form-grid">
                <div>
                    <label for="emprunteur">Prêté à *</label>
                    <input type="text" id="emprunteur" name="emprunteur" required>
                </div>
                <div>
                    <label for="date_pret">Date du prêt</label>
                    <input type="date" id="date_pret" name="date_pret" value="<?= date('Y-m-d') ?>">
                </div>
                <div>
                    <label for="date_retour_prevue">Retour prévu le</label>
                    <input type="date" id="date_retour_prevue" name="date_retour_prevue">
                </div>
            </div>
            <br>
            <button type="submit">Enregistrer le prêt</button>
        </form>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Historique des prêts</h3>
    <table>
        <thead>
            <tr><th>Emprunteur</th><th>Date du prêt</th><th>Retour prévu</th><th>Retour effectif</th></tr>
        </thead>
        <tbody>
        <?php if (empty($historique)): ?>
            <tr><td colspan="4">Aucun prêt enregistré pour ce livre.</td></tr>
        <?php endif; ?>
        <?php foreach ($historique as $h): ?>
            <tr>
                <td><?= e($h['emprunteur']) ?></td>
                <td><?= formatDateFr($h['date_pret']) ?></td>
                <td><?= formatDateFr($h['date_retour_prevue']) ?></td>
                <td><?= $h['date_retour_effective'] ? formatDateFr($h['date_retour_effective']) : '<span class="badge badge-pret">En cours</span>' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require 'includes/footer.php'; ?>
