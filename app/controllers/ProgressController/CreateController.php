<?php
require_once '../../models/ProgressModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class CreateController extends BaseController {
    private ProgressModel $progressModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->progressModel = new ProgressModel($pdo);
    }

    public function create(array $data): void {
        if (!$this->isUserAuthenticated()) {
            $this->redirectWithError('Unauthorized access. Please login to log your progress.');
            return;
        }

        if (!$this->isValidData($data)) {
            $this->redirectWithError('Please ensure all measurements are valid and within reasonable ranges.');
            return;
        }

        try {
            $this->progressModel->createProgress($data);
            $_SESSION['success_message'] = 'Progress logged successfully! Keep up the great work!';
            header("Location: ../../views/progress/confirmation.php");
            exit();
        } catch (Exception $e) {
            error_log('Progress creation failed: ' . $e->getMessage());
            $this->redirectWithError('Unable to log progress. Please try again or contact support if the issue persists.');
        }
    }

    private function isValidData(array $data): bool {
        // Check if all required fields are present
        $requiredFields = ['weight', 'muscle_mass', 'body_fat', 'chest', 'waist', 'hips'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return false;
            }
        }

        // Validate weight (in kg) - between 20kg and 300kg
        if (!is_numeric($data['weight']) || $data['weight'] < 20 || $data['weight'] > 300) {
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

        // Validate body measurements (in cm) - between 30cm and 200cm
        $measurements = ['chest', 'waist', 'hips'];
        foreach ($measurements as $measurement) {
            if (!is_numeric($data[$measurement]) || $data[$measurement] < 30 || $data[$measurement] > 200) {
                return false;
            }
        }

        return true;
    }

    private function isUserAuthenticated(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    private function redirectWithError(string $message): void {
        $_SESSION['error_message'] = $message;
        header("Location: ../../views/error/error.php");
        exit();
    }
}
?>