# WordPress Starter with Automation Stack

This repository contains a complete Docker-based WordPress stack with integrated automation and artificial intelligence services. The project combines WordPress with n8n (automation), Qdrant (vector database), PostgreSQL, Redis and MySQL to create a modern and extensible development environment.

## 🎯 Overview

**Main Stack:**
- **WordPress**: Main CMS with Timber parent theme and Tiz child theme
- **n8n**: Workflow automation platform
- **Qdrant**: Vector database for AI/ML
- **PostgreSQL**: Database for n8n
- **MySQL**: WordPress database
- **Redis**: Cache and session management

**Key Integrations:**
- Contact Form 7 connected to n8n via custom webhooks (Tiz theme)
- Timber parent theme with Twig and extensible PHP libraries
- Tiz child theme with modern front-end stack
- CI/CD pipeline ready for GitHub Actions and Bitbucket Pipelines

## 📋 Prerequisites

**For local development:**
- Docker Engine and Docker Compose plugin
- Node.js 22+ (for front-end development)
- Composer 2 (for PHP dependencies)

**For production servers:**
- PHP 8.4+
- MySQL 8.0+
- Nginx or Apache
- No Docker required (CI/CD handles deployment)

## 🚀 Quick Start (Local Development)

> **Note:** Docker is used **only for local development**. Production and pre-production servers run WordPress natively (without Docker). The CI/CD pipeline handles building and deploying to servers.

### Initial Configuration

1. **Copy the environment file:**
   ```bash
   cp .env.example .env
   ```

2. **Configure environment variables:**
   - `WORDPRESS_ENV=development` for local development (loads `dev_build/` assets)
   - Adjust ports and passwords according to your needs

### Development Setup

1. **Install theme dependencies:**
   ```bash
   # Timber Theme (PHP dependencies)
   cd wordpress/wp-content/themes/timber-starter-theme
   composer install
   ```

   ```bash
   # Tiz Theme (Node.js dependencies)
   cd wordpress/wp-content/themes/tiz
   npm install
   ```

2. **Start the Docker stacks:**
   ```bash
   # WordPress stack (Web)
   docker compose up -d

   # Automation stack (n8n, Postgres, Qdrant) - optional
   docker compose -f docker-compose.n8n.yml up -d
   ```

3. **Launch front-end development (Tiz theme):**
   ```bash
   cd wordpress/wp-content/themes/tiz
   npm run dev
   ```
   This starts Webpack in watch mode and BrowserSync for automatic reloading.

4. **Access services:**
   - WordPress: http://localhost:8080
   - BrowserSync (if enabled): http://localhost:3000
   - n8n (if automation stack started): http://localhost:5678

## 🚀 Server Deployment (No Docker)

> **Important:** Production and pre-production servers run WordPress **natively** (PHP, MySQL, Nginx) **without Docker**. The CI/CD pipeline builds assets and deploys via rsync.

### Server Requirements

- PHP 8.4+ with required extensions (mysql, curl, gd, mbstring, xml, zip)
- MySQL 8.0+ or MariaDB 10.5+
- Nginx or Apache with proper rewrite rules
- SSL certificate (Let's Encrypt recommended)

### Asset Loading Based on Environment

The theme automatically loads different assets based on `WP_ENV`:

| Environment | `WP_ENV` Value | Assets Folder | Build Command |
|-------------|----------------|---------------|---------------|
| Local (Docker) | `development` | `dev_build/` | `npm run dev` |
| Pre-production | `development` | `dev_build/` | `npm run build:dev` |
| Production | `production` | `dist/` | `npm run build` |

**How it works:**
- The theme reads `WP_ENV` constant from `wp-config.php`
- If `WP_ENV` is not defined, it defaults to `production` (safe fallback)
- `development` → loads unminified assets with source maps for debugging
- `production` → loads minified, optimized assets

## 🚀 CI/CD (GitHub Actions & Bitbucket Pipelines)

**Configuration files:**
- GitHub: `.github/workflows/deploy.yml`
- Bitbucket: `bitbucket-pipelines.yml`

### Build Strategy by Branch

| Branch | Environment | Build Command | Output Folder | `WP_ENV` on Server |
|--------|-------------|---------------|---------------|--------------------|
| `stage` | Pre-production | `npm run build:dev` | `dev_build/` | `development` |
| `main` | Production | `npm run build` | `dist/` | `production` |

### Pipeline Steps

1. **Build Timber Theme (Parent Theme)** - Both branches
   - Image: `composer:2`
   - Commands: `composer install --prefer-dist --no-dev --optimize-autoloader`
   - Directory: `timber-starter-theme`
   - Result: Optimized PHP dependencies

2. **Build Child Theme (JS/CSS)** - Different per branch
   - Image: `node:22`
   - **Branch `stage`**: `npm ci && npm run build:dev` → outputs to `dev_build/`
   - **Branch `main`**: `npm ci && npm run build` → outputs to `dist/`
   - Directory: Theme specified by `THEME_NAME` variable (default: `tiz`)

3. **Deploy to Server**
   - Uses `rsync` over SSH to sync files to the server
   - Excludes `.env`, `wp-config.php`, `node_modules/`, `.git/`, `.github/`, `wp-content/uploads/`
   - Preserves server-side configuration

### Branch Workflow

```
feature/xxx  →  stage (pre-production)  →  main (production)
     ↓              ↓                          ↓
  Local dev    Auto-deploy to           Manual approval
  (Docker)     preprod server           then deploy to prod
               (dev_build/)             (dist/)
```

1. **Feature development**: Create branch from `stage`, develop locally with Docker
2. **Pre-production**: Merge to `stage` → auto-deploy to preprod server with `dev_build/` assets
3. **Production**: Merge `stage` to `main` → deploy to production with `dist/` assets (manual approval on GitHub)

---

## 🔧 GitHub Actions Setup (Step by Step)

### Step 1: Generate SSH Key (on your local machine)

```bash
# Create a dedicated key WITHOUT passphrase
ssh-keygen -t ed25519 -f ~/.ssh/deploy_github_actions -C "github-actions" -N ""

# Display the PRIVATE key (for GitHub Secrets)
cat ~/.ssh/deploy_github_actions

# Display the PUBLIC key (for the server)
cat ~/.ssh/deploy_github_actions.pub
```

### Step 2: Add Public Key to Server

```bash
# Connect to your server
ssh root@YOUR_SERVER_IP

# Add the public key
echo "ssh-ed25519 AAAA... github-actions" >> ~/.ssh/authorized_keys

# Verify
tail -1 ~/.ssh/authorized_keys
```

### Step 2b: Configure WP_ENV on Server

Add this line to `wp-config.php` on each server **before** `/* That's all, stop editing! */`:

**Pre-production server (`stage` branch deploys here):**
```php
define('WP_ENV', 'development');
```
This loads assets from `dev_build/` (unminified, with source maps for debugging).

**Production server (`main` branch deploys here):**
```php
define('WP_ENV', 'production');
```
This loads assets from `dist/` (minified, optimized for performance).

> **Note:** If `WP_ENV` is not defined, the theme defaults to `production` for safety.

### Step 3: Test SSH Connection

```bash
# From your local machine
ssh -i ~/.ssh/deploy_github_actions root@YOUR_SERVER_IP "echo OK"
# Should display: OK
```

### Step 4: Create GitHub Secrets

Go to: `https://github.com/YOUR_USERNAME/YOUR_REPO/settings/secrets/actions`

Click **"New repository secret"** for each:

| Secret Name | Value |
|-------------|-------|
| `SSH_PRIVATE_KEY_PREPROD` | Content of `~/.ssh/deploy_github_actions` (entire private key) |
| `SSH_HOST_PREPROD` | Server IP (e.g., `51.210.183.88`) |
| `SSH_USER_PREPROD` | SSH user (e.g., `root`) |
| `DEPLOY_PATH_PREPROD` | Server path (e.g., `/var/www/preprod/wordpress`) |
| `SSH_PRIVATE_KEY_PROD` | Same private key (or different for prod server) |
| `SSH_HOST_PROD` | Production server IP |
| `SSH_USER_PROD` | Production SSH user |
| `DEPLOY_PATH_PROD` | Production server path |

### Step 5: Create GitHub Environments

Go to: `https://github.com/YOUR_USERNAME/YOUR_REPO/settings/environments`

1. Create environment **`pre-production`**
   - No protection rules (auto-deploy on push to `stage`)

2. Create environment **`production`**
   - Check **"Required reviewers"**
   - Add yourself as reviewer
   - This enables manual approval before production deployment

### Step 6: Create Variables (Optional)

Go to: `https://github.com/YOUR_USERNAME/YOUR_REPO/settings/variables/actions`

| Variable Name | Value |
|---------------|-------|
| `THEME_NAME` | `tiz` (or your theme name) |

### Step 7: Create the `stage` Branch

```bash
git checkout main
git checkout -b stage
git push -u origin stage
```

### Usage

```bash
# Deploy to preprod (automatic)
git checkout stage
git merge feature/my-feature
git push origin stage
# → Pipeline runs and deploys to preprod

# Deploy to production (manual approval)
git checkout main
git merge stage
git push origin main
# → Go to GitHub Actions → Approve deployment
```

---

## 🔧 Bitbucket Pipelines Setup (Step by Step)

### Step 1: Generate SSH Key (on your local machine)

Generate a **dedicated key pair** for Bitbucket Pipelines (not the server's existing key):

```bash
# Create a dedicated key WITHOUT passphrase
ssh-keygen -t ed25519 -f ~/.ssh/bitbucket_deploy -C "bitbucket-pipelines" -N ""

# Display the PRIVATE key (for Bitbucket)
cat ~/.ssh/bitbucket_deploy

# Display the PUBLIC key (for the server)
cat ~/.ssh/bitbucket_deploy.pub
```

This creates:
- `~/.ssh/bitbucket_deploy` → **private key** (goes to Bitbucket)
- `~/.ssh/bitbucket_deploy.pub` → **public key** (goes to the server)

### Step 2: Add SSH Key to Bitbucket

Go to: `Repository Settings → SSH keys`

1. Click **"Use my own keys"**
2. Paste the entire **private key** content (including `-----BEGIN...` and `-----END...`)
3. Bitbucket will extract and display the public key

### Step 3: Add Public Key to Server

Connect to your server and add the **public key** to authorized keys:

```bash
# Connect to your server
ssh root@YOUR_SERVER_IP

# Add the public key (replace with your actual key)
echo "ssh-ed25519 AAAA... bitbucket-pipelines" >> ~/.ssh/authorized_keys

# Verify
tail -1 ~/.ssh/authorized_keys
```

### Step 3b: Test SSH Connection

From your local machine, verify the key works:

```bash
ssh -i ~/.ssh/bitbucket_deploy root@YOUR_SERVER_IP "echo OK"
# Should display: OK
```

### Step 3c: Configure WP_ENV on Server

Add `WP_ENV` constant to `wp-config.php` on each server **before** `/* That's all, stop editing! */`:

**Pre-production server:**
```php
define('WP_ENV', 'development');
```

**Production server:**
```php
define('WP_ENV', 'production');
```

### Step 4: Create Repository Variables

Go to: `Repository Settings → Repository variables`

#### Required Variables

| Variable Name | Description | Example | Secured |
|---------------|-------------|---------|---------|
| `SSH_USER_PREPROD` | SSH user for preprod server | `root` | No |
| `SSH_HOST_PREPROD` | Preprod server IP or domain | `51.210.183.88` | No |
| `DEPLOY_PATH_PREPROD` | Deployment path on preprod | `/var/www/preprod.example.com` | No |
| `SSH_USER_PROD` | SSH user for production server | `root` | No |
| `SSH_HOST_PROD` | Production server IP or domain | `51.210.183.88` | No |
| `DEPLOY_PATH_PROD` | Deployment path on production | `/var/www/example.com` | No |

#### Optional Variables

| Variable Name | Description | Default Value | Secured |
|---------------|-------------|---------------|---------|
| `THEME_NAME` | Name of the theme to build | `tiz` | No |


---

## 📋 CI/CD Quick Reference

| Action | GitHub | Bitbucket |
|--------|--------|-----------|
| Config file | `.github/workflows/deploy.yml` | `bitbucket-pipelines.yml` |
| Secrets location | Settings → Secrets → Actions | Repository Settings → Repository variables |
| SSH key storage | In secrets (`SSH_PRIVATE_KEY_*`) | Repository Settings → SSH keys |
| Manual approval | Environment protection rules | `trigger: manual` in pipeline |
| Preprod trigger | Push to `stage` | Push to `stage` |
| Prod trigger | Push to `main` + approval | Push to `main` + manual click |
| **Stage build** | `npm run build:dev` → `dev_build/` | `npm run build:dev` → `dev_build/` |
| **Main build** | `npm run build` → `dist/` | `npm run build` → `dist/` |

### CI/CD Best Practices

- **Never commit secrets** - Use platform secrets/variables
- **Use dedicated SSH keys** - Don't reuse personal keys
- **No passphrase on CI keys** - CI can't enter passwords
- **Test locally first** - Verify SSH connection before pushing
- **Keep `package-lock.json`** - Ensures reproducible builds

### Stopping the Stacks

```bash
# Stop WordPress stack (preserves data)
docker compose down

# Stop Automation stack (preserves data)
docker compose -f docker-compose.n8n.yml down

# Stop with volume removal (DESTRUCTIVE)
docker compose down -v
docker compose -f docker-compose.n8n.yml down -v
```

## 🔧 Services and Infrastructure

The infrastructure is split into two separate Docker Compose files for independent lifecycle management:
- **`docker-compose.yml`**: WordPress stack (WordPress, MySQL, Redis)
- **`docker-compose.n8n.yml`**: Automation stack (n8n, PostgreSQL, Qdrant)

### WordPress
- **Port**: 8080
- **Themes**: Mounted from `wordpress/wp-content/themes/`
- **Configuration**: Via environment variables and `wp-config.php`
- **Database**: MySQL

### n8n (Automation)
- **Port**: `${N8N_PORT}` (default: 5678)
- **Database**: PostgreSQL
- **Volume**: `n8n-data` for workflow persistence
- **Integration**: Webhook reception from Contact Form 7

### MySQL
- **Role**: WordPress database
- **Configuration**: Credentials via `.env`
- **Volume**: `db-data`

### Redis
- **Port**: `${REDIS_PORT}`
- **Usage**: Cache, sessions, queues
- **Volume**: `redis-data`

### PostgreSQL
- **Role**: Database for n8n
- **Volume**: `postgres-data`
- **Configuration**: Automatic via Docker Compose

### Qdrant
- **Port**: `${QDRANT_PORT}` (default: 6333)
- **Role**: Vector database for AI
- **Volume**: `qdrant-data`
- **API**: REST and gRPC available

## 💡 Development Tips

### Local Development
- The `wordpress/` directory is mounted in the container → immediate modifications
- Use `docker compose exec wordpress bash` for WP-CLI or Composer in the container
- For Tiz theme: `npm run dev` activates watch mode + BrowserSync
- BrowserSync proxies WordPress on port 3000 with automatic reload

### Debugging and Logs
```bash
# Logs for WordPress stack
docker compose logs -f wordpress
docker compose logs -f db

# Logs for Automation stack
docker compose -f docker-compose.n8n.yml logs -f n8n
docker compose -f docker-compose.n8n.yml logs -f postgres

# Shell access to containers
docker compose exec wordpress bash
docker compose -f docker-compose.n8n.yml exec postgres bash
```

## 🎨 WordPress Themes

### Timber Starter Theme (Parent Theme)

**Location:** `wordpress/wp-content/themes/timber-starter-theme`

**Features:**
- **Parent theme** that integrates the Timber framework to use **Twig** in WordPress
- Allows integration of **additional PHP libraries** via **Composer**
- **Twig templates** for modern and secure syntax
- Clear separation between PHP logic (`src/`) and templates (`views/`)
- Integrated unit tests with PHPUnit
- Object-oriented architecture with custom classes

**What this theme provides:**
- **Twig templating**: Clear and secure syntax for templates
- **Structured context**: Data organization for views
- **Extensibility**: Easy integration of PHP libraries via Composer
- **Performance**: Twig template caching
- **Security**: Automatic protection against XSS vulnerabilities

**Structure:**
- `src/StarterSite.php`: Main theme class
- `views/`: Twig templates
- `static/`: Static assets
- `tests/`: PHPUnit tests
- `composer.json`: PHP dependencies (Timber + additional libraries)

**Development usage:**
```bash
cd wordpress/wp-content/themes/timber-starter-theme
composer install
```

### Tiz Theme (Child Theme)

**Location:** `wordpress/wp-content/themes/tiz`

**Features:**
- **Child theme** of Timber Starter Theme
- Inherits Timber/Twig functionality from parent theme
- Modern front-end stack with **Webpack 5**
- **Tailwind CSS 4** for styling
- **GSAP** for animations
- **BrowserSync** for real-time development
- Optimized build for production

**Parent/Child Relationship:**
- Automatically inherits all features from Timber parent theme
- Can override parent Twig templates if necessary
- Access to all PHP libraries installed via Composer in parent theme
- Combines Timber/Twig advantages with modern front-end workflow

**Advanced Features:**
- **Contact Form 7** integration with custom webhooks
- Custom Post Types system
- Conditional assets based on environment:
  - Development (`WP_ENV=development`): loads `dev_build/` (unminified, source maps)
  - Production (`WP_ENV=production`): loads `dist/` (optimized and minified)

**Available Scripts:**
```bash
npm run dev       # Development mode with watch and BrowserSync (local only)
npm run build     # Production build → outputs to dist/ (minified)
npm run build:dev # Pre-production build → outputs to dev_build/ (unminified)
```

**Asset Architecture:**
- **Local development**: `npm run dev` → Webpack watch + BrowserSync on port 3000
- **Pre-production (stage branch)**: CI runs `npm run build:dev` → `dev_build/` folder
- **Production (main branch)**: CI runs `npm run build` → `dist/` folder

## 🔗 Contact Form 7 ↔ n8n Integration

The Tiz theme includes a custom webhook system for Contact Form 7 that enables seamless integration with n8n.

### Features

**Custom Post Type "CF7 Webhooks":**
- Admin interface to map CF7 forms to webhook URLs
- Per-form configuration of n8n destination URL
- Automatic data sending management

**Integration Process:**
1. **Configuration**: In WordPress admin, create a "CF7 Webhook" and associate a CF7 form with an n8n URL
2. **Submission**: When the form is submitted, CF7 emails are disabled
3. **Webhook**: Data is automatically POSTed as JSON to n8n
4. **Response**: Success/failure status is displayed to the user

### n8n Configuration

1. **n8n Webhook**: Create an n8n workflow with a "Webhook" trigger
2. **Webhook URL**: Copy the generated URL into the "CF7 Webhooks" interface
3. **Processing**: Configure the n8n workflow to process received data
4. **Response**: n8n must respond with `{"ok": true}` to confirm reception

## 🗄️ Data Services for n8n

### PostgreSQL
- **Role**: Main database for n8n
- **Configuration**: Automatically configured via environment variables
- **Persistence**: Docker volume `postgres-data`

### Qdrant (Vector Database)
- **Role**: Vector storage and search for AI/ML
- **Port**: Configurable via `${QDRANT_PORT}` (default: 6333)
- **Usage**: Perfect for n8n workflows involving:
  - Semantic search
  - Recommendations
  - Text classification
  - RAG (Retrieval Augmented Generation)

**Example usage with n8n:**
- WordPress content embedding in Qdrant
- Semantic search for similar content
- Recommendation system based on user interactions

---

## 📁 Volumes and Persistence

The stack uses named Docker volumes for data persistence:

- `db-data`: MySQL data (WordPress)
- `postgres-data`: PostgreSQL data (n8n)
- `redis-data`: Redis cache
- `qdrant-data`: Qdrant collections and indexes
- `n8n-data`: n8n workflows and configuration

**⚠️ Warning:** Deleting volumes permanently erases persisted data.

```bash
# Stop and remove volumes (DESTRUCTIVE)
docker compose down -v

# Simple stop (preserves data)
docker compose down
```

## 🛠️ Troubleshooting

### Common Issues

**Ports already in use:**
- Check that ports defined in `.env` are free
- Modify ports in `.env` if necessary

**CI dependency failures:**
- Verify presence of `composer.lock` and `package-lock.json`
- Ensure cache directories are writable

**Container issues:**
```bash
# Check logs for a specific service
docker compose logs -f <service>

# Restart a specific service
docker compose restart <service>

# Rebuild images
docker compose build --no-cache
```

**CF7 → n8n webhooks:**
- Verify that n8n URL is accessible from WordPress
- Check WordPress logs: `/wp-content/debug.log`
- Verify that n8n responds with `{"ok": true}`

### n8n Debugging
- Web interface: http://localhost:5678
- Real-time logs: `docker compose logs -f n8n`
- Webhook testing: use n8n's built-in test tool

### Performance and Optimization
- **Redis**: Enable WordPress object cache
- **Qdrant**: Optimize collection size according to your data
- **MySQL**: Adjust `innodb_buffer_pool_size` for large volumes

## 📝 Technical Notes

### Security
- Change all default passwords in production
- Use HTTPS in production
- Limit access to service ports (PostgreSQL, Redis, etc.)

### Backup
- Regularly backup Docker volumes
- Export n8n workflows from the interface
- Backup WordPress database

### Monitoring
- Monitor Docker Compose logs
- Monitor container resource usage
- Regularly check n8n webhook health
