<?php
require_once '../../models/NutritionModel.php';
require_once '../../controllers/BaseController.php';

class UpdateController extends BaseController {
    private NutritionModel $nutritionModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->nutritionModel = new NutritionModel($pdo);
    }

    public function update(int $id, array $data) {
        if (!$this->isUserAuthorized()) {
            $this->redirectWithError('Unauthorized access. Please login to update meal plans.');
            return;
        }

        if ($this->isInvalidId($id)) {
            $this->redirectWithError('Invalid meal plan ID provided.');
            return;
        }

        if (!$this->isValidData($data)) {
            $this->redirectWithError('Please ensure all required fields are filled out correctly.');
            return;
        }

        try {
            $updateResult = $this->nutritionModel->updateMeal($id, $data);
            
            if ($updateResult) {
                $_SESSION['success_message'] = 'Meal plan updated successfully!';
                header("Location: ../../views/nutrition/index.php");
            } else {
                $this->redirectWithError('No changes were detected in the meal plan.');
            }
            exit();
        } catch (Exception $e) {
            error_log('Nutrition update failed: ' . $e->getMessage());
            $this->redirectWithError('Unable to update meal plan. Please try again later.');
        }
    }

    private function isInvalidId(int $id): bool {
        return $id <= 0 || !filter_var($id, FILTER_VALIDATE_INT);
    }

    private function isValidData(array $data): bool {
        if (!isset($data['name'], $data['calories'], $data['category_id'])) {
            return false;
        }

        if (empty(trim($data['name'])) || strlen($data['name']) > 255) {
            return false;
        }

        if (!is_numeric($data['calories']) || 
            $data['calories'] <= 0 || 
            $data['calories'] > 10000) {
            return false;
        }

        if (!is_numeric($data['category_id']) || 
            $data['category_id'] <= 0) {
            return false;
        }

        return true;
    }

    private function isUserAuthorized(): bool {
        return isset($_SESSION['user_id']) && 
               isset($_SESSION['user_permissions']) && 
               in_array('edit_nutrition', $_SESSION['user_permissions']);
    }

    private function redirectWithError(string $message) {
        $_SESSION['error_message'] = $message;
        $_SESSION['form_data'] = $_POST; // Preserve form data for user correction
        header('Location: ../../views/error.php');
        exit();
    }
}