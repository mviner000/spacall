# Laravel Backend Project

A minimal Laravel backend setup with only the essential components.

## 🚀 Quick Start

### Prerequisites

- Ubuntu 24.04 (or similar Linux distribution)
- Basic terminal knowledge

### Setup Instructions

#### 1. Install Requirements

First, install all necessary dependencies (PHP, Composer, SQLite, Node.js):

```bash
chmod +x install-laravel-requirements.sh
./install-laravel-requirements.sh
```

This will install:
- PHP 8.3 with required extensions
- Composer (PHP package manager)
- SQLite database
- Node.js (optional, for frontend tools)

#### 2. Create the Backend Project

Run the setup script to create a minimal Laravel backend:

```bash
chmod +x create-backend.sh
./create-backend.sh
```

This script will:
- Create a new Laravel project named `backend`
- Remove unnecessary files (tests, frontend assets, config files)
- Set up minimal `.env` configuration with SQLite
- Generate application key
- Create SQLite database file

#### 3. Start the Development Server

```bash
cd backend
php artisan serve
```

The application will be available at: **http://localhost:8000**

## 📁 Project Structure

```
laravel_proj/
├── backend/                          # Laravel application
├── create-backend.sh                 # Setup script
├── install-laravel-requirements.sh   # Requirements installer
└── README.md                         # This file
```

## 🔧 Configuration

The project uses minimal configuration:
- **Database**: SQLite (no MySQL required)
- **Cache**: Array driver (in-memory)
- **Session**: Array driver
- **Queue**: Sync (no queue worker needed)

## 📝 Git Commit Guide

Follow these commit message prefixes to keep our git history clean and organized!

### Common Commit Types

| Prefix   | Meaning                                                                 | Example |
|----------|-------------------------------------------------------------------------|---------|
| **feat:**    | Introduces a new feature                                               | `feat: add user profile page` |
| **fix:**     | Fixes a bug                                                            | `fix: resolve login redirect issue` |
| **docs:**    | Documentation changes only                                             | `docs: update README with setup steps` |
| **style:**   | Code style changes (formatting, missing semicolons, no logic changes) | `style: format dashboard layout` |
| **refactor:**| Rewriting code without altering behavior                               | `refactor: simplify auth logic` |
| **perf:**    | Performance improvements                                               | `perf: optimize database queries` |
| **test:**    | Adding or updating tests only                                          | `test: add unit tests for auth` |
| **build:**   | Changes to build system, dependencies, or CI pipelines                 | `build: update next.js to v14` |
| **ci:**      | CI configuration or scripts                                            | `ci: add github actions workflow` |
| **chore:**   | Maintenance tasks (e.g., cleaning files, bumps), no production code    | `chore: clean up unused imports` |
| **revert:**  | Reverts a previous commit                                              | `revert: undo feature X` |

## 🛠️ Common Commands

```bash
# Start development server
php artisan serve

# Run migrations
php artisan migrate

# Clear cache
php artisan cache:clear
php artisan config:clear

# View routes
php artisan route:list
```

## 📚 Documentation

- [Laravel Documentation](https://laravel.com/docs)
- [PHP Documentation](https://www.php.net/docs.php)

## 🤝 Contributing

1. Create a new branch for your feature
2. Follow the git commit guide above
3. Submit a pull request

## 📄 License

This project is open-sourced software licensed under the MIT license.