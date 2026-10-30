# Déploiement Railway et remise à l'encadrant

## 1. Utiliser la copie corrigée sans perdre les données locales

L'archive corrigée contient le code source, les migrations, les tests, les fichiers de verrouillage des dépendances et les modèles de variables. Elle ne contient ni `.env`, ni dépendances, ni historique Git, ni justificatifs, ni journaux, ni base de données.

**Sauvegarder le dossier original avant toute copie.** Pour mettre à jour le dépôt existant, copier les fichiers corrigés dans le dossier original. Conserver son `.git`, son `.env` et ses données locales privées. Le fait que ces éléments ne soient pas dans l'archive de remise ne signifie pas qu'il faut les supprimer du poste de travail.

Supprimer du dossier de travail les anciens fichiers inutiles, s'ils sont présents :

```powershell
$obsolete = @(
    'install_navigation.php',
    'install_boutique_validation.php',
    'e AppModelsBoutique',
    'id_reclamation',
    'quantite_disponible',
    'reclamationService',
    'tock->quantite_disponible = 20;',
    'tock-quantite_disponible = 20;'
)
foreach ($path in $obsolete) {
    if (Test-Path -LiteralPath $path) {
        Remove-Item -LiteralPath $path
    }
}
```

Ne pas lancer `git clean -fdx` : cette commande peut effacer le `.env` et les données ignorées. Ne pas supprimer les migrations : elles reconstruisent la structure de la base.

Après la copie :

```bash
composer install
php artisan config:clear
composer test
```

## 2. Envoyer le code sur GitHub

Respecter la consigne de dépôt interne Djezzy donnée par l'encadrant. Si GitHub est autorisé pour ce partage, utiliser un dépôt privé et les personnes autorisées.

Dans le dossier original qui contient déjà `.git` :

```bash
git status --short
git diff --stat
git add -A
git diff --cached --name-only
git diff --cached
```

Relire les changements avant le commit. Le `.env`, `vendor/`, `node_modules/`, les données de `storage/`, les bases SQLite et les caches ne doivent pas être ajoutés. Les fichiers `.env.example`, `.env.railway.example`, `composer.lock`, `package-lock.json`, les migrations et les tests doivent rester.

```bash
git commit -m "fix: prepare deployment and clean project handover"
git push origin main
```

L'archive fournie ne remplace pas l'historique Git du dépôt existant. Le contrôle effectué sur l'historique inclus dans l'archive initiale n'a retrouvé aucune des valeurs sensibles du `.env` local. Il ne porte pas sur des branches absentes de cette archive ni sur d'anciens secrets différents.

## 3. Configurer Railway

Le projet utilise **Railpack**, avec `railway.toml` et `start-container.sh` à la racine. Aucune commande `npm run start` n'est nécessaire.

1. Dans le service applicatif, sélectionner le dépôt et la branche contenant cette correction. Le répertoire racine doit être celui qui contient `artisan`, `composer.json` et `railway.toml`.
2. Choisir Railpack comme builder. Retirer une éventuelle ancienne commande de démarrage personnalisée si elle masque celle du fichier de configuration. La commande attendue est `bash /app/start-container.sh`.
3. Ajouter ou retrouver le service **MySQL**. La base Railway est distincte de la base présente sur le PC : les données locales ne sont pas copiées automatiquement.
4. Recopier et compléter `.env.railway.example` dans les **Variables du service applicatif**. Les références `${{MySQL.MYSQLHOST}}`, etc., supposent que le service de base s'appelle exactement `MySQL` ; sinon, choisir les références correspondantes dans Railway.
5. Générer une clé dédiée au déploiement :

   ```bash
   php artisan key:generate --show
   ```

   Copier le résultat uniquement dans `APP_KEY` sur Railway, jamais dans GitHub. Garder cette même clé entre les redéploiements d'une installation existante ; la changer invalide notamment les sessions et les données chiffrées.
6. Renseigner le vrai domaine HTTPS dans `APP_URL`, puis configurer les e-mails. Les valeurs laissées vides dans le modèle doivent être complétées.
7. Attacher un volume persistant au service applicatif, au chemin **`/app/storage`**, pour conserver les justificatifs et les sessions entre redéploiements. MySQL conserve ses propres données dans son service.
8. Déployer la nouvelle version du dépôt. Le script recharge d'abord la configuration, exécute les migrations, recrée les caches puis lance le serveur. Aucun seeder ne tourne automatiquement.

Pour une petite démonstration, `QUEUE_CONNECTION=sync` simplifie le déploiement. Si `QUEUE_CONNECTION=database`, ajouter un service worker du même dépôt, avec les mêmes variables de connexion MySQL et d'e-mail. Utiliser :

```bash
php artisan config:clear && php artisan queue:work --tries=3 --timeout=60
```

Pour ce worker, supprimer le contrôle HTTP `/up` hérité du fichier `railway.toml` : un worker ne sert pas de page web. Les justificatifs n'ont pas besoin d'être montés dans le worker d'envoi de vérification actuel.

## 4. Créer les accès en ligne

Dans un terminal connecté au conteneur Railway en cours d'exécution, lancer :

```bash
php artisan app:create-commercial commercial@example.com --name="Commercial"
```

Remplacer l'adresse. Ne pas créer le compte dans le terminal local en pensant qu'il apparaîtra sur Railway. Avec Railway CLI, `railway ssh` ouvre le conteneur ; `railway run` exécute une commande sur le PC et ne donne pas nécessairement accès au réseau privé MySQL.

Créer un compte client depuis le site, vérifier l'e-mail puis valider son compte avec le commercial. Ajouter des offres et du stock, puis tester une boutique, une commande et une réclamation. Pour tester le catalogue initial, le seeder facultatif du README doit lui aussi être exécuté dans l'environnement souhaité.

## 5. Vérifier avant la remise

- Le déploiement est actif et `/up` répond. Ce contrôle prouve que Laravel démarre, pas que MySQL et les e-mails fonctionnent.
- La connexion des deux rôles fonctionne.
- Un client peut suivre le parcours d'inscription et de validation.
- Les justificatifs déposés s'ouvrent uniquement avec les accès autorisés.
- Une commande peut être créée et traitée ; le stock correspond au scénario.
- Les données restent disponibles après un redéploiement de contrôle.

Transmettre à l'encadrant : le dépôt autorisé ou l'archive source propre, le lien de démonstration réellement testé et le README. Communiquer les identifiants de démonstration séparément du code.

## Références techniques

- Railpack, PHP et personnalisation du démarrage : <https://railpack.com/languages/php/>
- Laravel, déploiement : <https://laravel.com/framework/docs/deployment>
- Railway, variables : <https://docs.railway.com/variables>
- Railway, volumes persistants : <https://docs.railway.com/volumes>
