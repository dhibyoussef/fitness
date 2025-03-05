<?php
namespace App\Controllers\ProgressController;

require_once __DIR__ . '/../../models/ProgressModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use App\Controllers\BaseController;
use App\Models\ProgressModel;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PDO;

class DeleteControllerP extends BaseController {
    private ProgressModel $progressModel;
    protected Logger $logger;

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
                $this->render('progress/delete', [ // Fixed: Use relative path
                    'pageTitle' => 'Delete Progress',
                    'id' => $id,
                    'date' => $progress['date'],
                    'csrf_token' => $this->generateCsrfToken(),
                    'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
                ]);
                return;
            }

            if (!$this->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid security token.');
            }

            if (!isset($_POST['confirm']) || $_POST['confirm'] !== 'yes') {
                throw new Exception('Deletion not confirmed.');
            }

            $this->pdo->beginTransaction();
            if ($this->progressModel->deleteProgress($id)) {
                $this->pdo->commit();
                $this->logger->info("Progress deleted", [
                    'id' => $id,
                    'user_id' => $_SESSION['user_id']
                ]);
                $this->setFlashMessage('success', 'Progress entry deleted successfully.');
            } else {
                $this->pdo->rollBack();
                throw new Exception('Failed to delete progress entry.');
            }
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
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