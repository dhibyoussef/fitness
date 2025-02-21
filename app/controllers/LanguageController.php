<?php
require_once __DIR__ . '/../controllers/BaseController.php';

class LanguageController extends BaseController {
    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }

    public function changeLanguage(): void {
        if (!$this->isPostRequest()) {
            return $this->handleInvalidRequest();
        }

        if (!$this->isUserAuthenticated()) {
            return $this->handleUnauthorizedAccess();
        }

        $csrfToken = $this->getPostParam('csrf_token');
        if (!$this->isValidCsrfToken($csrfToken)) {
            return $this->handleInvalidCsrfToken();
        }

        $newLang = $this->getPostParam('lang', 'en');
        if (!$this->isValidLanguage($newLang)) {
            return $this->handleInvalidLanguage();
        }

        $this->setSessionLanguage($newLang);
        $this->loadLanguageFiles($newLang);
        $this->redirectBack();
    }

    private function isPostRequest(): bool {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    private function getPostParam(string $key, $default = null) {
        return filter_input(INPUT_POST, $key, FILTER_SANITIZE_STRING) ?? $default;
    }

    private function isValidLanguage(string $lang): bool {
        return in_array($lang, $this->getValidLanguages(), true);
    }

    private function getValidLanguages(): array {
        return ['en', 'fr', 'ar']; // Add more languages as needed
    }

    private function setSessionLanguage(string $lang): void {
        $_SESSION['lang'] = $lang;
    }

    private function loadLanguageFiles(string $lang): void {
        // Logic to load language files or translations
        // This could involve including language files or using a translation library
        // Example: require_once "../languages/{$lang}.php";
    }

    private function redirectBack(): void {
        $previousPage = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($previousPage);
    }

    private function handleInvalidCsrfToken(): void {
        $this->logError("Invalid CSRF token");
        $this->redirect('/app/views/error.php');
    }

    private function handleInvalidLanguage(): void {
        $this->logError("Invalid language selection");
        $this->redirect('/app/views/error.php');
    }

    private function handleInvalidRequest(): void {
        $this->logError("Invalid request method");
        $this->redirect('/app/views/error.php');
    }

    private function handleUnauthorizedAccess(): void {
        $this->logError("Unauthorized access attempt");
        $this->redirect('/app/views/error.php');
    }

    private function isUserAuthenticated(): bool {
        return isset($_SESSION['user_id']);
    }

    private function logError(string $message): void {
        error_log($message);
    }
}