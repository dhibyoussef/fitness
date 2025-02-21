<?php


// Check if a meal plan ID is provided
if (!isset($_GET['id'])) {
    header("Location: nutrition_index.php");
    exit();
}

$plan_id = $_GET['id'];

// In a real application, you would delete the meal plan from the database here
// For this example, we'll just simulate the deletion

// Simulating database interaction
$success = true; // Assume the deletion was successful

if ($success) {
    $_SESSION['message'] = "Meal plan deleted successfully.";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Error deleting meal plan. Please try again.";
    $_SESSION['message_type'] = "error";
}

// Redirect back to the nutrition index page
header("Location: ../../views/nutrition/index.php");
exit();
?>