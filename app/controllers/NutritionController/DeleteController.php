<?php
require_once '../../models/NutritionModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class DeleteController extends BaseController {
    private NutritionModel $nutritionModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->nutritionModel = new NutritionModel($pdo);
    }

    public function delete(int $id, bool $confirmDeletion = false): void {
        if (!$this->isUserLoggedIn()) {
            $this->handleUnauthorizedAccess();
            return;
        }

        if ($this->isInvalidId($id)) {
            return;
        }

        if (!$confirmDeletion) {
            $this->promptForConfirmation($id);
            return;
        }

        $this->processDeletion($id);
    }

    private function isUserLoggedIn(): bool {
        return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'];
    }

    private function handleUnauthorizedAccess(): void {
        $this->setErrorMessage('Unauthorized access. Please log in to delete a meal plan.');
        $this->redirectToLogin();
    }

    private function isInvalidId(int $id): bool {
        if ($id <= 0) {
            $this->setErrorMessage('Invalid nutrition ID provided.');
            $this->redirectToIndex();
            return true;
        }
        return false;
    }

 private function promptForConfirmation(int $id): void {
        $_SESSION['confirmation_message'] = 'Are you sure you want to delete this meal plan?';
        header("Location: ../../views/nutrition/confirm_delete.php?id=$id");
        exit();
    }

    private function processDeletion(int $id): void {
        try {
            $this->ensureMealPlanExists($id);
            $this->attemptDelete($id);
            $this->setSuccessMessage('Nutrition entry deleted successfully.');
        } catch (RuntimeException $e) {
            $this->handleException($e, $id);
        } catch (Exception $e) {
            $this->handleGenericException($e, $id);
        } finally {
            $this->redirectToIndex();
        }
    }

    private function ensureMealPlanExists(int $id): void {
        if (!$this->nutritionModel->getNutritionById($id)) {
            throw new RuntimeException('Nutrition entry does not exist.');
        }
    }

    private function attemptDelete(int $id): void {
        if (!$this->nutritionModel->deleteMeal($id)) {
            throw new RuntimeException('Failed to delete nutrition entry. Please try again.');
        }
    }

    private function setErrorMessage(string $message): void {
        $_SESSION['error_message'] = $message;
    }

    private function setSuccessMessage(string $message): void {
        $_SESSION['success_message'] = $message;
    }

    private function handleException(RuntimeException $e, int $id): void {
        error_log(sprintf('DeleteController Runtime Error: %s | Nutrition ID: %d', $e->getMessage(), $id));
        $this->setErrorMessage('An error occurred while processing your request. Please try again.');
    }

    private function handleGenericException(Exception $e, int $id): void {
        error_log(sprintf('DeleteController Error: %s | Nutrition ID: %d', $e->getMessage(), $id));
        $this->setErrorMessage('An unexpected error occurred. Please contact support.');
    }

    private function redirectToIndex(): void {
        header("Location: ../../views/nutrition/index.php");
        exit();
    }

    private function redirectToLogin(): void {
        header("Location: ../../views/auth/login.php");
        exit();
    }
}