# WordPress Starter avec Stack d'Automatisation

Ce repository contient un stack WordPress complet basé sur Docker avec des services d'automatisation et d'intelligence artificielle intégrés. Le projet combine WordPress avec n8n (automatisation), Qdrant (base de données vectorielle), PostgreSQL, Redis et MySQL pour créer un environnement de développement moderne et extensible.

## 🎯 Vue d'ensemble

**Stack principal :**
- **WordPress** : CMS principal avec thème parent Timber et thème enfant Tiz
- **n8n** : Plateforme d'automatisation de workflows
- **Qdrant** : Base de données vectorielle pour l'IA/ML
- **PostgreSQL** : Base de données pour n8n
- **MySQL** : Base de données WordPress
- **Redis** : Cache et gestion des sessions

**Intégrations clés :**
- Contact Form 7 connecté à n8n via webhooks personnalisés (thème Tiz)
- Thème parent Timber avec Twig et librairies PHP extensibles
- Thème enfant Tiz avec stack front-end moderne
- Pipeline CI/CD prêt pour Bitbucket

## 📋 Prérequis

- Docker Engine et Docker Compose plugin
- Node.js 18+ (pour le développement front-end)
- Composer (pour les dépendances PHP)

## 🚀 Démarrage Rapide

### Configuration initiale

1. **Copier le fichier d'environnement :**
   ```bash
   cp .env.example .env
   ```

2. **Configurer les variables d'environnement :**
   - `WORDPRESS_ENV=development` pour le développement local
   - `WORDPRESS_ENV=production` pour la production
   - Ajuster les ports et mots de passe selon vos besoins

### Démarrage en Développement

1. **Installer les dépendances des thèmes :**
   ```bash
   # Thème Timber (dépendances PHP)
   cd wordpress/wp-content/themes/timber-starter-theme
   composer install
   cd ../../../../

   # Thème Tiz (dépendances Node.js)
   cd wordpress/wp-content/themes/tiz
   npm install
   cd ../../../../
   ```

2. **Démarrer le stack Docker :**
   ```bash
   docker compose up -d
   ```

3. **Lancer le développement front-end (thème Tiz) :**
   ```bash
   cd wordpress/wp-content/themes/tiz
   npm run dev
   ```
   Ceci lance Webpack en mode watch et BrowserSync pour le rechargement automatique.

4. **Accéder aux services :**
   - WordPress : http://localhost:8080
   - n8n : http://localhost:5678
   - BrowserSync (si activé) : http://localhost:3000

### Démarrage en Production

1. **Configurer l'environnement de production :**
   ```bash
   # Dans le fichier .env
   WORDPRESS_ENV=production
   ```

2. **Build des assets de production :**
   ```bash
   # Thème Timber
   cd wordpress/wp-content/themes/timber-starter-theme
   composer install --prefer-dist --no-dev --optimize-autoloader
   
   # Thème Tiz
   cd ../tiz
   npm ci --only=production
   npm run build
   ```

3. **Démarrer le stack :**
   ```bash
   docker compose up -d
   ```

### Stopping the Stack

```bash
# Arrêt simple (conserve les données)
docker compose down

# Arrêt avec suppression des volumes (DESTRUCTIF)
docker compose down -v
```

## 🔧 Services et Infrastructure

### WordPress
- **Port** : 8080
- **Thèmes** : Montés depuis `wordpress/wp-content/themes/`
- **Configuration** : Via variables d'environnement et `wp-config.php`
- **Base de données** : MySQL

### n8n (Automatisation)
- **Port** : `${N8N_PORT}` (défaut: 5678)
- **Base de données** : PostgreSQL
- **Volume** : `n8n-data` pour la persistance des workflows
- **Intégration** : Réception de webhooks depuis Contact Form 7

### MySQL
- **Rôle** : Base de données WordPress
- **Configuration** : Credentials via `.env`
- **Volume** : `db-data`

### Redis
- **Port** : `${REDIS_PORT}`
- **Usage** : Cache, sessions, queues
- **Volume** : `redis-data`

### PostgreSQL
- **Rôle** : Base de données pour n8n
- **Volume** : `postgres-data`
- **Configuration** : Automatique via Docker Compose

### Qdrant
- **Port** : `${QDRANT_PORT}` (défaut: 6333)
- **Rôle** : Base de données vectorielle pour l'IA
- **Volume** : `qdrant-data`
- **API** : REST et gRPC disponibles

## 💡 Conseils de Développement

### Développement Local
- Le répertoire `wordpress/` est monté dans le conteneur → modifications immédiates
- Utiliser `docker compose exec wordpress bash` pour WP-CLI ou Composer dans le conteneur
- Pour le thème Tiz : `npm run dev` active le watch mode + BrowserSync
- BrowserSync proxy WordPress sur le port 3000 avec rechargement automatique

### Debugging et Logs
```bash
# Logs d'un service spécifique
docker compose logs -f wordpress
docker compose logs -f n8n

# Accès shell aux conteneurs
docker compose exec wordpress bash
docker compose exec postgres bash
```

### Workflows n8n Recommandés
- **Contact Form → Email/Slack** : Traitement des soumissions de formulaires
- **Content Indexing** : Synchronisation WordPress → Qdrant pour la recherche
- **User Analytics** : Collecte et analyse des interactions utilisateurs
- **Content Generation** : Intégration avec des APIs d'IA pour la génération de contenu

## 🚀 CI/CD (Bitbucket Pipelines)

**Configuration :** `bitbucket-pipelines.yml`

### Étapes du Pipeline

1. **Build Timber Theme**
   - Image : `composer:latest`
   - Commandes : `composer install --prefer-dist --no-dev --optimize-autoloader`
   - Répertoire : `timber-starter-theme`

2. **Build Tiz Theme**
   - Image : `node:22`
   - Commandes : 
     ```bash
     npm ci
     npm run build
     ```
   - Répertoire : `tiz`

3. **Artefacts**
   - Dépendances PHP optimisées
   - Assets front-end compilés et minifiés
   - Prêt pour déploiement

### Variables d'Environnement (Bitbucket)
Configurer dans les paramètres du repository :
- Secrets de déploiement
- URLs de production
- Clés API pour services externes

## 🎨 Thèmes WordPress

### Timber Starter Theme (Thème Parent)

**Localisation :** `wordpress/wp-content/themes/timber-starter-theme`

**Caractéristiques :**
- **Thème parent** qui intègre le framework Timber pour utiliser **Twig** dans WordPress
- Permet l'intégration de **librairies PHP supplémentaires** via **Composer**
- Templates **Twig** pour une syntaxe moderne et sécurisée
- Séparation claire entre logique PHP (`src/`) et templates (`views/`)
- Tests unitaires intégrés avec PHPUnit
- Architecture orientée objet avec classes personnalisées

**Ce que ce thème apporte :**
- **Twig templating** : Syntaxe claire et sécurisée pour les templates
- **Contexte structuré** : Organisation des données pour les vues
- **Extensibilité** : Intégration facile de librairies PHP via Composer
- **Performance** : Cache des templates Twig
- **Sécurité** : Protection automatique contre les failles XSS

**Structure :**
- `src/StarterSite.php` : Classe principale du thème
- `views/` : Templates Twig
- `static/` : Assets statiques
- `tests/` : Tests PHPUnit
- `composer.json` : Dépendances PHP (Timber + librairies additionnelles)

**Utilisation en développement :**
```bash
cd wordpress/wp-content/themes/timber-starter-theme
composer install
```

### Thème Tiz (Thème Enfant)

**Localisation :** `wordpress/wp-content/themes/tiz`

**Caractéristiques :**
- **Thème enfant** du Timber Starter Theme
- Hérite des fonctionnalités Timber/Twig du thème parent
- Stack front-end moderne avec **Webpack 5**
- **Tailwind CSS 4** pour le styling
- **GSAP** pour les animations
- **BrowserSync** pour le développement en temps réel
- Build optimisé pour la production

**Relation Parent/Enfant :**
- Hérite automatiquement de toutes les fonctionnalités du thème parent Timber
- Peut surcharger les templates Twig du parent si nécessaire
- Accès à toutes les librairies PHP installées via Composer dans le thème parent
- Combine les avantages de Timber/Twig avec un workflow front-end moderne

**Fonctionnalités avancées :**
- Intégration **Contact Form 7** avec webhooks personnalisés
- Système de Custom Post Types
- Assets conditionnels selon l'environnement :
  - Développement : `dev_build/` (hot reload)
  - Production : `dist/` (optimisé et minifié)

**Scripts disponibles :**
```bash
npm run dev      # Mode développement avec watch et BrowserSync
npm run build    # Build de production optimisé
```

**Architecture des assets :**
- Mode développement : Webpack watch + BrowserSync sur port 3000
- Mode production : Assets minifiés et optimisés dans `dist/`

## 🔗 Intégration Contact Form 7 ↔ n8n

Le thème Tiz inclut un système personnalisé de webhooks pour Contact Form 7 qui permet une intégration transparente avec n8n.

### Fonctionnalités

**Custom Post Type "Webhooks CF7" :**
- Interface d'administration pour mapper les formulaires CF7 à des URLs de webhook
- Configuration par formulaire de l'URL de destination n8n
- Gestion automatique de l'envoi des données

**Processus d'intégration :**
1. **Configuration** : Dans l'admin WordPress, créer un "Webhook CF7" et associer un formulaire CF7 à une URL n8n
2. **Soumission** : Quand le formulaire est soumis, les emails CF7 sont désactivés
3. **Webhook** : Les données sont automatiquement POSTées en JSON vers n8n
4. **Réponse** : Le statut de réussite/échec est affiché à l'utilisateur

**Format des données envoyées :**
```json
{
  "form": {
    "id": 123,
    "title": "Contact Form"
  },
  "meta": {
    "timestamp": "2025-10-30T10:00:00Z",
    "remote_ip": "192.168.1.1",
    "user_agent": "Mozilla/5.0...",
    "url": "https://example.com/contact",
    "referer": "https://example.com"
  },
  "fields": {
    "your-name": "John Doe",
    "your-email": "john@example.com",
    "your-message": "Hello world"
  },
  "files": [
    {
      "field-name": [
        {
          "filename": "document.pdf",
          "path": "/tmp/upload/document.pdf"
        }
      ]
    }
  ]
}
```

### Configuration n8n

1. **Webhook n8n** : Créer un workflow n8n avec un trigger "Webhook"
2. **URL de webhook** : Copier l'URL générée dans l'interface "Webhooks CF7"
3. **Traitement** : Configurer le workflow n8n pour traiter les données reçues
4. **Réponse** : n8n doit répondre avec `{"ok": true}` pour confirmer la réception

## 🗄️ Services de Données pour n8n

### PostgreSQL
- **Rôle** : Base de données principale pour n8n
- **Configuration** : Automatiquement configurée via variables d'environnement
- **Persistance** : Volume Docker `postgres-data`

### Qdrant (Base de Données Vectorielle)
- **Rôle** : Stockage et recherche de vecteurs pour l'IA/ML
- **Port** : Configurable via `${QDRANT_PORT}` (défaut: 6333)
- **Utilisation** : Parfait pour les workflows n8n impliquant :
  - Recherche sémantique
  - Recommandations
  - Classification de texte
  - RAG (Retrieval Augmented Generation)

**Exemple d'utilisation avec n8n :**
- Embedding de contenu WordPress dans Qdrant
- Recherche sémantique de contenus similaires
- Système de recommandations basé sur les interactions utilisateurs

## Local Development Tips

- The entire `wordpress/` directory is mounted into the WordPress container, so local edits are reflected immediately.
- Use `docker compose exec wordpress bash` to run Composer commands or WP-CLI inside the container if needed.
- For front-end development on `tiz`, run `npm run dev` locally (after `npm install`) to watch and rebuild assets into `dev_build/`.
- When preparing a release, set `WORDPRESS_ENV=production` and run `npm run build` to refresh `dist/` assets used in production.

## CI/CD (Bitbucket Pipelines)

- Defined in `bitbucket-pipelines.yml`.
- Step 1: installs Composer dependencies inside `timber-starter-theme` using the official Composer image.
- Step 2: installs Node dependencies and builds assets for `tiz` using Node 22.
- Built artifacts are stored as pipeline artifacts for deployment or further stages.
- Configure repository variables in Bitbucket for any secrets required during deploy stages.

## 📁 Volumes et Persistance

Le stack utilise des volumes nommés Docker pour la persistance des données :

- `db-data` : Données MySQL (WordPress)
- `postgres-data` : Données PostgreSQL (n8n)
- `redis-data` : Cache Redis
- `qdrant-data` : Collections et index Qdrant
- `n8n-data` : Workflows et configuration n8n

**⚠️ Attention :** La suppression des volumes efface définitivement les données persistées.

```bash
# Arrêter et supprimer les volumes (DESTRUCTIF)
docker compose down -v

# Arrêt simple (conserve les données)
docker compose down
```

## 🛠️ Dépannage

### Problèmes Courants

**Ports déjà utilisés :**
- Vérifier que les ports définis dans `.env` sont libres
- Modifier les ports dans `.env` si nécessaire

**Échec des dépendances en CI :**
- Vérifier la présence de `composer.lock` et `package-lock.json`
- S'assurer que les répertoires de cache sont accessibles en écriture

**Problèmes de conteneurs :**
```bash
# Vérifier les logs d'un service
docker compose logs -f <service>

# Redémarrer un service spécifique
docker compose restart <service>

# Reconstruire les images
docker compose build --no-cache
```

**Webhooks CF7 → n8n :**
- Vérifier que l'URL n8n est accessible depuis WordPress
- Contrôler les logs WordPress : `/wp-content/debug.log`
- Vérifier que n8n répond bien avec `{"ok": true}`

### Debugging n8n
- Interface web : http://localhost:5678
- Logs en temps réel : `docker compose logs -f n8n`
- Test de webhooks : utiliser l'outil de test intégré n8n

### Performance et Optimisation
- **Redis** : Activer le cache d'objets WordPress
- **Qdrant** : Optimiser la taille des collections selon vos données
- **MySQL** : Ajuster `innodb_buffer_pool_size` pour de gros volumes

## 📝 Notes Techniques

### Sécurité
- Changer tous les mots de passe par défaut en production
- Utiliser HTTPS en production
- Limiter l'accès aux ports de services (PostgreSQL, Redis, etc.)

### Backup
- Sauvegarder régulièrement les volumes Docker
- Exporter les workflows n8n depuis l'interface
- Sauvegarder la base de données WordPress

### Monitoring
- Surveiller les logs Docker Compose
- Monitorer l'usage des ressources des conteneurs
- Vérifier la santé des webhooks n8n régulièrement

---

## 🤝 Contribution

Ce projet est conçu pour être extensible. N'hésitez pas à :
- Ajouter de nouveaux workflows n8n
- Étendre les fonctionnalités des thèmes
- Améliorer l'intégration Qdrant
- Optimiser le pipeline CI/CD
