# Thunder Youth

Thunder Youth is an application built to ease the operational and administrative related activities of church for the youth department, such as monitoring attendance of community members, managing their information, and providing a platform for announcements and communication.

## Prerequisites

Before setting up the project locally, ensure your machine meets the following requirements:

- **PHP** >= 8.4 (configured with standard extensions: `sqlite3`, `pdo_sqlite`, `openssl`, `mbstring`, `xml`, etc.)
- **Composer**
- **Node.js** & **NPM**

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

## Available Composer Scripts

The following helper commands are available in [composer.json](file:///c:/laragon/www/thunderyouth-v2/composer.json):

- **Start Local Servers**: `composer run dev`
- **Format Code (Laravel Pint)**: `composer run lint`
- **Verify Lint Rules**: `composer run lint:check`
- **Run Static Analysis (PHPStan)**: `composer run types:check`
- **Run Complete Test Suite**: `composer run test` (Clears configuration, runs linter checks, performs static analysis, and runs Pest tests)
- **Run Tests Only**: `php artisan test` or `vendor/bin/pest`
