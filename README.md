> # Murugo Real Estate Platform

Welcome to the redesigned Murugo Real Estate Platform, a modern, minimalistic, and user-friendly application for finding and listing rental properties in Rwanda. This project is a complete overhaul of the original application, featuring an Anthropic-inspired design, a robust backend, and a comprehensive internationalization framework.

## Table of Contents

1.  [Project Overview](#project-overview)
2.  [Features](#features)
3.  [Tech Stack](#tech-stack)
4.  [Getting Started](#getting-started)
5.  [Design System](#design-system)
6.  [Internationalization](#internationalization)
7.  [Deployment](#deployment)
8.  [Contributing](#contributing)

---

## 1. Project Overview

The Murugo platform is designed to connect renters with landlords in Rwanda, providing a seamless and efficient experience for both parties. This redesigned version focuses on:

-   **Minimalistic UI/UX**: A clean, modern, and intuitive interface inspired by Anthropic and Webflow design principles.
-   **Robust Backend**: A reliable and scalable backend built with Laravel and PostgreSQL.
-   **Internationalization**: Support for multiple languages, including English, French, and Kinyarwanda.
-   **Comprehensive Documentation**: Detailed guides for setup, deployment, and maintenance.

## 2. Features

-   **User Roles**: Separate registration and dashboards for Renters and Landlords.
-   **Property Listings**: Landlords can list properties with detailed information, including photos, amenities, and location.
-   **Advanced Search**: Renters can search for properties using various filters, such as location, property type, and price.
-   **Map Search**: A visual map-based search for properties.
-   **Favorites**: Renters can save their favorite properties for later viewing.
-   **Messaging System**: Direct and secure communication between renters and landlords.
-   **Internationalization**: The platform is available in English, French, and Kinyarwanda.

## 3. Tech Stack

-   **Backend**: Laravel 10, PHP 8.2
-   **Frontend**: Blade, Tailwind CSS, Alpine.js
-   **Database**: PostgreSQL 14
-   **Web Server**: Nginx
-   **Deployment**: Ubuntu 22.04, Supervisor

## 4. Getting Started

### Prerequisites

-   PHP 8.2
-   Composer
-   Node.js & npm
-   PostgreSQL

### Installation

1.  **Clone the repository:**

    ```bash
    git clone https://github.com/Ikaze-Murugo/miniature-den.git
    cd miniature-den
    ```

2.  **Install dependencies:**

    ```bash
    composer install
    npm install
    ```

3.  **Set up the environment:**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Configure your `.env` file** with your database credentials and other settings.

5.  **Run database migrations and seeders:**

    ```bash
    php artisan migrate --seed
    ```

6.  **Build frontend assets:**

    ```bash
    npm run dev
    ```

7.  **Start the development server:**

    ```bash
    php artisan serve
    ```

## 5. Design System

A new design system has been implemented to create a modern and minimalistic user interface. The design principles, color palette, typography, and component styles are documented in the `NEW_DESIGN_SYSTEM.md` file.

## 6. Internationalization

The platform supports multiple languages. For detailed instructions on how to add new languages or modify existing translations, please refer to the `INTERNATIONALIZATION_GUIDE.md` file.

## 7. Deployment

A comprehensive deployment guide is available in the `DEPLOYMENT_GUIDE.md` file. It covers the entire process of deploying the application on a Linux server with Nginx, PostgreSQL, and Supervisor.

## 8. Contributing

Contributions are welcome! Please feel free to submit a pull request or open an issue for any bugs or feature requests.

