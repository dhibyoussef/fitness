<?php
require_once '../../models/NutritionModel.php';
require_once '../../controllers/BaseController.php';

class ShowController extends BaseController {
    private NutritionModel $nutritionModel;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->nutritionModel = new NutritionModel($pdo);
    }

    public function show(int $id) {
        if (!$this->isUserAuthenticated()) {
            $this->redirectWithError('Unauthorized access.');
            return;
        }

        if ($this->isInvalidId($id)) {
            $this->redirectWithError('Invalid meal ID.');
            return;
        }

        $meal = $this->nutritionModel->getNutritionById($id);
        if (!$meal) {
            $this->redirectWithError("Meal not found for ID: $id");
            return;
        }

        $detailedMealInfo = $this->getDetailedMealInfo($meal);
        $this->renderView('../../views/nutrition/show.php', ['meal' => $detailedMealInfo]);
    }

    private function getDetailedMealInfo(array $meal): array {
        $meal['ingredients'] = $this->nutritionModel->getIngredientsByMealId($meal['id']);
        $meal['portion_sizes'] = $this->nutritionModel->getPortionSizesByMealId($meal['id']);
        $meal['nutritional_values'] = $this->nutritionModel->getNutritionalValuesByMealId($meal['id']);
        $meal['suggestions'] = $this->getMealSuggestions($meal);
        return $meal;
    }

    private function getMealSuggestions(array $meal): array {
        return ['Consider adding more vegetables', 'Reduce sugar intake'];
    }

    private function renderView(string $viewPath, array $data) {
        if (!file_exists($viewPath) || !is_readable($viewPath)) {
            $this->redirectWithError("View template not found: $viewPath");
            return;
        }
        extract($data);
        include $viewPath;
    }

    private function redirectWithError(string $message) {
        $_SESSION['error_message'] = $message;
        header('Location: ../../views/error.php');
        exit();
    }

    private function isInvalidId(int $id): bool {
        return $id <= 0;
    }

    private function isUserAuthenticated(): bool {
        return isset($_SESSION['user_id']);
    }
}