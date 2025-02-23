<?php
// Dynamically generate navigation links based on user authentication status
if ($baseController->isAuthenticated()) {
    echo '<nav>';
    echo '<ul>';
    echo '<li><a href="/user/profile">Profile</a></li>';
    echo '<li><a href="/fitness/index">Fitness</a></li>';
    if ($baseController->isAdmin()) {
        echo '<li><a href="/admin/dashboard">Admin</a></li>';
    }
    echo '<li><form action="/auth/logout" method="POST">';
    echo '<input type="hidden" name="csrf_token" value="' . $baseController->generateCsrfToken() . '">';
    echo '<button type="submit">Logout</button>';
    echo '</form></li>';
    echo '</ul>';
    echo '</nav>';
} else {
    echo '<nav>';
    echo '<ul>';
    echo '<li><a href="/auth/login">Login</a></li>';
    echo '<li><a href="/auth/signup">Signup</a></li>';
    echo '</ul>';
    echo '</nav>';
}
?>