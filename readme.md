# 🚗 Car Project IO

A modern Laravel + React application for managing car projects and modifications, built with the latest technologies and best practices.

## ✨ Features

- **Modern Stack**: Laravel 12 + React 19 + TypeScript + Tailwind CSS 4 + Shadcn UI (Laravel React Starter Kit)
- **Real-time Monitoring**: Laravel Horizon, Pulse, and Telescope integration
- **API Documentation**: OpenAPI/Swagger specification
- **Background Processing**: Queue jobs and events for data processing
- **Activity Logging**: Comprehensive activity tracking with Spatie
- **Code Quality**: PHPStan, Laravel Pint, ESLint, Prettier, and comprehensive testing with Pest
- **Development Tools**: Laravel Boost integration for enhanced development experience

## 🛠️ Tech Stack

### Backend
- **Laravel 12** - PHP Framework
- **PHP 8.4.12** - Latest PHP version
- **MySQL 8.4.2** - Database
- **Laravel Horizon** - Queue monitoring
- **Laravel Pulse** - Application monitoring
- **Laravel Telescope** - Debug assistant
- **Laravel Sanctum** - API authentication
- **Laravel Octane** - Application server
- **Spatie Activity Log** - Activity tracking
- **Spatie Query Builder** - API query building
- **Nuno Maduro Essentials** - Essential Settings

### Frontend
- **React 19** - UI Library
- **TypeScript** - Type safety
- **Inertia.js v2** - SPA framework
- **Tailwind CSS 4** - Styling
- **Shadcn UI** - Theme management

### Development Tools
- **Laravel Boost** - Enhanced development experience
- **Pest** - Testing framework
- **PHPStan** - Static analysis
- **Laravel Pint** - Code formatting
- **ESLint + Prettier** - Code linting and formatting
- **Laravel IDE Helper** - IDE support

## 🚀 Quick Start

### Prerequisites

- PHP 8.4+
- Node.js 22+
- MySQL 8.4+
- Composer

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd car-project-io
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan storage:link
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

6. **Start development server**
   ```bash
   composer dev
   ```

## 🏗️ Development Environment

### Tested Configuration
- **Hardware**: MacBook Pro 14" M3 Pro
- **Local Server**: Laravel Herd with SSL enabled
- **Domain**: `car-project-io.test`
- **Database**: MySQL 8.4.2 via DBngin
- **IDE**: Cursor with Laravel Boost

### Development Commands

```bash
# Development server (with queue, logs, and Vite)
composer dev

# Development with Herd
composer dev:herd

# Run tests
php artisan test

# Code quality
composer stan          # PHPStan analysis
composer pint          # Code formatting
npm run lint           # ESLint
npm run format         # Prettier

# Documentation
composer swagger       # Generate OpenAPI docs
composer ide-helper    # Generate IDE helpers
```

## 📁 Project Structure

Beyond Laravel's default structure, this project includes:

```
app/
├── Actions/           # Action classes for business logic
├── Repositories/      # Data access layer
├── Services/          # Service layer (planned)
├── Policies/          # Authorization policies
└── Rules/             # Custom validation rules

resources/js/
├── components/        # Reusable React components
├── pages/            # Inertia.js pages
├── layouts/          # Page layouts
├── hooks/            # Custom React hooks
└── types/            # TypeScript definitions
```

## 🔧 Available Artisan Commands

```bash
# Create custom classes
php artisan make:action [Name]      # Action class
php artisan make:repository [Name]  # Repository class
```

## 📊 Monitoring & Debugging

- **Laravel Horizon**: Queue monitoring at `/horizon`
- **Laravel Pulse**: Application metrics at `/pulse`
- **Laravel Telescope**: Debug assistant at `/telescope`
- **API Documentation**: Swagger UI at `/api/documentation`


## 🚧 Roadmap

### Planned Features
- [ ] **End-to-End Testing** - Browser testing with Pest
- [ ] **Laravel Modules** - Modular architecture
- [ ] **Service Layer** - Enhanced business logic organization
- [ ] **Background Jobs** - Queue processing for heavy operations
- [ ] **Event System** - Event-driven architecture

### Considered Packages
- Sentry (error tracking)
- Spatie Laravel PDF (PDF generation)
- Hammerstone Sidecar (serverless functions)
- Laravel Cashier (subscription billing)
- Laravel Scout (search)
- Laravel Socialite (OAuth)
- Laravel Reverb (WebSockets)
- Laravel Echo (real-time events)
