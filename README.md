# Project Overview

This repository contains a Docker-based WordPress stack with additional services (MySQL, Redis, PostgreSQL, Qdrant, and n8n) and two custom themes: `timber-starter-theme` and `tiz`. The project is ready for CI/CD on Bitbucket Pipelines.

## Requirements

- Docker Engine and Docker Compose plugin
- Node.js (optional, only if you want to build assets locally)
- Composer (optional, only if you want to manage PHP dependencies locally)

## Getting Started

1. Copy the environment file template:
   ```bash
   cp .env.example .env
   ```
2. Adjust `.env` values to match your local setup (set `WORDPRESS_ENV=development` for local work, `production` for releases).
3. Install theme dependencies locally:
   ```bash
   (cd wordpress/wp-content/themes/timber-starter-theme && composer install)
   (cd wordpress/wp-content/themes/tiz && npm install)
   ```
4. Start the stack:
   ```bash
   docker compose up -d
   ```
5. Access WordPress at http://localhost:8080.
6. n8n is available at http://localhost:${N8N_PORT:-5678}.

### Stopping the Stack

```bash
docker compose down
```
Use `docker compose down -v` if you also want to remove named volumes.

## Services

- **WordPress**: served on port 8080 with themes mounted from `wordpress/wp-content/themes/`.
- **MySQL**: stores WordPress data, credentials configured via `.env`.
- **Redis**: available on `${REDIS_PORT}` for caching or queue usage.
- **PostgreSQL**: provides the database for n8n.
- **Qdrant**: vector database exposed on `${QDRANT_PORT}`.
- **n8n**: workflow automation accessible on `${N8N_PORT}` and configured to use PostgreSQL.

## Themes

### timber-starter-theme

- Located in `wordpress/wp-content/themes/timber-starter-theme`.
- Uses Composer to manage PHP dependencies.
- Timber-based starter with Twig templates in `views/` and PHP logic in `src/`.
- Run dependencies in CI/CD via `composer install --prefer-dist --no-dev --optimize-autoloader`.

### tiz

- Located in `wordpress/wp-content/themes/tiz`.
- Modern front-end stack with Webpack, Tailwind CSS, and custom JS modules.
- `WORDPRESS_ENV=development` makes the theme load assets from `dev_build/`, so keep `npm run dev` running locally to regenerate CSS/JS.
- `WORDPRESS_ENV=production` switches asset loading to the optimized files in `dist/`, generated with `npm run build`.
- Build process uses `npm run build` to compile production assets.

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

## Volumes

The stack uses named volumes for persistence:
- `db-data` for MySQL
- `redis-data` for Redis
- `postgres-data` for PostgreSQL
- `qdrant-data` for Qdrant
- `n8n-data` for n8n configuration

Remove volumes with caution since this clears persisted data.

## Troubleshooting

- Ensure ports defined in `.env` are free on your host.
- If dependencies fail in CI, verify cache directories are writable and lockfiles exist (`composer.lock`, `package-lock.json` or `npm-shrinkwrap.json`).
- Check container logs with `docker compose logs -f <service>` for runtime issues.
