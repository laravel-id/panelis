# Panelis

Panelis is a ready-to-use CMS and administration panel built with [Laravel](https://laravel.com) and [Filament](https://filamentphp.com).

It provides a solid foundation for building content-driven applications, internal tools, directories, and custom business systems without starting from scratch.

![General Setting](https://res.cloudinary.com/bentang-nusantara/image/upload/v1717988251/rztab4nmtpjqixx5rwic.png)

## Features

Current built-in features include:

- User management
- Role and permission management
- Application settings
- Location management (country, region, district)
- Modular architecture for extending functionality

More modules and features are planned for future releases.

## Installation

Create a new Panelis project using Composer:

```bash
composer create-project panelis-php/skeleton my-project
```

Enter the project directory:

```bash
cd my-project
```

Configure your environment:

```bash
cp .env.example .env
php artisan key:generate
```

Run migrations and seeders:

```bash
php artisan migrate --seed
```

Start the development server:

```bash
php artisan serve
```

Visit:

```
http://localhost:8000
```

## Development Status

> [!WARNING]
> Panelis is currently under active development.
>
> Until the first stable release (`v1.0`), breaking changes may occur between releases without prior notice.
>
> We recommend using tagged releases and reviewing upgrade notes before updating existing projects.

## Contributing

Contributions, bug reports, and feature suggestions are welcome.

## License

Panelis is open-sourced software licensed under the MIT License.
