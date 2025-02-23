<?php
// app/controllers/ProgressController/EditController.php
require_once __DIR__ . '/../../models/ProgressModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class EditController extends BaseController {
    private ProgressModel $progressModel;
    private Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->progressModel = new ProgressModel($pdo);
        $this->logger = new Logger('ProgressEditController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function edit(int $id): void {
        try {
            if ($id <= 0) {
                throw new Exception('Invalid progress ID.');
            }

            $progress = $this->progressModel->getProgressById($id);
            if (!$progress || $progress['user_id'] !== (int)$_SESSION['user_id']) {
                throw new Exception('Progress entry not found or not owned by you.');
            }

            $this->render(__DIR__ . '/../../views/progress/edit.php', [
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
            $this->redirect('/progress/index');
        }
    }

    public function update(int $id, array $data): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidCsrfToken($data['csrf_token'] ?? '')) {
                throw new Exception('Invalid request or security token.');
            }

            if ($id <= 0) {
                throw new Exception('Invalid progress ID.');
            }

            $progress = $this->progressModel->getProgressById($id);
            if (!$progress || $progress['user_id'] !== (int)$_SESSION['user_id']) {
                throw new Exception('Progress entry not found or not owned by you.');
            }

            $this->validateData($data);
            $sanitizedData = [
                'weight' => (float)$data['weight'],
                'body_fat' => (float)$data['body_fat'],
                'muscle_mass' => isset($data['muscle_mass']) ? (float)$data['muscle_mass'] : null,
                'date' => $data['date']
            ];

            if ($this->progressModel->updateProgress($id, $sanitizedData)) {
                $this->logger->info("Progress updated", [
                    'id' => $id,
                    'user_id' => $_SESSION['user_id']
                ]);
                $this->setFlashMessage('success', 'Progress updated successfully!');
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
        $this->redirect('/progress/index');
    }

    private function validateData(array $data): void {
        if (empty($data['weight']) || !is_numeric($data['weight']) || $data['weight'] < 20 || $data['weight'] > 300) {
            throw new Exception('Weight must be 20-300 kg.');
        }
        if (empty($data['body_fat']) || !is_numeric($data['body_fat']) || $data['body_fat'] < 2 || $data['body_fat'] > 50) {
            throw new Exception('Body fat must be 2-50%.');
        }
        if (isset($data['muscle_mass']) && (!is_numeric($data['muscle_mass']) || $data['muscle_mass'] < 10 || $data['muscle_mass'] > 100)) {
            throw new Exception('Muscle mass must be 10-100 kg if provided.');
        }
        if (empty($data['date']) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date'])) {
            throw new Exception('Invalid date format (YYYY-MM-DD).');
        }
    }
}