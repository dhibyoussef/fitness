<?php
// app/controllers/ProgressController/DeleteController.php
require_once __DIR__ . '/../../models/ProgressModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class DeleteController extends BaseController {
    private ProgressModel $progressModel;
    private Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->progressModel = new ProgressModel($pdo);
        $this->logger = new Logger('ProgressDeleteController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function delete(int $id): void {
        try {
            if ($id <= 0) {
                throw new Exception('Invalid progress ID.');
            }

            $progress = $this->progressModel->getProgressById($id);
            if (!$progress || $progress['user_id'] !== (int)$_SESSION['user_id']) {
                throw new Exception('Progress entry not found or not owned by you.');
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->render(__DIR__ . '/../../views/progress/delete.php', [
                    'id' => $id,
                    'date' => $progress['date'],
                    'csrf_token' => $this->generateCsrfToken()
                ]);
                return;
            }

            if (!$this->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid security token.');
            }

            if (!isset($_POST['confirm']) || $_POST['confirm'] !== 'yes') {
                throw new Exception('Deletion not confirmed.');
            }

            if ($this->progressModel->deleteProgress($id)) {
                $this->logger->info("Progress deleted", [
                    'id' => $id,
                    'user_id' => $_SESSION['user_id']
                ]);
                $this->setFlashMessage('success', 'Progress entry deleted successfully.');
            } else {
                throw new Exception('Failed to delete progress entry.');
            }
        } catch (Exception $e) {
            $this->logger->error("Deletion error", [
                'message' => $e->getMessage(),
                'id' => $id,
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
        }
        $this->redirect('/progress/index');
    }
}