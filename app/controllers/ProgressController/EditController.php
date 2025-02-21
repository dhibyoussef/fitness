<?php
require_once '../../models/ProgressModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class EditController extends BaseController {
    private ProgressModel $progressModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->progressModel = new ProgressModel($pdo);
    }

    public function edit(int $id): void {
        if (!$this->isUserAuthenticated()) {
            $this->redirectWithError('Unauthorized access. Please login to edit progress entries.');
            return;
        }

        if ($this->isInvalidId($id)) {
            $this->redirectWithError('Invalid progress ID provided. Please try again.');
            return;
        }

        $progress = $this->progressModel->getProgressById($id);
        if (!$progress) {
            $this->redirectWithError("Progress entry not found for ID: $id");
            return;
        }

        $this->renderView('../../views/progress/edit.php', ['progress' => $progress]);
    }

    public function update(int $id, array $data): void {
        if (!$this->isUserAuthenticated()) {
            $this->redirectWithError('Unauthorized access. Please login to update progress entries.');
            return;
        }

        if ($this->isInvalidId($id)) {
            $this->redirectWithError('Invalid progress ID provided.');
            return;
        }

        if (!$this->isValidData($data)) {
            $this->redirectWithError('Please ensure all measurements are valid and within reasonable ranges.');
            return;
        }

        try {
            if ($this->progressModel->updateProgress($id, $data)) {
                $_SESSION['success_message'] = 'Progress updated successfully! Your fitness journey is on track.';
                $this->redirect('../../views/progress/confirmation.php');
            } else {
                $this->redirectWithError('No changes were detected in the progress entry.');
            }
        } catch (Exception $e) {
            error_log('Progress update failed: ' . $e->getMessage());
            $this->redirectWithError('Unable to update progress. Please try again or contact support if the issue persists.');
        }
    }

    private function isInvalidId(int $id): bool {
        return $id <= 0 || !filter_var($id, FILTER_VALIDATE_INT);
    }

    private function isValidData(array $data): bool {
        // Check if all required fields are present
        $requiredFields = ['weight', 'muscle_mass', 'body_fat', 'date'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return false;
            }
        }

        // Validate weight (in kg) - between 20kg and 300kg
        if (!is_numeric($data['weight']) || $data[' weight'] < 20 || $data['weight'] > 300) {
            return false;
        }

        // Validate muscle mass (in kg) - between 10kg and 100kg
        if (!is_numeric($data['muscle_mass']) || $data['muscle_mass'] < 10 || $data['muscle_mass'] > 100) {
            return false;
        }

        // Validate body fat percentage - between 2% and 50%
        if (!is_numeric($data['body_fat']) || $data['body_fat'] < 2 || $data['body_fat'] > 50) {
            return false;
        }

        // Validate date format
        if (!strtotime($data['date'])) {
            return false;
        }

        return true;
    }

    private function isUserAuthenticated(): bool {
        return isset($_SESSION['user_id']);
    }

    private function redirectWithError(string $message): void {
        $_SESSION['error_message'] = $message;
        $this->redirect('../../views/error/error.php');
    }

    protected function redirect($url): void {
        header("Location: $url");
        exit();
    }

    private function renderView(string $viewPath, array $data): void {
        if (!file_exists($viewPath) || !is_readable($viewPath)) {
            $this->redirectWithError("View template not found: $viewPath");
            return;
        }
        extract($data);
        include $viewPath;
    }
}