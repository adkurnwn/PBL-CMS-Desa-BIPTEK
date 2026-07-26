# PBL CMS Desa BIPTEK

<p align="center">
<img src="https://laravel.com/img/logomark.min.svg" width="100" alt="Laravel Logo">
</p>

<p align="center">
<strong>Village Content Management System</strong><br>
A Laravel-based CMS solution for village administration and information management.
</p>

## About This Project

PBL CMS Desa BIPTEK is a comprehensive content management system designed specifically for village administration. Built with Laravel, this system provides tools for managing village information, news, and administrative content with an intuitive interface.

### Key Features

-   **News Management**: Create, edit, and publish village news and announcements
-   **User Management**: Role-based access control for administrators and content creators
-   **Content Organization**: Structured content management for village information
-   **Responsive Design**: Built with Tailwind CSS for mobile-friendly interfaces
-   **Modern Architecture**: Laravel framework with Vite for fast development

## Technology Stack

-   **Backend**: Laravel 11 (PHP Framework)
-   **Frontend**: Vue 3 + Inertia.js (SPA) + Tailwind CSS + Vite
-   **Database**: MySQL / MariaDB / PostgreSQL
-   **Testing**: PHPUnit
-   **Development**: Laragon / Herd / Docker (Laravel Sail)

## Installation & Setup

### Prerequisites

-   **PHP >= 8.2** (Required by Laravel 11)
-   **Composer**
-   **Node.js & npm** (v18+ recommended)
-   **MySQL or PostgreSQL Database**
-   **PHP Extensions**: `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `curl`, `gd`, `zip`

### Setup Steps

1. **Clone the Repository**

    ```bash
    git clone <repository-url>
    cd PBL-CMS-Desa-BIPTEK
    ```

2. **Install PHP Dependencies**

    ```bash
    composer install
    ```

3. **Install Frontend Dependencies**

    ```bash
    npm install
    ```

4. **Environment Configuration**

    Copy the example environment file to create your `.env` file:

    ```bash
    cp .env.example .env
    ```

    By default, the `.env` file will have `APP_INSTALLED=false`.

5. **Generate Application Key**

    ```bash
    php artisan key:generate
    ```

---

### Option A: Web-Based Installation Wizard (Recommended)

This project features a web installer that automates database setup, migrations, default seeding, and initial profile/admin configuration.

1. **Start the local servers**
   - Run the Vite dev server (frontend):
     ```bash
     npm run dev
     ```
   - Start the Laravel local server:
     ```bash
     php artisan serve
     ```
2. **Access the Installer**
   - Open your browser and navigate to `http://localhost:8000`.
   - The application will automatically redirect you to the **Installation Wizard** (`/install`).
   - Follow the steps to:
     - Check system requirements & directory permissions.
     - Configure your database credentials (the installer will automatically run migrations and core seeders like `IndoRegionSeeder` for Indonesian administrative regions).
     - Set up the Administrator account.
     - Configure the initial Village Profile (logo, name, regions, vision, and mission).
   - Once completed, you will be redirected to the `/login` page.

---

### Option B: Manual Installation (Developers)

If you prefer to set up the system manually via the CLI, follow these steps:

1. **Configure `.env`**
   - Open `.env` and fill in your database credentials (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
   - Change `APP_INSTALLED=false` to `APP_INSTALLED=true` in your `.env`.

2. **Run Migrations & Seeders**
   - Run the migration and seeders in order (including the Indonesian regions data which might take a minute or two):
     ```bash
     php artisan migrate:fresh
     php artisan db:seed --class=IndoRegionSeeder
     php artisan db:seed --class=RoleSeeder
     php artisan db:seed --class=PermissionSeeder
     php artisan db:seed --class=UserSeeder
     php artisan db:seed --class=ProfileSeeder
     php artisan db:seed --class=DatabaseSeeder
     ```

3. **Link Public Storage**
     ```bash
     php artisan storage:link
     ```

4. **Run the Application**
   - Run Vite development server:
     ```bash
     npm run dev
     ```
   - Run Laravel server:
     ```bash
     php artisan serve
     ```
   - Access the application at `http://localhost:8000`.
   - Use the seeded default credentials to log in:
     - **Admin**: `admin@gmail.com` | Password: `password`
     - **Kepala Desa**: `kepala@gmail.com` | Password: `password`
     - **Sekretaris Desa**: `sekretaris@gmail.com` | Password: `password`
     - **Bendahara Desa**: `bendahara@gmail.com` | Password: `password`

---

## Testing

Run the test suite:

```bash
php artisan test
```

Or using PHPUnit directly:

```bash
./vendor/bin/phpunit
```

## Project Structure

```
app/
├── Console/         # Artisan commands
├── Helpers/         # Helper functions
├── Http/
│   └── Resources/   # API resources (e.g., NewsPreview)
├── Mail/           # Mail classes
├── Models/         # Eloquent models
├── Providers/      # Service providers
└── Services/       # Business logic services

config/             # Configuration files
database/           # Migrations, seeders, factories
resources/          # Views, assets, language files
routes/             # Route definitions
tests/              # Test files
```

## API Resources

The project includes structured API resources for data transformation:

-   **News Management**: [`NewsPreview`](app/Http/Resources/News/NewsPreview.php) resource for news data formatting

## Configuration

Key configuration files:

-   [`.env`](.env) - Environment variables
-   [`config/app.php`](config/app.php) - Application settings
-   [`config/database.php`](config/database.php) - Database configuration
-   [`tailwind.config.js`](tailwind.config.js) - Tailwind CSS configuration
-   [`vite.config.js`](vite.config.js) - Vite build configuration

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Create a Pull Request

## Support

For support and questions, please contact the development team or create an issue in the repository.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

**Developed for Village Administration Management**
