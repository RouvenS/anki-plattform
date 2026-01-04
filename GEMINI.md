# GEMINI.md

> **Context & Credentials**
>
> We use **Laravel 12**, **Tailwind CSS ^4.1**, and **Vite 7**.
> Please keep these in mind when giving recommendations or code examples.
>
> **ChromeDevMCP Tools Login Credentials:**
> - Email: `rouven.schuessler1@googlemail.com`
> - Password: `12345678`

---

# Anki Platform - Developer Guide

This document serves as the primary context and guide for working on the Anki Platform project. It outlines the architecture, setup procedures, and development conventions.

## 1. Project Overview

The Anki Platform is a Laravel-based web application designed to help users generate and manage flashcards for Anki. It leverages AI (via OpenAI) to generate content and integrates with Anki through generated notes.

**Key Technologies:**
-   **Backend:** Laravel 12 (PHP 8.2+)
-   **Frontend:** Blade Templates, Tailwind CSS v4, Alpine.js
-   **Build Tool:** Vite 7
-   **Database:** PostgreSQL (Production) / SQLite (Local Default)
-   **Queue:** Database driver (for generating flashcards/audio)

## 2. Getting Started

### Prerequisites
-   PHP >= 8.2
-   Composer
-   Node.js & npm

### Installation
1.  **Clone & Install Dependencies:**
    ```bash
    composer install
    npm install
    ```

2.  **Environment Setup:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Configure your database and `OPENAI_API_KEY` in `.env`.*

3.  **Database Migration:**
    ```bash
    php artisan migrate
    ```

### Running the Application

The project includes a convenient `composer` script to run all necessary processes (Server, Queue, Vite) simultaneously.

**Start Development Environment:**
```bash
composer dev
```
*This runs `php artisan serve`, `php artisan queue:listen`, and `npm run dev` in parallel.*

**Alternative (Manual):**
-   Server: `php artisan serve`
-   Assets: `npm run dev`
-   Queue: `php artisan queue:listen`

## 3. Architecture & Structure

The project follows standard Laravel conventions.

### Key Directories
-   **`app/Http/Controllers/`**: Contains core logic.
    -   `CardController`: Flashcard CRUD and Anki export logic.
    -   `BatchController`: Manages collections of cards.
    -   `PromptController`: Manages AI prompts for card generation.
    -   `SettingsController`: User profile and preference management.
-   **`app/Jobs/`**: Queueable jobs for background processing.
    -   `GenerateFlashcards`: Handles API calls to OpenAI.
    -   `GenerateTts`: Handles Text-to-Speech generation.
-   **`resources/views/`**: Blade templates.
    -   `layouts/app.blade.php`: Main application layout.
    -   `components/`: Reusable UI components.
-   **`routes/web.php`**: Application routes.

### Database Schema
Key tables managed via migrations in `database/migrations/`:
-   `users`: App users.
-   `batches`: Groups of flashcards.
-   `cards`: Individual flashcards (linked to batches).
-   `prompts`: Custom prompts for AI generation.

## 4. Development Conventions

-   **Styling:** Use **Tailwind CSS** utility classes directly in Blade views. Avoid custom CSS files unless absolutely necessary.
-   **Interactivity:** Use **Alpine.js** for client-side interactions (modals, dropdowns, simple state).
-   **Testing:** Run tests using standard Artisan commands:
    ```bash
    php artisan test
    ```
-   **Code Style:** Follow standard PSR-12 and Laravel naming conventions.
