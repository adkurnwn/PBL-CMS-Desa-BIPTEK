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

-   **Backend**: Laravel (PHP Framework)
-   **Frontend**: Vite + Tailwind CSS
-   **Database**: MySQL/PostgreSQL (configurable)
-   **Testing**: PHPUnit
-   **Development**: Laragon (Windows development environment)

## Installation

### Prerequisites

-   PHP >= 8.1
-   Composer
-   Node.js & npm
-   MySQL/PostgreSQL database
-   Web server (Apache/Nginx)

### Setup Steps

1. **Clone the repository**

    ```bash
    git clone <repository-url>
    cd PBL-CMS-Desa-BIPTEK
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Install Node.js dependencies**

    ```bash
    npm install
    ```

4. **Environment configuration**

    ```bash
    cp .env.example .env
    ```

    Edit `.env` file with your database credentials and application settings.

5. **Generate application key**

    ```bash
    php artisan key:generate
    ```

6. **Run database migrations**

    ```bash
    php artisan migrate
    ```

7. **Seed database (optional)**

    ```bash
    php artisan db:seed
    ```

8. **Build frontend assets**
    ```bash
    npm run build
    ```

## Development

### Running the Application

1. **Start the Laravel development server**

    ```bash
    php artisan serve
    ```

2. **Watch frontend assets (in separate terminal)**
    ```bash
    npm run dev
    ```

The application will be available at `http://localhost:8000`.

### Testing

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
