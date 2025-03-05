<?php
namespace App\Controllers\NutritionController;

require_once __DIR__ . '/../../models/NutritionModel.php';
require_once __DIR__ . '/../../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use App\Controllers\BaseController;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\Models\NutritionModel;
use PDO;

class DeleteControllerN extends BaseController {
    private NutritionModel $nutritionModel;
    protected Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->nutritionModel = new NutritionModel($pdo);
        $this->logger = new Logger('NutritionDeleteController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function delete(int $id): void {
        try {
            if ($id <= 0) {
                throw new Exception('Invalid meal plan ID.');
            }

            $meal = $this->nutritionModel->getNutritionById($id);
            if (!$meal || $meal['user_id'] !== (int)$_SESSION['user_id']) {
                throw new Exception('Meal plan not found or not owned by you.');
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->render('nutrition/delete', [ // Fixed: Use relative path
                    'pageTitle' => 'Delete Meal Plan',
                    'id' => $id,
                    'name' => $meal['name'],
                    'csrf_token' => $this->generateCsrfToken(),
                    'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
                ]);
                return;
            }

            if (!$this->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid security token. Received: ' . ($_POST['csrf_token'] ?? 'none'));
            }

            if (!isset($_POST['confirm']) || $_POST['confirm'] !== 'yes') {
                throw new Exception('Deletion not confirmed.');
            }

            $this->pdo->beginTransaction();
            if ($this->nutritionModel->deleteMeal($id)) {
                $this->pdo->commit();
                $this->logger->info("Meal plan deleted", [
                    'id' => $id,
                    'user_id' => $_SESSION['user_id']
                ]);
                $this->setFlashMessage('success', 'Meal plan deleted successfully.');
            } else {
                $this->pdo->rollBack();
                throw new Exception('Failed to delete meal plan.');
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
        $this->redirect('/nutrition/index');
    }
}