<?php
require_once '../../models/ProgressModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class IndexController extends BaseController {
    private ProgressModel $progressModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->progressModel = new ProgressModel($pdo);
    }

    public function index(int $page = 1, int $itemsPerPage = 10, string $searchQuery = '', string $sortBy = 'date', string $sortOrder = 'DESC'): void {
        if (!$this->isAuthenticated()) {
            $this->redirectWithError('Unauthorized access.');
            return;
        }

        try {
            $offset = ($page - 1) * $itemsPerPage;
            $progressEntries = $this->progressModel->getProgressWithPagination($offset, $itemsPerPage, $searchQuery, $sortBy, $sortOrder);
            $totalEntries = $this->progressModel->countProgressEntries($searchQuery);

            $stats = $this->calculateProgressStats($progressEntries);

            $this->renderView('../../views/progress/index.php', [
                'progressEntries' => $progressEntries,
                'currentPage' => $page,
                'totalPages' => ceil($totalEntries / $itemsPerPage),
                'searchQuery' => $searchQuery,
                'sortBy' => $sortBy,
                'sortOrder' => $sortOrder,
                'stats' => $stats,
                'itemsPerPage' => $itemsPerPage
            ]);

        } catch (Exception $e) {
            error_log('Error fetching progress entries: ' . $e->getMessage());
            $this->redirectWithError("Error fetching progress entries. Please try again later.");
        }
    }

    private function calculateProgressStats(array $entries): array {
        $stats = [
            'weight_change' => 0,
            'muscle_mass_change' => 0,
            'body_fat_change' => 0
        ];

        if (count($entries) >= 2) {
            $newest = reset($entries);
            $oldest = end($entries);
            
            $stats['weight_change'] = $newest['weight'] - $oldest['weight'];
            $stats['muscle_mass_change'] = $newest['muscle_mass'] - $oldest['muscle_mass'];
            $stats['body_fat_change'] = $newest['body_fat'] - $oldest['body_fat'];
        }

        return $stats;
    }

    private function renderView(string $viewPath, array $data): void {
        if (!file_exists($viewPath) || !is_readable($viewPath)) {
            $this->redirectWithError("View template not found at: $viewPath");
            return;
        }
        extract($data);
        include $viewPath;
    }

    private function redirectWithError(string $message): void {
        $_SESSION['error _message'] = $message;
        $this->redirect('../../views/error/error.php');
    }
}