# AppMed - Medical Document Management

## Description

AppMed is a web application built with Laravel 11, Livewire 3, and AlpineJS for managing medical educational documents. It features a dynamic PDF viewer with Turn.js integration and offers both grid and list views.

## Features

-   Multi-role authentication (Admin/Teacher/Student)
-   Document organization by level, course, and semester
-   Interactive PDF viewer with Turn.js
-   Grid/List view toggle
-   Advanced search and filtering
-   Secure document downloads
-   Usage statistics

## Tech Stack

-   Laravel 11
-   Livewire 3
-   Alpine.js
-   Tailwind CSS
-   Turn.js
-   PDF.js
-   MySQL

## Installation

```bash
# Clone repository
git clone https://github.com/GasyCoder/appmed.git
cd appmed

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database migration
php artisan migrate
php artisan db:seed

# Compile assets
npm run dev

# Start server
php artisan serve
```
