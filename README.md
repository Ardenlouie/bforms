# Laravel 12 Starter

<p align="center">
  <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/badge/laravel-12.x-brightgreen.svg" alt="Laravel Version"></a>
  <a href="https://packagist.org/packages/php"><img src="https://img.shields.io/badge/php-%3E%3D8.2-8892BF.svg" alt="PHP Version"></a>
  <a href="LICENSE"><img src="https://img.shields.io/badge/license-MIT-blue.svg" alt="License"></a>
</p>

A robust and feature-rich Laravel 12 boilerplate for rapid project setup. This repository is pre-configured with essential tools to accelerate your development workflow and get you building your application's core functionality right away.

## ✨ What's Included?

This starter kit includes a suite of powerful features and packages right out of the box:

-   **Authentication:** A complete and secure user login, registration, and password management system (e.g., using Laravel Breeze).
-   **Roles & Permissions:** Easily define and assign user roles to control access (e.g., using `spatie/laravel-permission`).
-   **Activity Logs:** Track user actions and system events for monitoring and auditing (e.g., using `spatie/laravel-activitylog`).
-   **Developer-Friendly Utilities:** A collection of helpers to simplify common development tasks.
-   **Frontend:** Built with Vite, Tailwind CSS, and Alpine.js for a modern and fast frontend experience.
-   **Code Quality:** Pre-configured with tools to enforce code style and catch bugs early.

## 💻 Prerequisites

Before you begin, ensure you have the following installed on your system:

-   PHP >= 8.4
-   Composer
-   Node.js & npm
-   A database (MySQL, PostgreSQL, SQLite, etc.)

## 🚀 Getting Started

Follow these steps to get the project up and running on your local machine.

### 1. Clone the repository

```bash
git clone https://github.com/bevi-webdev-jm/laravel-10-starter.git <app-name>
```

2. Navigate to the project directory
   Change into the new project directory:

```bash
    cd <app-name>
```

3. Install dependencies
   Install the project's backend dependencies using Composer and frontend dependencies using npm.

```bash
    composer install
    npm install
```

4. Environment configuration
   Create your .env file by copying the contents from the provided example file:

```bash
    cp .env.example .env
```

Next, open the newly created .env file and update the database connection details to match your local environment.

5. Run migrations and seed the database
   Execute the following command to create the database tables and populate them with essential seed data:

```bash
    php artisan migrate:fresh --seed
```

6. Start the development server
   Start the built-in PHP development server to run the application locally:

```bash
    php artisan serve
```

7. Access the application
   The application should now be accessible in your web browser at the following URL:

```bash
    http://127.0.0.1:8000/
```

📄 License
This project is open-source and licensed under the MIT License.
