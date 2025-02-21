<?php


// Check if a workout ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$workout_id = $_GET['id'];

// In a real application, you would delete the workout from the database here
// For this example, we'll just simulate the deletion

// Simulating database interaction
$success = true; // Assume the deletion was successful

if ($success) {
    $_SESSION['message'] = "Workout deleted successfully.";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Error deleting workout. Please try again.";
    $_SESSION['message_type'] = "error";
}

// Redirect back to the workout index page
header("Location: workout_index.php");
exit();
?>