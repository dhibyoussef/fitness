<?php
// app/controllers/NutritionController/CreateController.php
require_once __DIR__ . '/../../models/NutritionModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class CreateController extends BaseController {
    private NutritionModel $nutritionModel;
    private Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->nutritionModel = new NutritionModel($pdo);
        $this->logger = new Logger('NutritionCreateController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function create(array $data): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidCsrfToken($data['csrf_token'] ?? '')) {
                throw new Exception('Invalid request or security token.');
            }

            $this->validateNutritionData($data);
            $sanitizedData = [
                'user_id' => (int)$_SESSION['user_id'],
                'name' => $this->sanitizeInput($data['name']),
                'calories' => (int)$data['calories'],
                'protein' => isset($data['protein']) ? (float)$data['protein'] : null,
                'carbs' => isset($data['carbs']) ? (float)$data['carbs'] : null,
                'fat' => isset($data['fat']) ? (float)$data['fat'] : null,
                'category_id' => isset($data['category_id']) ? (int)$data['category_id'] : null,
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->nutritionModel->createMeal($sanitizedData)) {
                $this->logger->info("Meal plan created", [
                    'user_id' => $_SESSION['user_id'],
                    'name' => $sanitizedData['name']
                ]);
                $this->setFlashMessage('success', 'Meal plan created successfully!');
                $this->redirect('/nutrition/index');
            } else {
                throw new Exception('Failed to create meal plan.');
            }
        } catch (Exception $e) {
            $this->logger->error("Creation error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/nutrition/create');
        }
    }

    private function validateNutritionData(array $data): void {
        if (empty($data['name']) || !$this->isValidName($data['name'])) {
            throw new Exception('Meal name must be 3-100 characters and contain only letters, numbers, and spaces.');
        }
        if (!isset($data['calories']) || !$this->isValidCalories($data['calories'])) {
            throw new Exception('Calories must be a number between 1 and 5000.');
        }
        if (isset($data['protein']) && !$this->isValidMacro($data['protein'])) {
            throw new Exception('Protein must be a number between 0 and 1000 grams.');
        }
        if (isset($data['carbs']) && !$this->isValidMacro($data['carbs'])) {
            throw new Exception('Carbs must be a number between 0 and 1000 grams.');
        }
        if (isset($data['fat']) && !$this->isValidMacro($data['fat'])) {
            throw new Exception('Fat must be a number between 0 and 1000 grams.');
        }
        if (isset($data['category_id']) && !$this->isValidCategory($data['category_id'])) {
            throw new Exception('Invalid category selected.');
        }
    }

    private function isValidName(string $name): bool {
        $length = strlen(trim($name));
        return $length >= 3 && $length <= 100 && preg_match('/^[\p{L}\p{N}\s]+$/u', $name);
    }

    private function isValidCalories($calories): bool {
        return is_numeric($calories) && $calories > 0 && $calories <= 5000;
    }

    private function isValidMacro($value): bool {
        return is_numeric($value) && $value >= 0 && $value <= 1000;
    }

    private function isValidCategory($categoryId): bool {
        $query = "SELECT COUNT(*) FROM categories WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => (int)$categoryId]);
        return $stmt->fetchColumn() > 0;
    }

    private function sanitizeInput(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}