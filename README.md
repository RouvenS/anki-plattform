# Anki Plattform - Build the Best Vocabulary Ever

**Mission:** To create the ultimate vocabulary learning experience by seamlessly integrating with Anki and leveraging the power of AI.

**This is a work in progress.** This document outlines the project's vision, current state, and future plans. We are actively seeking contributors to help us achieve our mission.

## Vision & Future

The long-term vision for the Anki Plattform is to become an indispensable tool for language learners. We want to move beyond simple flashcard generation and create a fully integrated learning ecosystem.

### Key Future Goals:

*   **Direct Anki Account Management:** Eliminate the need for the AnkiConnect plugin by providing a secure client for each user to manage their Anki account directly from our platform. This will allow for a seamless experience across all devices.
*   **Automated & Personalized Learning:** Leverage AI to create personalized learning plans for each user. The platform will automatically introduce new vocabulary based on the user's learning goals and progress, and nudge them to study at the optimal time.
*   **AI-Powered Content Generation:** Go beyond simple translations and provide rich, contextual content for each vocabulary word. This includes example sentences, conjugations, and even entire phrases for specific situations (e.g., "vocabulary for a business dinner").
*   **Full Card Customization:** Allow users to fully customize their flashcards with additional examples, images, and personal notes.

For a more detailed breakdown of our future plans, please see our [ROADMAP.md](ROADMAP.md).

## Current State & Critical Analysis

The Anki Plattform is currently in the **alpha stage**. The core functionality is in place, but there are several areas that need improvement before it is ready for production.

*   **Anki Integration:** The current integration relies on the AnkiConnect plugin, which requires users to have Anki running on their local machine. This is a significant barrier to entry and a major focus for future development.
*   **Testing:** The project currently lacks a comprehensive test suite. The existing tests are the default Laravel example tests. This is a critical issue that needs to be addressed to ensure the stability and reliability of the application.
*   **User Experience:** The UI is functional but could be improved to provide a more intuitive and engaging user experience.
*   **Security:** The application has not yet undergone a formal security audit.

## Core Features

Despite its early stage, the Anki Plattform already offers several powerful features:

*   **AI-Powered Flashcard Generation:** Simply provide a list of vocabulary words, and the platform will use an LLM to generate translations, example sentences, and text-to-speech audio.
*   **Batch Management:** Organize your flashcards into batches for easy management and export to Anki.
*   **Customizable Prompts:** Tailor the AI-generated content to your specific needs by creating and saving custom prompts.

## To-Do List for Production Readiness

*   [ ] **Implement direct Anki account management:** This is the highest priority task.
*   [ ] **Develop a comprehensive test suite:** We need to achieve at least 80% code coverage.
*   [ ] **Conduct a security audit:** Ensure the application is secure and user data is protected.
*   [ ] **Improve the user interface:** Redesign the UI to be more user-friendly and intuitive.
*   [ ] **Implement a robust error handling and logging system.**
*   [ ] **Create a CI/CD pipeline for automated testing and deployment.**

We welcome contributions to help us tackle these tasks. Please see our [CONTRIBUTING.md](CONTRIBUTING.md) for more information.

## Installation & Setup (for Development)

**Note:** This project is not yet ready for production use. The following instructions are for setting up a local development environment.

### System Requirements

*   PHP 8.2+
*   Composer
*   Node.js & npm
*   PostgreSQL (or another database of your choice)

### Step-by-step Setup Guide

1.  **Clone the repository:**

    ```bash
    git clone https://github.com/your-username/anki-plattform.git
    cd anki-plattform
    ```

2.  **Install dependencies:**

    ```bash
    composer install
    npm install
    ```

3.  **Create your environment file:**

    ```bash
    cp .env.example .env
    ```

4.  **Generate an application key:**

    ```bash
    php artisan key:generate
    ```

5.  **Configure your `.env` file:**

    *   Set your database credentials.
    *   Add your `OPENAI_API_KEY`.

6.  **Run database migrations:**

    ```bash
    php artisan migrate
    ```

7.  **Build frontend assets:**

    ```bash
    npm run dev
    ```

8.  **Run the development server:**

    ```bash
    php artisan serve
    ```

## Architecture & Design Patterns

The application follows a standard Laravel MVC architecture. Key design patterns include:

*   **Queued Jobs:** For handling long-running tasks like AI-powered content generation.
*   **Policies:** For authorization and access control.

## Testing

**Critical Issue:** The project currently lacks a meaningful test suite. The existing tests are the default Laravel examples. We are actively seeking contributors to help us build out our test coverage.

To run the existing tests, you can use the following command:

```bash
php artisan test
```

## Deployment

**This project is not yet ready for production deployment.**

## Appendices

### API Documentation

The application's routes are defined in `routes/web.php`. A detailed API documentation will be created as the project matures.