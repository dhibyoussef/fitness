<?php
require_once '../../models/ProgressModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class UpdateController extends BaseController {
    private ProgressModel $progressModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->progressModel = new ProgressModel($pdo);
    }

    public function edit(int $id): void {
        $this->authorizeUser ();
        $this->validateId($id);

        $progress = $this->progressModel->getProgressById($id);
        if (!$progress) {
            $this->redirectWithError("Progress not found for ID: $id");
        }

        $this->renderView('../../views/progress/edit.php', ['progress' => $progress]);
    }

    public function update(int $id, array $data): void {
        $this->authorizeUser ();
        $this->validateId($id);
        $this->validateProgressDataOrFail($data);

        try {
            $this->progressModel->updateProgress($id, $data);
            $_SESSION['success_message'] = 'Progress updated successfully.';
            $this->redirect('../../views/progress/index.php');
        } catch (Exception $e) {
            error_log('Update failed: ' . $e->getMessage());
            $this->redirectWithError('Failed to update progress. Please try again.');
        }
    }

    private function validateProgressDataOrFail(array $data): void {
        if (!isset($data['weight'], $data['body_fat'], $data['date']) ||
            !is_numeric($data['weight']) || $data['weight'] <= 0 ||
 !is_numeric($data['body_fat']) || $data['body_fat'] < 0) {
            $this->redirectWithError('Validation failed. Please check the input data.');
        }
    }

    private function validateId(int $id): void {
        if ($id <= 0) {
            $this->redirectWithError('Invalid progress ID.');
        }
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
        }
        extract($data);
        include $viewPath;
    }

    private function authorizeUser (): void {
        if (!isset($_SESSION['user_id'])) {
            $this->redirectWithError('Unauthorized access.');
        }
    }
}