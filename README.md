# Plate-forme de gestion des offres

Projet de stage ESI réalisé chez Djezzy par **Bourguig Leila Sérine** et **Chelmoun Asmaa**, groupe **SIT3**, option **ST**.

Application Laravel avec deux espaces : client/revendeur et commercial. Elle couvre les offres et caractéristiques, les disponibilités par wilaya, les boutiques et justificatifs, les commandes multi-offres, les réclamations, les notifications et la validation des comptes clients.

## Prérequis

- PHP **8.5.4 ou ultérieur dans la branche 8.5**, Composer 2 ; extensions Laravel habituelles, PDO MySQL, Fileinfo et Mbstring.
- MySQL 8.4, avec une base vide et un utilisateur autorisé à créer les tables.
- Node.js 22.12+ ou 24+ et npm si vous compilez les ressources Vite.
- Un service d'e-mail fonctionnel pour tester la vérification des comptes.

## Installation locale

1. Extraire le projet puis ouvrir un terminal à sa racine.
2. Installer les dépendances PHP :

   ```bash
   composer install
   ```

3. Copier `.env.example` vers `.env` :

   ```powershell
   Copy-Item .env.example .env
   ```

   Sous Linux/macOS : `cp .env.example .env`.
4. Dans `.env`, renseigner `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` et `DB_PASSWORD`. Créer cette base MySQL avant les migrations. Le projet ne contient pas d'export de la base locale.
5. Exécuter :

   ```bash
   php artisan key:generate
   php artisan migrate
   php artisan app:create-commercial commercial@example.com --name="Commercial"
   php artisan serve
   ```

   Remplacer l'adresse d'exemple. Le mot de passe est demandé sans être affiché. La commande refuse d'écraser ou de promouvoir un compte existant.
6. Ouvrir <http://localhost:8000>. Connectez-vous avec le commercial créé.

Les pages Blade actuelles utilisent Bootstrap depuis un CDN et leurs styles intégrés. Pour compiler les ressources Vite présentes dans le projet :

```bash
npm ci
npm run build
```

## E-mails et file d'attente

Le modèle local utilise `MAIL_MAILER=log` et `QUEUE_CONNECTION=sync`. Les courriels sont alors écrits dans `storage/logs/laravel.log` ; ils ne sont pas réellement envoyés. Ce mode sert uniquement aux essais locaux.

Pour un vrai envoi, configurer SMTP ou Resend. Exemples de variables :

```dotenv
MAIL_MAILER=resend
RESEND_API_KEY=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME="Gestion des offres"
```

L'adresse d'expédition doit être autorisée chez le prestataire. `APP_URL` doit correspondre à l'adresse publique HTTPS afin que les liens de vérification soient corrects.

Pour reprendre le fonctionnement asynchrone utilisé pendant le stage, définir `QUEUE_CONNECTION=database` et garder ce processus actif dans un second terminal ou un second service Railway :

```bash
php artisan queue:work --tries=3 --timeout=60
```

Avec `QUEUE_CONNECTION=sync`, aucun worker séparé n'est nécessaire, mais l'envoi attend la réponse du prestataire pendant la requête HTTP.

## Données et démonstration

Aucun compte avec un mot de passe prédéfini n'est créé par les migrations ou par `db:seed`. Créer le commercial avec la commande dédiée, puis créer les comptes clients depuis le formulaire public. Le parcours normal reste : vérification e-mail, puis validation du compte par le commercial.

Le catalogue initial fourni est facultatif :

```bash
php artisan db:seed --class=OffresReellesSeeder
```

Ce seeder reprend les données déjà présentes dans le projet ; il ne certifie pas les offres commerciales actuellement vendues. Il réécrit les caractéristiques des quatre offres concernées. À utiliser uniquement pour initialiser une démonstration, avant d'adapter le catalogue. Il ne crée aucun stock régional : saisir les disponibilités dans l'espace commercial pour tester les commandes.

Les justificatifs sont privés, dans `storage/app/private`. Ils sont servis par les contrôleurs après contrôle d'accès. Aucun lien public `storage:link` n'est nécessaire aux fonctionnalités actuelles.

## Tests

```bash
composer test
```

Les tests utilisent SQLite en mémoire, une clé dédiée aux tests, un cache temporaire et aucun envoi réel d'e-mail. Ils n'utilisent pas votre base MySQL. L'extension PDO SQLite doit être disponible sur le poste de test.

## Déploiement et remise

- [Guide Railway et GitHub](docs/DEPLOIEMENT_ET_REMISE.md)
- [Compte rendu du contrôle et des modifications](docs/CONTROLE_PROJET.md)

Le code métier réalisé pendant le stage est destiné au cadre de partage convenu avec Djezzy. Les licences de Laravel et des dépendances demeurent celles de leurs auteurs. Aucun droit de publication publique du projet d'entreprise n'est accordé par ce README.
