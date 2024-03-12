# Documentation du Projet Evento

## Contexte du Projet

La société "Evento" ambitionne de développer une plateforme novatrice dédiée à la gestion et à la réservation des places d'événements. L'objectif est de fournir une expérience utilisateur optimale aux participants, organisateurs et administrateurs. Cette plateforme permettra aux utilisateurs de découvrir, réserver et générer des tickets pour une variété d'événements, tandis que les organisateurs auront la possibilité de créer et de gérer leurs propres événements.

## Fonctionnalités Requises

### Utilisateur

- En tant qu'utilisateur, je veux pouvoir m'inscrire sur la plateforme en fournissant mon nom, mon adresse e-mail et un mot de passe.
- En tant qu'utilisateur, je veux avoir la possibilité de me connecter à mon compte en utilisant mes identifiants.
- En tant qu'utilisateur, je veux pouvoir réinitialiser mon mot de passe en cas d'oubli, en recevant un e-mail de réinitialisation.
- En tant qu'utilisateur, je veux pouvoir consulter la liste des événements disponibles sur la plateforme avec pagination pour faciliter la navigation.
- En tant qu'utilisateur, je veux pouvoir filtrer les événements par catégorie.
- En tant qu'utilisateur, je veux pouvoir rechercher des événements par titre.
- En tant qu'utilisateur, je veux pouvoir visualiser les détails d'un événement, y compris sa description, sa date, son lieu et le nombre de places disponibles.
- En tant qu'utilisateur, je veux pouvoir réserver une place pour un événement.
- En tant qu'utilisateur, je veux pouvoir générer un ticket une fois ma réservation confirmée.

### Organisateur

- En tant qu'organisateur, je veux pouvoir créer un nouvel événement en spécifiant son titre, sa description, sa date, son lieu, sa catégorie et le nombre de places disponibles.
- En tant qu'organisateur, je veux pouvoir gérer mes événements.
- En tant qu'organisateur, je veux avoir accès à des statistiques sur les réservations de mes événements.
- En tant qu'organisateur, je veux avoir la possibilité de choisir entre une acceptation automatique des réservations ou une validation manuelle.

### Administrateur

- En tant qu'administrateur, je veux pouvoir gérer les utilisateurs en restreignant leur accès.
- En tant qu'administrateur, je veux pouvoir gérer les catégories d'événements en ajoutant, modifiant ou supprimant des catégories.
- En tant qu'administrateur, je veux pouvoir valider les événements créés par les organisateurs avant leur publication sur la plateforme.
- En tant qu'administrateur, je veux avoir accès à des statistiques.

## Technologies Utilisées

- Laravel
- PostgreSQL
- SASS
- JavaScript

## Procédure d'Installation

1. Cloner le dépôt GitHub.
2. Installer les dépendances en exécutant `composer install`.
3. Configurer le fichier `.env` avec les informations de la base de données PostgreSQL.
4. Exécuter les migrations avec la commande `php artisan migrate`.
5. Exécuter les seeds pour peupler la base de données avec des données d'exemple (`php artisan db:seed`).

## Structure du Projet

- `app/` : Contient les modèles, les contrôleurs et d'autres classes de l'application.
- `database/migrations/` : Contient les fichiers de migration de la base de données.
- `resources/views/` : Contient les fichiers de vue Blade.
- `routes/` : Contient les définitions des routes de l'application.
- `public/` : Contient les ressources publiques telles que les fichiers CSS, JavaScript et les images.

## Contribution

Les contributions sont les bienvenues ! N'hésitez pas à soumettre des pull requests pour améliorer le projet.

## Licence

Ce projet est sous licence MIT. Voir le fichier LICENSE pour plus de détails.

