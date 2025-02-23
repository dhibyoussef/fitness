<?php
// app/controllers/FitnessController.php
require_once __DIR__ . '/../models/ExerciseModel.php';
require_once __DIR__ . '/../controllers/BaseController.php';
require_once __DIR__ . '/../../../config/database.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class FitnessController extends BaseController {
    private ExerciseModel $exerciseModel;
    private Logger $logger;

    public function __construct(PDO $pdo) {
        parent::__construct($pdo);
        $this->exerciseModel = new ExerciseModel($pdo);
        $this->logger = new Logger('FitnessController');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::INFO));
        $this->requireAuth();
    }

    public function index(): void {
        $userId = (int)$_SESSION['user_id'];
        $exercises = $this->exerciseModel->getUserExercises($userId);
        $goals = $this->exerciseModel->getGoals($userId);
        $this->render('fitness/index', [
            'exercises' => $exercises,
            'goals' => $goals,
            'csrf_token' => $this->generateCsrfToken(),
            'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
        ]);
    }

    public function calculate(): void {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isValidCsrfToken($_POST['csrf_token'] ?? '')) {
                throw new Exception('Invalid request or security token.');
            }

            $userId = (int)$_SESSION['user_id'];
            $data = $_POST;
            $exerciseId = (int)($data['exercise_id'] ?? 0);
            $exercise = $this->exerciseModel->getExerciseById($exerciseId, $userId);

            if (!$exercise) {
                throw new Exception('Exercise not found.');
            }

            $results = [
                'load' => $this->calculateLoad($exercise['one_rm']),
                'one_rm' => $this->calculate1RM((float)($data['weight'] ?? 0), (int)($data['reps'] ?? 0)),
                'warmup' => $this->calculateWarmup($exercise['one_rm']),
                'tdee' => $this->calculateTDEE($data),
                'macros' => $this->calculateMacros((float)($data['tdee'] ?? 0), $data['goal'] ?? 'maintain')
            ];

            $this->render('fitness/results', array_merge($results, [
                'exercise' => $exercise,
                'csrf_token' => $this->generateCsrfToken(),
                'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
            ]));
        } catch (Exception $e) {
            $this->logger->error("Fitness calculation error", [
                'message' => $e->getMessage(),
                'user_id' => $_SESSION['user_id'] ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            $this->setFlashMessage('error', $e->getMessage());
            $this->redirect('/fitness/index');
        }
    }

    private function calculateLoad(float $oneRm): array {
        $percentages = range(10, 200, 5);
        return array_map(function ($pct) use ($oneRm) {
            return ['percent' => $pct, 'weight' => round($oneRm * ($pct / 100), 1)];
        }, $percentages);
    }

    private function calculate1RM(float $weight, int $reps): float {
        if ($reps <= 0 || $weight <= 0) return 0;
        return round($weight * (1 + $reps / 30), 1); // Epley formula
    }

    private function calculateWarmup(float $oneRm): array {
        return [
            ['set' => 1, 'percent' => 50, 'weight' => round($oneRm * 0.5, 1), 'reps' => 8, 'rest' => '2m'],
            ['set' => 2, 'percent' => 60, 'weight' => round($oneRm * 0.6, 1), 'reps' => 5, 'rest' => '2m'],
            ['set' => 3, 'percent' => 70, 'weight' => round($oneRm * 0.7, 1), 'reps' => 3, 'rest' => '3m'],
            ['set' => 4, 'percent' => 80, 'weight' => round($oneRm * 0.8, 1), 'reps' => 1, 'rest' => '3m'],
            ['set' => 5, 'percent' => 90, 'weight' => round($oneRm * 0.9, 1), 'reps' => 1, 'rest' => '3-5m'],
            ['set' => 6, 'percent' => 102, 'weight' => round($oneRm * 1.02, 1), 'reps' => 1, 'rest' => '5-7m']
        ];
    }

    private function calculateTDEE(array $data): array {
        $gender = $data['gender'] ?? 'male';
        $age = (int)($data['age'] ?? 0);
        $height = (float)($data['height'] ?? 0);
        $weight = (float)($data['weight'] ?? 0);
        $bodyFat = isset($data['body_fat']) && $data['body_fat'] !== '' ? (float)$data['body_fat'] : null;
        $activity = $data['activity'] ?? 'moderate';

        $activityMultipliers = [
            'sedentary' => 1.2,
            'light' => 1.375,
            'moderate' => 1.55,
            'very' => 1.725,
            'extra' => 1.9
        ];

        if ($bodyFat !== null) {
            $lbm = $weight * (1 - $bodyFat / 100); // Lean body mass
            $bmr = 370 + (21.6 * $lbm); // Katch-McArdle
        } else {
            $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age); // Mifflin-St Jeor
            $bmr += ($gender === 'male' ? 5 : -161);
        }

        $tdee = round($bmr * ($activityMultipliers[$activity] ?? 1.55));
        $bmi = $weight && $height ? round($weight / (($height / 100) ** 2), 1) : 0;

        return [
            'bmr' => round($bmr),
            'tdee' => $tdee,
            'bmi' => $bmi,
            'bulk' => $tdee + 275,
            'cut' => $tdee - 275,
            'maintain' => $tdee
        ];
    }

    private function calculateMacros(float $tdee, string $goal): array {
        $calories = $goal === 'bulk' ? $tdee + 275 : ($goal === 'cut' ? $tdee - 275 : $tdee);
        $macros = [
            'cut' => ['carbs' => 0.4, 'protein' => 0.4, 'fat' => 0.2],
            'bulk' => ['carbs' => 0.55, 'protein' => 0.25, 'fat' => 0.2],
            'maintain' => ['carbs' => 0.5, 'protein' => 0.3, 'fat' => 0.2]
        ];

        $plan = $macros[$goal] ?? $macros['maintain'];
        return [
            'carbs' => round(($calories * $plan['carbs']) / 4),
            'protein' => round(($calories * $plan['protein']) / 4),
            'fat' => round(($calories * $plan['fat']) / 9),
            'total_calories' => $calories
        ];
    }
}