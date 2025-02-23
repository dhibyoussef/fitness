<?php
// app/controllers/ProgressController/IndexController.php
require_once __DIR__ . '/../../models/ProgressModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class IndexController extends BaseController {
    private ProgressModel $progressModel;
    private Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->progressModel = new ProgressModel($pdo);
        $this->logger = new Logger('ProgressIndexController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function index(int $page = 1, int $itemsPerPage = 10, string $filter = '', string $sortBy = 'date', string $sortOrder = 'DESC'): void {
        try {
            $offset = max(0, ($page - 1) * $itemsPerPage);
            $progressEntries = $this->progressModel->getProgressWithPagination($offset, $itemsPerPage, $filter, $sortBy, $sortOrder, (int)$_SESSION['user_id']);
            $totalEntries = $this->progressModel->countProgressEntries($filter, (int)$_SESSION['user_id']);
            $totalPages = max(1, (int)ceil($totalEntries / $itemsPerPage));

            $stats = $this->calculateProgressStats($progressEntries);
            $this->render(__DIR__ . '/../../views/progress/index.php', [
                'progressEntries' => $progressEntries,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'itemsPerPage' => $itemsPerPage,
                'filter' => htmlspecialchars($filter),
                'sortBy' => $sortBy,
                'sortOrder' => $sortOrder,
                'stats' => $stats,
                'csrf_token' => $this->generateCsrfToken(),
                'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
            ]);
        } catch (Exception $e) {
            $this->logger->error("Progress fetch error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', 'Error loading progress.');
            $this->redirect('/error');
        }
    }

    private function calculateProgressStats(array $entries): array {
        $stats = [
            'weight_change' => 0,
            'muscle_mass_change' => 0,
            'body_fat_change' => 0,
            'total_entries' => count($entries)
        ];

        if (count($entries) >= 2) {
            $newest = reset($entries);
            $oldest = end($entries);
            $stats['weight_change'] = round($newest['weight'] - $oldest['weight'], 1);
            $stats['muscle_mass_change'] = round(($newest['muscle_mass'] ?? 0) - ($oldest['muscle_mass'] ?? 0), 1);
            $stats['body_fat_change'] = round($newest['body_fat'] - $oldest['body_fat'], 1);
        }
        return $stats;
    }
}