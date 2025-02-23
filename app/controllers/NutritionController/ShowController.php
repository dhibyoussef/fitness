<?php
// app/controllers/NutritionController/ShowController.php
require_once __DIR__ . '/../../models/NutritionModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ShowController extends BaseController {
    private NutritionModel $nutritionModel;
    private Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->nutritionModel = new NutritionModel($pdo);
        $this->logger = new Logger('NutritionShowController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function show(int $id): void {
        try {
            if ($id <= 0) {
                throw new Exception('Invalid meal ID.');
            }

            $meal = $this->nutritionModel->getNutritionById($id);
            if (!$meal || $meal['user_id'] !== (int)$_SESSION['user_id']) {
                throw new Exception('Meal not found or not owned by you.');
            }

            $detailedMealInfo = $this->getDetailedMealInfo($meal);
            $this->render(__DIR__ . '/../../views/nutrition/show.php', [
                'meal' => $detailedMealInfo,
                'csrf_token' => $this->generateCsrfToken(),
                'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
            ]);
        } catch (Exception $e) {
            $this->logger->error("Meal show error", [
                'message' => $e->getMessage(),
                'id' => $id,
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/nutrition/index');
        }
    }

    private function getDetailedMealInfo(array $meal): array {
        $meal['category'] = $this->nutritionModel->getCategoryById($meal['category_id'])['name'] ?? 'N/A';
        $meal['suggestions'] = $this->getMealSuggestions($meal);
        return $meal;
    }

    private function getMealSuggestions(array $meal): array {
        $suggestions = [];
        if ($meal['calories'] > 2000) {
            $suggestions[] = 'Consider reducing calorie intake for weight management.';
        }
        if (!$meal['category_id']) {
            $suggestions[] = 'Assign a category for better organization.';
        }
        if ($meal['protein'] < 20) {
            $suggestions[] = 'Increase protein for muscle maintenance.';
        }
        return $suggestions ?: ['Looks good! Keep it up.'];
    }
}