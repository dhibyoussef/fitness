<?php
// app/controllers/LanguageController.php
namespace App\Controllers;
use Exception;
use PDO;

require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../../config/config.php';

class LanguageController extends BaseController {
    private const VALID_LANGUAGES = ['en', 'fr', 'ar', 'es'];

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
    }

    public function changeLanguage(): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid request or security token.');
            }

            if (!$this->isAuthenticated()) {
                throw new Exception('Please log in to change language.');
            }

            $newLang = $this->sanitizeText($_POST['lang'] ?? DEFAULT_LANG);
            if (!$this->isValidLanguage($newLang)) {
                throw new Exception('Invalid language selected.');
            }

            $this->setSessionLanguage($newLang);
            $this->loadLanguageFiles($newLang);
            $this->logger->info("Language changed", [
                'user_id' => $_SESSION['user_id'],
                'lang' => $newLang
            ]);
            $this->setFlashMessage('success', 'Language updated to ' . strtoupper($newLang));
            $this->redirectBack();
        } catch (Exception $e) {
            $this->logger->error("Language change error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'ip' => $_SERVER['REMOTE_ADDR']
            ]);
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/error');
        }
    }

    private function isValidLanguage(string $lang): bool {
        return in_array($lang, self::VALID_LANGUAGES, true);
    }

    private function setSessionLanguage(string $lang): void {
        $_SESSION['lang'] = $lang;
    }

    private function loadLanguageFiles(string $lang): void {
        $file = __DIR__ . "/../../languages/{$lang}.json";
        if (file_exists($file)) {
            $_SESSION['translations'] = json_decode(file_get_contents($file), true) ?? [];
        } else {
            $this->logger->warning("Language file not found", ['lang' => $lang]);
            $_SESSION['translations'] = [];
        }
    }

    private function redirectBack(): void {
        $previousPage = $_SERVER['HTTP_REFERER'] ?? '/dashboard';
        $this->redirect($previousPage);
    }

    private function sanitizeText(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}