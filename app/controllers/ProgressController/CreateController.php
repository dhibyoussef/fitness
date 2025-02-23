<?php
// app/controllers/ProgressController/CreateController.php
require_once __DIR__ . '/../../models/ProgressModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class CreateController extends BaseController {
    private ProgressModel $progressModel;
    private Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->progressModel = new ProgressModel($pdo);
        $this->logger = new Logger('ProgressCreateController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function create(array $data): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidCsrfToken($data['csrf_token'] ?? '')) {
                throw new Exception('Invalid request or security token.');
            }

            $this->validateData($data);
            $sanitizedData = [
                'user_id' => (int)$_SESSION['user_id'],
                'weight' => (float)$data['weight'],
                'body_fat' => (float)$data['body_fat'],
                'muscle_mass' => isset($data['muscle_mass']) ? (float)$data['muscle_mass'] : null,
                'date' => $data['date'] ?? date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->progressModel->createProgress($sanitizedData)) {
                $this->logger->info("Progress logged", [
                    'user_id' => $_SESSION['user_id'],
                    'date' => $sanitizedData['date']
                ]);
                $this->setFlashMessage('success', 'Progress logged successfully!');
                $this->redirect('/progress/index');
            } else {
                throw new Exception('Failed to log progress.');
            }
        } catch (Exception $e) {
            $this->logger->error("Progress creation error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/progress/create');
        }
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
        if (isset($data['date']) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['date'])) {
            throw new Exception('Invalid date format (YYYY-MM-DD).');
        }
    }
}