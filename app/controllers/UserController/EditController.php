<?php
// app/controllers/UserController/EditController.php
namespace App\Controllers\UserController {
    require_once __DIR__ . '/../../models/UserModel.php';
    require_once __DIR__ . '/../../models/ProgressModel.php';
    require_once __DIR__ . '/../../controllers/BaseController.php';
    require_once __DIR__ . '/../../../config/database.php';

    use App\Controllers\BaseController;
    use Exception;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use PDO;
    use App\Models\ProgressModel;
    use App\Models\UserModel;

    class EditControllerU extends BaseController {
        private UserModel $userModel;
        private ProgressModel $progressModel;
        protected Logger $logger;

        public function __construct(PDO $pdo) {
            parent::__construct($pdo);
            $this->userModel = new UserModel($pdo);
            $this->progressModel = new ProgressModel($pdo);
            $this->logger = new Logger('UserEditController');
            $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log'));
            $this->requireAuth();
        }

        public function edit(int $id): void {
            try {
                if ($id <= 0 || $id !== (int)$_SESSION['user_id']) {
                    throw new Exception('You can only edit your own profile.');
                }

                $user = $this->userModel->getUserById($id);
                if (!$user) {
                    throw new Exception('User not found.');
                }

                $progress = $this->progressModel->getOverallProgressStatistics($id);
                $this->render('user/edit', [ // Fixed: Use relative path 'user/edit'
                    'pageTitle' => 'Edit Profile',
                    'user' => $user,
                    'progress' => $progress,
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
                $this->redirect('/user/profile');
            }
        }

        public function update(int $id, array $data): void {
            try {
                if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidCsrfToken($data['csrf_token'] ?? '')) {
                    throw new Exception('Invalid request or security token.');
                }

                if ($id <= 0 || $id !== (int)$_SESSION['user_id']) {
                    throw new Exception('You can only update your own profile.');
                }

                $this->validateUserData($data);
                $sanitizedData = [
                    'username' => $this->sanitizeText($data['name']),
                    'email' => $this->sanitizeEmail($data['email'])
                ];

                $existingUser = $this->userModel->getUserByEmail($sanitizedData['email']);
                if ($existingUser && $existingUser['id'] !== $id) {
                    throw new Exception('Email is already in use by another account.');
                }

                if ($this->userModel->updateUser($id, $sanitizedData)) {
                    $_SESSION['username'] = $sanitizedData['username'];
                    $this->logger->info("User updated", [
                        'id' => $id,
                        'username' => $sanitizedData['username']
                    ]);
                    $this->setFlashMessage('success', 'Profile updated successfully!');
                } else {
                    throw new Exception('No changes detected or update failed.');
                }
            } catch (Exception $e) {
                $this->logger->error("Update error", [
                    'message' => $e->getMessage(),
                    'id' => $id,
                    'user_id' => $_SESSION['user_id'] ?? 'unknown',
                    'trace' => $e->getTraceAsString()
                ]);
                $this->setFlashMessage('error', $e->getMessage());
            }
            $this->redirect('/user/profile');
        }

        /**
         * @throws Exception
         */
        private function validateUserData(array $data): void {
            if (empty($data['name']) || strlen($data['name']) < 3 || strlen($data['name']) > 50) {
                throw new Exception('Username must be 3-50 characters.');
            }
            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Invalid email format.');
            }
        }

        private function sanitizeText(string $input): string {
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }

        private function sanitizeEmail(string $email): string {
            return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        }
    }
}