<?php
namespace App\Controllers\NutritionController;

require_once __DIR__ . '/../../models/NutritionModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use App\Controllers\BaseController;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Models\NutritionModel;
use PDO;

class IndexControllerN extends BaseController {
    private NutritionModel $nutritionModel;
    protected Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->nutritionModel = new NutritionModel($pdo);
        $this->logger = new Logger('NutritionIndexController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function index(int $page = 1, int $itemsPerPage = 10, string $filter = '', string $sortBy = 'created_at', string $sortOrder = 'DESC'): void {
        try {
            $offset = max(0, ($page - 1) * $itemsPerPage);
            $meals = $this->nutritionModel->fetchMeals($offset, $itemsPerPage, $filter, $sortBy, $sortOrder, (int)$_SESSION['user_id']);
            $totalMeals = $this->nutritionModel->countFilteredMeals($filter, (int)$_SESSION['user_id']);
            $totalPages = max(1, (int)ceil($totalMeals / $itemsPerPage));

            $this->render('nutrition/index', [ // Fixed: Use relative path
                'pageTitle' => 'Nutrition Plans',
                'meals' => $meals,
                'currentPage' => $page,
                'totalPages' => $totalPages,
                'itemsPerPage' => $itemsPerPage,
                'filter' => htmlspecialchars($filter),
                'sortBy' => $sortBy,
                'sortOrder' => $sortOrder,
                'csrf_token' => $this->generateCsrfToken(),
                'stats' => $this->getNutritionStats(),
                'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
            ]);
        } catch (Exception $e) {
            $this->logger->error("Meals fetch error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', 'Error loading meals.');
            $this->redirect('/error');
        }
    }

    private function getNutritionStats(): array {
        $query = "SELECT SUM(calories) as total_calories, AVG(calories) as avg_calories,
                         SUM(protein) as total_protein, AVG(protein) as avg_protein,
                         SUM(carbs) as total_carbs, AVG(carbs) as avg_carbs,
                         SUM(fat) as total_fat, AVG(fat) as avg_fat
                  FROM meals WHERE user_id = :user_id AND deleted_at IS NULL";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['user_id' => (int)$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_calories' => 0, 'avg_calories' => 0,
            'total_protein' => 0, 'avg_protein' => 0,
            'total_carbs' => 0, 'avg_carbs' => 0,
            'total_fat' => 0, 'avg_fat' => 0
        ];
    }
}