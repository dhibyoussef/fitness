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

class EditControllerN extends BaseController {
    private NutritionModel $nutritionModel;
    protected Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->nutritionModel = new NutritionModel($pdo);
        $this->logger = new Logger('NutritionEditController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function edit(int $id): void {
        try {
            if ($id <= 0) {
                throw new Exception('Invalid meal plan ID.');
            }

            $mealPlan = $this->nutritionModel->getNutritionById($id);
            if (!$mealPlan || $mealPlan['user_id'] !== (int)$_SESSION['user_id']) {
                throw new Exception('Meal plan not found or not owned by you.');
            }

            $this->render('nutrition/edit', [ // Fixed: Use relative path
                'pageTitle' => 'Edit Meal Plan',
                'nutrition' => $mealPlan,
                'categories' => $this->nutritionModel->getAllCategories(),
                'csrf_token' => $this->generateCsrfToken(),
                'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
            ]);
        } catch (Exception $e) {
            $this->logger->error("Edit fetch error", [
                'message' => $e->getMessage(),
                'id' => $id,
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/nutrition/index');
        }
    }

    public function update(int $id, array $data): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method. Expected POST, got ' . $_SERVER['REQUEST_METHOD']);
            }
            if (!$this->isValidCsrfToken($data['csrf_token'] ?? '')) {
                throw new Exception('Invalid security token. Received: ' . ($data['csrf_token'] ?? 'none'));
            }

            if ($id <= 0) {
                throw new Exception('Invalid meal plan ID.');
            }

            $mealPlan = $this->nutritionModel->getNutritionById($id);
            if (!$mealPlan || $mealPlan['user_id'] !== (int)$_SESSION['user_id']) {
                throw new Exception('Meal plan not found or not owned by you.');
            }

            $this->validateNutritionData($data);
            $sanitizedData = [
                'name' => $this->sanitizeInput($data['name']),
                'calories' => (int)$data['calories'],
                'protein' => isset($data['protein']) && $data['protein'] !== '' ? (float)$data['protein'] : null,
                'carbs' => isset($data['carbs']) && $data['carbs'] !== '' ? (float)$data['carbs'] : null,
                'fat' => isset($data['fat']) && $data['fat'] !== '' ? (float)$data['fat'] : null,
                'category_id' => isset($data['category_id']) && $data['category_id'] !== '' ? (int)$data['category_id'] : null
            ];

            $this->pdo->beginTransaction();
            if ($this->nutritionModel->updateMeal($id, $sanitizedData)) {
                $this->pdo->commit();
                $this->logger->info("Meal plan updated", [
                    'id' => $id,
                    'user_id' => $_SESSION['user_id'],
                    'name' => $sanitizedData['name']
                ]);
                $this->setFlashMessage('success', 'Meal plan updated successfully!');
            } else {
                $this->pdo->rollBack();
                throw new Exception('No changes detected or update failed.');
            }
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $this->logger->error("Update error", [
                'message' => $e->getMessage(),
                'id' => $id,
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'data' => $data, // Debug submitted data
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/nutrition/edit/' . $id); // Redirect back to edit form on error
        }
        $this->redirect('/nutrition/index');
    }

    private function validateNutritionData(array $data): void {
        if (empty($data['name']) || !$this->isValidName($data['name'])) {
            throw new Exception('Name must be 3-100 characters with letters, numbers, spaces.');
        }
        if (!isset($data['calories']) || !$this->isValidCalories($data['calories'])) {
            throw new Exception('Calories must be 1-5000.');
        }
        if (isset($data['protein']) && $data['protein'] !== '' && !$this->isValidMacro($data['protein'])) {
            throw new Exception('Protein must be 0-1000 grams.');
        }
        if (isset($data['carbs']) && $data['carbs'] !== '' && !$this->isValidMacro($data['carbs'])) {
            throw new Exception('Carbs must be 0-1000 grams.');
        }
        if (isset($data['fat']) && $data['fat'] !== '' && !$this->isValidMacro($data['fat'])) {
            throw new Exception('Fat must be 0-1000 grams.');
        }
        if (isset($data['category_id']) && $data['category_id'] !== '' && !$this->isValidCategory($data['category_id'])) {
            throw new Exception('Invalid category.');
        }
    }

    private function isValidName(string $name): bool {
        $length = strlen(trim($name));
        return $length >= 3 && $length <= 100 && preg_match('/^[\p{L}\p{N}\s\-\']+$/u', $name);
    }

    private function isValidCalories($calories): bool {
        return is_numeric($calories) && $calories > 0 && $calories <= 5000;
    }

    private function isValidMacro($value): bool {
        return is_numeric($value) && $value >= 0 && $value <= 1000;
    }

    private function isValidCategory($categoryId): bool {
        $query = "SELECT COUNT(*) FROM categories WHERE id = :id AND deleted_at IS NULL"; // Fixed: Use $this->pdo and add deleted_at check
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => (int)$categoryId]);
        return $stmt->fetchColumn() > 0;
    }

    private function sanitizeInput(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}