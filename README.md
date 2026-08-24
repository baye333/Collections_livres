# Gestion de collection de livres — Projet DIT1

## Installation avec XAMPP

1. **Copier le dossier** `gestion-livres` dans `htdocs` :
   - Windows : `C:\xampp\htdocs\gestion-livres`
   - Mac : `/Applications/XAMPP/htdocs/gestion-livres`

2. **Démarrer Apache et MySQL** depuis le panneau de contrôle XAMPP.

3. **Créer la base de données** :
   - Aller sur `http://localhost/phpmyadmin`
   - Onglet "Importer" → choisir le fichier `schema.sql` → Exécuter
   - (Cela crée la base `gestion_livres`, les tables `livres` et `emprunts`, avec 3 livres d'exemple)

4. **Vérifier la config** dans `config/db.php` :
   - Par défaut : user `root`, mot de passe vide (config standard XAMPP). Modifier si besoin.

5. **Ouvrir le site** : `http://localhost/gestion-livres/`

## Structure du projet

```
gestion-livres/
├── config/
│   ├── db.php          → connexion PDO à MySQL
│   └── functions.php   → fonctions utilitaires (échappement, format de date)
├── includes/
│   ├── header.php       → en-tête HTML commun
│   └── footer.php       → pied de page commun
├── css/
│   └── style.css
├── index.php            → liste de la collection, recherche & filtres
├── ajouter.php           → formulaire d'ajout d'un livre
├── modifier.php          → formulaire de modification
├── supprimer.php         → traitement de suppression (POST uniquement)
├── pret.php              → gestion des prêts (nouveau prêt, retour, historique)
└── schema.sql            → script de création de la base de données
```

## Fonctionnalités couvertes

- Ajouter / modifier / supprimer un livre (titre, auteur, année d'édition, date d'achat, genre, éditeur, description)
- Affichage de la collection sous forme de tableau
- Suivi des prêts : qui a emprunté quoi, depuis quand, retour prévu, marquer comme rendu, historique complet par livre
- Recherche par titre/auteur, filtre par genre, par tranche d'années (ex. livres sortis entre 2009 et 2012), et par statut (disponible / prêté)

## Pistes d'amélioration (si le temps le permet)

- Système de comptes utilisateurs (chaque utilisateur ne voit que sa propre collection)
- Export de la collection en PDF ou CSV
- Ajout d'une couverture de livre (upload d'image)
- Statistiques (nombre de livres par genre, par décennie...)
- Tri des colonnes du tableau au clic
