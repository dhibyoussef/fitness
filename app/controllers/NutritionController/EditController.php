<?php
require_once '../../models/NutritionModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class EditController extends BaseController {
    private NutritionModel $nutritionModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->nutritionModel = new NutritionModel($pdo);
    }

    public function edit(int $id): void {
        if ($this->isInvalidId($id)) {
            return;
        }

        try {
            $mealPlan = $this->nutritionModel->getNutritionById($id);
            $this->validateNutritionOwnership($mealPlan, $id);

            $this->renderView('../../views/nutrition/edit.php', [
                'nutrition' => $mealPlan,
                'csrf_token' => $_SESSION['csrf_token']
            ]);
        } catch (RuntimeException $e) {
            $this->handleError("EditController Error: " . $e->getMessage(), $id);
        }
    }

    public function update(int $id, array $data): void {
        try {
            $this->validateCsrfToken($data);
            $this->regenerateSession();
            if ($this->isInvalidId($id)) {
                return;
            }

            $this->validateNutritionData($data);
            $data = $this->sanitizeInput($data);
            $mealPlan = $this->nutritionModel->getNutritionById($id);
            $this->validateNutritionOwnership($mealPlan, $id);

            if (!$this->nutritionModel->updateMeal($id, $data)) {
                throw new RuntimeException('Failed to update meal plan');
            }

            $this->setSuccessMessage('Meal plan updated successfully!');
            $this->logActivity("Updated meal plan ID: $id");
        } catch (InvalidArgumentException $e) {
            $this->handleError('Validation Error: ' . $e->getMessage(), $id);
        } catch (RuntimeException $e) {
            $this->handleError('Security Error: ' . $e->getMessage(), $id);
        } catch (Exception $e) {
            $this->handleError('System Error: ' . $e->getMessage(), $id);
        } finally {
            $this->redirectToIndex();
        }
    }

    private function validateNutritionOwnership(?array $mealPlan, int $id): void {
        if (!$mealPlan) {
            throw new RuntimeException("Meal plan not found for ID: $id");
        }
        if ($mealPlan['user_id'] !== $_SESSION['user_id']) {
            throw new RuntimeException("Unauthorized access attempt for meal plan ID: $id");
        }
    }

    private function sanitizeInput(array $data): array {
        return [
            'name' => trim(strip_tags($data['name'])),
            'calories' => (float)filter_var($data['calories'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'protein' => isset($data['protein']) ? (float)$data['protein'] : null,
            'carbs' => isset($data['carbs']) ? (float)$data['carbs'] : null,
            'fat' => isset($data['fat']) ? (float)$data['fat'] : null
        ];
    }

    private function handleError(string $message, int $id): void {
        error_log("$message | Meal Plan ID: $id | User ID: " . ($_SESSION['user_id'] ?? 'unknown'));
        $this->setErrorMessage($message);
    }

    private function validateCsrfToken(array $data): void {
        if (!$this->isValidCsrfToken($data['csrf_token'] ?? '')) {
            throw new RuntimeException('Invalid security token');
        }
    }

    private function regenerateSession(): void {
        session_regenerate_id(true);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    private function validateNutritionData(array $data): void {
        $requiredFields = ['name', 'calories'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new InvalidArgumentException("Missing required field: $field");
            }
        }

        if (!$this->isValidCalories($data['calories'])) {
            throw new InvalidArgumentException('Calories must be a positive number');
        }

        if (!$this->isValidName($data['name'])) {
            throw new InvalidArgumentException('Name contains invalid characters');
        }
    }

    private function isInvalidId(int $id): bool {
        if ($id <= 0) {
            $this->setErrorMessage('Invalid meal plan ID format');
            $this->redirectToIndex();
            return true;
        }
        return false;
    }

    private function isValidCalories($calories): bool {
        return is_numeric($calories) && $calories > 0;
    }

    private function isValidName(string $name): bool {
        return preg_match('/^[\p{L}\p{N}\s\-\']+$/u', $name) === 1;
    }

    private function renderView(string $viewPath, array $data = []): void {
        if (!file_exists($viewPath) || !is_readable($viewPath)) {
            throw new RuntimeException("View template not found: $viewPath");
        }
        extract($data);
        include $viewPath;
    }

    private function setErrorMessage(string $message): void {
        $_SESSION['error_message'] = $message;
    }

    private function setSuccessMessage(string $message): void {
        $_SESSION['success_message'] = $message;
    }

    private function logActivity(string $message): void {
        error_log("User  {$_SESSION['user_id']}: $message");
    }

    private function redirectToIndex(): void {
        header("Location: ../../views/nutrition/index.php");
        exit();
    }
}