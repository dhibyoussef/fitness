<?php
require_once '../../models/UserModel.php';
require_once '../../models/ProgressModel.php';
require_once '../../controllers/BaseController.php';
require_once '../../../config/database.php';

class ProfileController extends BaseController {
    private UserModel $userModel;
    private ProgressModel $progressModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->userModel = new UserModel($pdo);
        $this->progressModel = new ProgressModel($pdo);
    }

    public function show(int $id): void {
        if (!$this->isUserAuthenticated()) {
            $this->redirectWithError('Unauthorized access.');
            return;
        }

        if ($this->isInvalidId($id)) {
            $this->redirectWithError('Invalid user ID.');
            return;
        }

        $user = $this->userModel->getUserById($id);
        if (!$user) {
            $this->redirectWithError('User  not found.');
            return;
        }

        $user = $this->formatUserData($user);
        $progress = $this->progressModel->getOverallProgressStatistics();
        $this->renderView('../../views/user/profile.php', ['user' => $user, 'progress' => $progress]);
    }

    public function update(int $id, array $data): void {
        if (!$this->isUserAuthenticated()) {
            $this->redirectWithError('Unauthorized access.');
            return;
        }

        if ($this->isInvalidId($id)) {
            $this->redirectWithError('Invalid user ID.');
            return;
        }

        if (!$this->validateUserData($data)) {
            $this->redirectWithError('Validation failed. Please check the input data.');
            return;
        }

        try {
            $this->userModel->updateUser ($id, $data);
            $_SESSION['success_message'] = 'User  updated successfully.';
            $this->redirect("../../views/user/profile.php?id=$id");
        } catch (Exception $e) {
            error_log('Update failed: ' . $e->getMessage());
            $this->redirectWithError('Failed to update user. Please try again.');
        }
    }

    private function isInvalidId(int $id): bool {
        return $id <= 0;
    }

    private function validateUserData(array $data): bool {
        return isset($data['name'], $data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    }

    private function redirectWithError(string $message): void {
        $_SESSION['error_message'] = $message;
        $this->redirect('../../views/error.php');
    }

    protected function redirect($url): void {
        header("Location: $url");
        exit();
    }

    private function renderView(string $viewPath, array $data): void {
        if (!file_exists($viewPath) || !is_readable($viewPath)) {
            $this->redirectWithError("View template not found at: $viewPath");
            return;
        }
        extract($data);
        include $viewPath;
    }

    private function formatUserData(array $user): array {
        // Example formatting logic for user data
        $user['full_name'] = strtoupper($user['name']);
        return $user;
    }

    private function isUserAuthenticated(): bool {
        return isset($_SESSION['user_id']);
    }
}