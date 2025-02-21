<?php
require_once '../../models/NutritionModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class CreateController extends BaseController {
    private NutritionModel $nutritionModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->nutritionModel = new NutritionModel($pdo);
    }

    public function create(array $data): void {
        if (!$this->isUserAuthenticated()) {
            $this->handleError('Unauthorized access. Please log in to create a meal plan.');
            return;
        }

        try {
            // Validate input data
            $this->validateNutritionData($data);

            // Attempt to create nutrition entry
            if (!$this->nutritionModel->createMeal($data)) {
                throw new RuntimeException('Failed to create meal plan. Please try again later.');
            }

            // Set success message and redirect to nutrition index
            $_SESSION['success_message'] = 'Meal plan created successfully.';
            $this->redirect('../../views/nutrition/index.php');

        } catch (InvalidArgumentException $e) {
            $this->handleError('Validation Error: ' . $e->getMessage());
        } catch (RuntimeException $e) {
            $this->handleError('Creation Error: ' . $e->getMessage());
        } catch (Exception $e) {
            $this->handleError('An unexpected error occurred. Please try again later.');
        }
    }

    private function validateNutritionData(array $data): void {
        if (empty($data['name']) || !$this->isValidCalories($data['calories']) || !$this->isValidName($data['name'])) {
            throw new InvalidArgumentException('Invalid meal plan data provided. Please ensure all fields are filled out correctly.');
        }
    }

    private function isValidCalories($calories): bool {
        return is_numeric($calories) && $calories > 0 && $calories <= 5000; // Ensure calories are within a reasonable range
    }

    private function isValidName(string $name): bool {
        return preg_match('/^[\p{L}\p{N}\s]+$/u', $name) === 1; // Allow letters from any language
    }

    private function handleError(string $message): void {
        $_SESSION['error_message'] = $message;
        $this->redirect('../../views/error/error.php');
    }

    private function isUserAuthenticated(): bool {
        return isset($_SESSION['user_id']) && $_SESSION['logged_in'] === true;
    }

    protected function redirect($url) {
        header("Location: $url");
        exit();
    }
}