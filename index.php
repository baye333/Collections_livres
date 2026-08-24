<?php
require 'config/db.php';
require 'config/functions.php';

// --- Construction de la requête de recherche/filtrage ---
$where = [];
$params = [];

if (!empty($_GET['q'])) {
    $where[] = '(l.titre LIKE :q OR l.auteur LIKE :q)';
    $params[':q'] = '%' . $_GET['q'] . '%';
}
if (!empty($_GET['genre'])) {
    $where[] = 'l.genre = :genre';
    $params[':genre'] = $_GET['genre'];
}
if (!empty($_GET['annee_min'])) {
    $where[] = 'l.annee_edition >= :annee_min';
    $params[':annee_min'] = $_GET['annee_min'];
}
if (!empty($_GET['annee_max'])) {
    $where[] = 'l.annee_edition <= :annee_max';
    $params[':annee_max'] = $_GET['annee_max'];
}
if (!empty($_GET['statut'])) {
    if ($_GET['statut'] === 'pret') {
        $where[] = 'e.id IS NOT NULL';
    } elseif ($_GET['statut'] === 'dispo') {
        $where[] = 'e.id IS NULL';
    }
}

$sql = "SELECT l.*, e.id AS emprunt_id, e.emprunteur, e.date_pret, e.date_retour_prevue
        FROM livres l
        LEFT JOIN emprunts e ON e.livre_id = l.id AND e.date_retour_effective IS NULL
        " . (count($where) ? 'WHERE ' . implode(' AND ', $where) : '') . "
        ORDER BY l.titre ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$livres = $stmt->fetchAll();

// Liste des genres existants, pour le filtre
$genres = $pdo->query("SELECT DISTINCT genre FROM livres WHERE genre IS NOT NULL AND genre <> '' ORDER BY genre")->fetchAll(PDO::FETCH_COLUMN);

require 'includes/header.php';
?>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?= e($_GET['msg']) ?></div>
<?php endif; ?>

<div class="card">
    <form method="get" class="filters">
        <div class="field">
            <label for="q">Titre ou auteur</label>
            <input type="text" id="q" name="q" placeholder="Rechercher..." value="<?= e($_GET['q'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="genre">Genre</label>
            <select id="genre" name="genre">
                <option value="">Tous</option>
                <?php foreach ($genres as $g): ?>
                    <option value="<?= e($g) ?>" <?= (($_GET['genre'] ?? '') === $g) ? 'selected' : '' ?>><?= e($g) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="annee_min">Année de</label>
            <input type="number" id="annee_min" name="annee_min" style="width:90px" value="<?= e($_GET['annee_min'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="annee_max">Année à</label>
            <input type="number" id="annee_max" name="annee_max" style="width:90px" value="<?= e($_GET['annee_max'] ?? '') ?>">
        </div>
        <div class="field">
            <label for="statut">Statut</label>
            <select id="statut" name="statut">
                <option value="">Tous</option>
                <option value="dispo" <?= (($_GET['statut'] ?? '') === 'dispo') ? 'selected' : '' ?>>Disponible</option>
                <option value="pret" <?= (($_GET['statut'] ?? '') === 'pret') ? 'selected' : '' ?>>Prêté</option>
            </select>
        </div>
        <div class="field">
            <button type="submit">Filtrer</button>
        </div>
        <?php if (!empty($_GET)): ?>
        <div class="field">
            <a href="index.php" class="btn btn-secondary">Réinitialiser</a>
        </div>
        <?php endif; ?>
    </form>
</div>

<div class="card">
    <p><strong><?= count($livres) ?></strong> livre(s) trouvé(s)</p>
    <table>
        <thead>
            <tr>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Année</th>
                <th>Genre</th>
                <th>Date d'achat</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($livres)): ?>
            <tr><td colspan="7">Aucun livre ne correspond à ces critères.</td></tr>
        <?php endif; ?>
        <?php foreach ($livres as $livre): ?>
            <tr>
                <td><?= e($livre['titre']) ?></td>
                <td><?= e($livre['auteur']) ?></td>
                <td><?= e($livre['annee_edition']) ?></td>
                <td><?= e($livre['genre']) ?></td>
                <td><?= formatDateFr($livre['date_achat']) ?></td>
                <td>
                    <?php if ($livre['emprunt_id']): ?>
                        <span class="badge badge-pret">Prêté à <?= e($livre['emprunteur']) ?></span>
                    <?php else: ?>
                        <span class="badge badge-dispo">Disponible</span>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a href="modifier.php?id=<?= $livre['id'] ?>" class="btn btn-accent">Modifier</a>
                    <a href="pret.php?livre_id=<?= $livre['id'] ?>" class="btn">Prêts</a>
                    <form class="inline" method="post" action="supprimer.php" onsubmit="return confirm('Supprimer ce livre ainsi que son historique de prêts ?');">
                        <input type="hidden" name="id" value="<?= $livre['id'] ?>">
                        <button type="submit" class="btn-danger">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require 'includes/footer.php'; ?>
