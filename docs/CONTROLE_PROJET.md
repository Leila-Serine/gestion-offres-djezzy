# Contrôle du projet et préparation de la remise

Contrôle effectué le **30 octobre 2026**, sur l'archive `Projey.zip`. La copie corrigée est séparée de l'archive reçue. Aucun dépôt distant et aucun service Railway n'ont été modifiés.

## Résultats principaux

- **Avant modification : 81 tests réussis, 207 assertions.**
- **Après correction : 84 tests réussis, 222 assertions.** Les trois nouveaux tests portent sur la création explicite du commercial et le refus d'écraser un compte existant ou d'accepter un mot de passe invalide.
- Compilation Vite réussie. L'avertissement sur `fontaine` est facultatif ; il n'empêche pas la compilation.
- Audit npm complet : aucun avis de sécurité signalé à la date du contrôle.
- Installation Composer en mode production (`--no-dev`) réussie ; pages `/up`, `/`, `/login` et `/register` accessibles avec le serveur PHP de vérification, et `/offres` redirigée vers la connexion sans authentification.
- Validation Composer réussie. Les dépendances PHP signalées ont été mises à jour dans `composer.lock` ; le contrôle Composer ne signale plus d'avis de sécurité à la date du contrôle.
- Vérification de syntaxe des fichiers PHP applicatifs et du script Bash.
- Migrations sur SQLite vide et deux exécutions consécutives du nouveau script de démarrage réussies.
- Le scénario part d'une configuration compilée volontairement ancienne avec cache en base et chemin SQLite obsolète. Le script utilise ensuite le bon chemin transmis au démarrage et le cache fichier ; l'ancienne base n'est pas créée.
- Le seeder de catalogue a été exécuté deux fois : quatre offres, aucun doublon, champs Internet numériques présents et aucun compte créé. Le cache en base peut aussi être vidé après les migrations.

## Plantage Railway

Le journal fourni échoue sur `delete from cache`, avec une connexion SQLite. Les valeurs locales de `.env` sont pourtant MySQL et cache fichier. Elles ne prouvent pas les valeurs réellement disponibles dans le service Railway.

Le script standard Railpack consulté lance les migrations avant `optimize:clear`. Une configuration compilée au build peut donc encore être utilisée au moment des migrations. Ce mécanisme explique le journal et a été reproduit localement ; l'état exact du service Railway n'a pas été inspecté.

Corrections :

- `start-container.sh` efface d'abord les caches de configuration, routes, vues et événements ; il ne lance pas `optimize:clear` ni de seeder.
- Les migrations sont ensuite appliquées sur la base configurée au démarrage, puis les caches sont reconstruits.
- `railway.toml` fixe le builder, la commande de démarrage et le contrôle `/up`.
- Les valeurs de repli sont désormais `mysql` et `file`, cohérentes avec le projet.
- Une migration crée les tables `cache` et `cache_locks`, si le cache en base est choisi ultérieurement. L'archive initiale ne comportait aucune migration de cache.
- Le lien public de stockage n'est pas créé : les justificatifs actuels sont privés et passent par les contrôleurs.

## Éléments exclus de la copie à partager

| Élément | Traitement et raison |
| --- | --- |
| `.env` | Exclu. Contient la configuration privée et des secrets ; garder le fichier uniquement sur le poste et configurer Railway séparément. |
| `.git/` | Exclu de l'archive de remise. Conserver l'historique dans le dossier de travail original. |
| `vendor/`, `node_modules/` | Exclure du partage ; réinstaller avec Composer et npm. |
| Fichiers de `storage/` | Exclure justificatifs, sessions, vues compilées, caches et journaux. Garder les petits `.gitignore` qui conservent la structure. |
| `bootstrap/cache/*.php`, `.phpunit.result.cache` | Résultats locaux générés ; exclus. |
| `public/build/` | Résultat de compilation ; exclu du code source, reconstruit au déploiement. |
| `e AppModelsBoutique`, `id_reclamation`, `quantite_disponible`, `reclamationService`, fichier commençant par `tock-` | Fragments de commandes ou fichiers vides créés par erreur. Exclus ; ils étaient déjà suivis par Git pour certains. |
| `install_navigation.php`, `install_boutique_validation.php` | Anciens scripts de modification de fichiers. Les providers et inclusions correspondants sont déjà intégrés. Exclus de la remise. |
| `migrations_diagrammes.txt` | Notes internes de travail, exclues de la remise applicative. |
| `AGENTS.md` | Instructions d'outil de développement, exclues de la remise. |

Le `.gitignore` a été renforcé et sa règle mal concaténée `CLAUDE.md*:Zone.Identifier` séparée en règles correctes. Il protège aussi les variantes locales de `.env`, les données de stockage et les exports locaux de base. Les modèles sans secret `.env.example` et `.env.railway.example` restent inclus.

## Autres corrections

- README remplacé par la présentation du projet et une installation reproductible.
- Modèle de variables Railway ajouté, sans valeur sensible.
- `DatabaseSeeder` ne crée plus le `Test User` de Laravel avec un mot de passe prévisible et sans rôle. Aucun compte n'est créé automatiquement.
- Commande `app:create-commercial` ajoutée : demande un mot de passe non affiché, le fait hacher par le modèle et refuse un e-mail déjà utilisé. Aucun client existant n'est promu par cette commande.
- `UserFactory` définit le rôle client par défaut, afin de respecter la colonne obligatoire.
- Chaîne accidentelle `php artisan migrate` supprimée de la liste des attributs remplissables de `User`.
- Le catalogue initial renseigne désormais les champs normalisés Internet et durée, utilisés par les filtres. Les autres caractéristiques textuelles existantes sont conservées. Aucun stock fictif n'est ajouté.
- `phpunit.xml` fournit une clé publique dédiée aux tests et force une base SQLite en mémoire pour éviter de dépendre du `.env` privé du développeur.
- Métadonnées Composer adaptées au projet ; les licences des dépendances ne sont pas modifiées. `package-lock.json` ajouté pour reproduire l'installation npm.

## Secrets et limites

Les valeurs sensibles du `.env` local ont été comparées aux fichiers suivis par Git et à tous les blobs de l'historique présent dans l'archive : **aucune correspondance**. Le `.env` lui-même n'était pas suivi. Cette vérification ne garantit pas l'absence d'autres secrets anciens ou de données dans des branches non fournies.

Le contrôle porte sur la préparation du code et du démarrage, pas sur une certification de sécurité complète. Les tests automatisés et la reproduction du démarrage utilisent SQLite ; aucun serveur MySQL 8.4 réel n'a été exécuté ici. L'exécution finale de FrankenPHP/Caddy a été remplacée par un témoin lors du test du script. Il faut donc encore déployer et vérifier le vrai serveur, les connexions MySQL, la persistance du volume et la livraison des e-mails sur Railway.

Le site nécessite un accès aux CDN Bootstrap pour son affichage actuel. Aucun compte réel, justificatif ou export de données n'est fourni dans la version de remise. Un dépôt ou un lien externe doit suivre les consignes de confidentialité et de dépôt interne données par Djezzy.
