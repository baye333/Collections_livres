<?php
// Petites fonctions utilitaires

function e($value) {
    // Echappe une valeur pour un affichage HTML sûr
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function formatDateFr($date) {
    if (empty($date)) return '—';
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d ? $d->format('d/m/Y') : $date;
}
