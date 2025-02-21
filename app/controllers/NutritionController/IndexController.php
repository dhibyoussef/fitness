<?php
require_once '../../models/NutritionModel.php';
require_once '../../controllers/BaseController.php';

class IndexController extends BaseController {
    private NutritionModel $nutritionModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->nutritionModel = new NutritionModel($pdo);
    }

    public function index(int $page = 1, int $itemsPerPage = 10, string $filter = ''): void {
        try {
            $offset = $this->calculateOffset($page, $itemsPerPage);
            $meals = $this->nutritionModel->fetchMeals($offset, $itemsPerPage, $filter);
            $totalMeals = $this->nutritionModel->countFilteredMeals($filter);
            $totalPages = $this->calculateTotalPages($totalMeals, $itemsPerPage);

            $this->render('nutrition/index', [
                'meals' => $meals,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'filter' => $filter
            ]);
        } catch (Exception $e) {
            $this->logError('Failed to fetch meals', $e);
            $this->handleError('Error loading meals. Please try again later.');
        }
    }

    private function calculateOffset(int $page, int $itemsPerPage): int {
        return max(0, ($page - 1) * $itemsPerPage);
    }

    private function calculateTotalPages(int $totalMeals, int $itemsPerPage): int {
        return max(1, (int) ceil($totalMeals / $itemsPerPage));
    }

    private function handleError(string $message): void {
        $this->setFlashMessage('error', $message);
        $this->redirect('/app/views/error.php');
    }

    private function logError(string $message, Exception $e): void {
        error_log($message . ': ' . $e->getMessage());
    }
}