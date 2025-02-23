<?php
// app/controllers/ProgressController/ShowController.php
require_once __DIR__ . '/../../models/ProgressModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ShowController extends BaseController {
    private ProgressModel $progressModel;
    private Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->progressModel = new ProgressModel($pdo);
        $this->logger = new Logger('ProgressShowController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function show(int $id): void {
        try {
            if ($id <= 0) {
                throw new Exception('Invalid progress ID.');
            }

            $progress = $this->progressModel->getProgressById($id);
            if (!$progress || $progress['user_id'] !== (int)$_SESSION['user_id']) {
                throw new Exception('Progress entry not found or not owned by you.');
            }

            $this->render(__DIR__ . '/../../views/progress/show.php', [
                'progress' => $progress,
                'csrf_token' => $this->generateCsrfToken(),
                'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
            ]);
        } catch (Exception $e) {
            $this->logger->error("Progress show error", [
                'message' => $e->getMessage(),
                'id' => $id,
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/progress/index');
        }
    }
}