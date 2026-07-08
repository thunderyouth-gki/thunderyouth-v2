# Thunder Youth

Thunder Youth is an application built to ease the operational and administrative related activities of church for the youth department, such as monitoring attendance of community members, managing their information, and providing a platform for announcements and communication.

## Features

- **Member Management**: Track and manage community members' profiles and information.
- **Attendance Monitoring**: Easily record and monitor attendance for events and weekly services.
- **Role-Based Access Control**: Secure sections of the application with specific permissions (e.g., admin, leaders).

## Tech Stack

- **Backend**: Laravel (PHP)
- **Frontend**: Livewire, Alpine.js, Tailwind CSS (via Vite), Flux UI
- **Database**: SQLite (Default) / MySQL / PostgreSQL

## Prerequisites

Before setting up the project locally, ensure your machine meets the following requirements:

- **PHP** >= 8.4 (configured with standard extensions: `sqlite3`, `pdo_sqlite`, `openssl`, `mbstring`, `xml`, etc.)
- **Composer** (PHP Package Manager)
- **Node.js** & **NPM** (Javascript Package Manager)

## Local Setup

To configure the application on your local machine, follow these steps:

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd thunderyouth-v2
   ```

2. **Run the Setup Script:**
   The project includes a comprehensive Composer script that automates the initial setup. This script installs Composer and NPM dependencies, copies `.env.example` to `.env`, generates the application key, runs database migrations, and builds frontend assets:
   ```bash
   composer run setup
   ```
   *Note: The project defaults to a local **SQLite** database (`database/database.sqlite`). If you prefer to use another database system like **MySQL** or **PostgreSQL**, configure the `DB_*` connection details in your `.env` file before running the setup or migrations.*

3. **Start the Development Environment:**
   Run the following command to start the Laravel local server, queue listener, and Vite asset bundler concurrently:
   ```bash
   composer run dev
   ```
   Once started, visit the application in your browser at [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Managing Packages

Whenever you pull new changes from the repository (e.g., via `git pull`), you might need to install or update dependencies if `composer.json` or `package.json` have changed:

- **PHP Packages (Composer)**: 
  ```bash
  composer install
  ```
- **Node Packages (NPM)**:
  ```bash
  npm install
  npm run build # or npm run dev
  ```

## Managing the Database

Laravel uses migrations to manage database schema changes. Here are common commands you will use:

- **Run outstanding migrations**:
  ```bash
  php artisan migrate
  ```
- **Rollback the last database migration**:
  ```bash
  php artisan migrate:rollback
  ```
- **Reset the database and run all migrations from scratch**:
  ```bash
  php artisan migrate:fresh
  ```
- **Seed the database with test data (if available)**:
  ```bash
  php artisan db:seed
  # Or combined with fresh migration:
  php artisan migrate:fresh --seed
  ```

## Available Composer Scripts

The following helper commands are available in [composer.json](file:///c:/laragon/www/thunderyouth-v2/composer.json):

- **Start Local Servers**: `composer run dev`
- **Format Code (Laravel Pint)**: `composer run lint`
- **Verify Lint Rules**: `composer run lint:check`
- **Run Static Analysis (PHPStan)**: `composer run types:check`
- **Run Complete Test Suite**: `composer run test` (Clears configuration, runs linter checks, performs static analysis, and runs Pest tests)
- **Run Tests Only**: `php artisan test` or `vendor/bin/pest`

## Production Deployment

When deploying to a production environment (such as a VPS or Laravel Forge), you need to optimize the application for performance and security:

1. **Install Dependencies (No Dev)**:
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install
   npm run build
   ```

2. **Configure Environment variables (`.env`)**:
   Ensure `APP_ENV=production` and `APP_DEBUG=false` are set. Update your `DB_*` credentials to your production database.

3. **Run Migrations**:
   ```bash
   php artisan migrate --force
   ```

4. **Cache Configuration and Routes**:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

5. **Storage Link**:
   If your application manages file uploads, ensure the storage directory is linked:
   ```bash
   php artisan storage:link
   ```
