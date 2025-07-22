# 🚖 Application de Réservation de Taxi — Laravel 9

Ce projet est une plateforme de gestion de réservations de taxis avec plusieurs rôles utilisateurs, notifications, QR codes, dashboards dynamiques, et gestion des affectations.

---

## 🛠️ Installation du projet

### 1. Cloner le dépôt ou extraire le `.zip`

Assurez-vous d’avoir **PHP ≥ 8.1**, **Composer**, **Node.js ≥ 16**, **MySQL** et **npm** installés.

```bash
composer install
npm install && npm run build
```

### 2. Créer et configurer le fichier .env

```bash
cp .env.example .env
```

Configurez vos informations de base de données et de mail dans le fichier .env :

```ini
DB_DATABASE=nom_de_la_base
DB_USERNAME=utilisateur
DB_PASSWORD=motdepasse

MAIL_USERNAME=...
MAIL_PASSWORD=...
```

### 3. Générer la clé de l’application

```bash
php artisan key:generate
```

### 4. Migrer et remplir la base de données

```bash
php artisan migrate --seed
```

## Lancement de l'application

```bash
php artisan serve
```

L'application sera disponible sur : http://127.0.0.1:8000

## Commande planifiée importante

L'application utilise une commande Laravel planifiée pour élargir automatiquement la recherche de chauffeurs après un certain temps si aucune candidature n’est reçue.

Important : Cette commande doit être exécutée en arrière-plan en permanence :

```bash
php artisan schedule:work
```

## Notifications Email (Mailtrap)

Les emails sont simulés grâce à Mailtrap.io pour les tests de développement.

Renseignez vos identifiants Mailtrap dans le fichier .env

Notifications gérées :

Nouvelle course disponible

Nouvelle candidature d’un chauffeur

Acceptation de candidature par un client

Annulation de course ou désistement du chauffeur

## Rôles utilisateurs

Super Admin :Gestion globale, statistiques, utilisateurs, agences
Admin d’agence :Gestion des chauffeurs, taxis, réservations, statistiques locales
Chauffeur :Voir les réservations disponibles, postuler, suivre l’historique
Client :Réserver un taxi, suivre ses courses, modifier son profil

## Fonctionnalités principales

Formulaire de réservation avec filtres dynamiques (ville, date, statut)

Affectation manuelle des chauffeurs

Notifications automatiques par email

Génération de QR code pour chaque réservation

Dashboards interactifs avec statistiques animées

Interface responsive (ordinateur, tablette, mobile)

Système de rôles avec gestion personnalisée des accès

Back-office pour les agences partenaires
