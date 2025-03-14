<?php
// routes.php
return [
    '/' => ['controller' => 'Home', 'action' => 'index'],
    'dashboard' => ['controller' => 'Dashboard', 'action' => 'index'],
    'admin/dashboard' => ['controller' => 'AdminController\Dashboard', 'action' => 'index'],
    'admin/statistics' => ['controller' => 'AdminController\Statistics', 'action' => 'index'],
    'admin/user_management' => ['controller' => 'AdminController\UserManagement', 'action' => 'index'],
    'admin/user_management/view/{id}' => ['controller' => 'AdminController\UserManagement', 'action' => 'viewUserDetails'],
    'admin/user_management/bulk_activate' => ['controller' => 'AdminController\UserManagement', 'action' => 'bulkActivateUsers'],
    'auth/login' => ['controller' => 'AuthController\Login', 'action' => 'login'],
    'auth/logout' => ['controller' => 'AuthController\Logout', 'action' => 'logout'],
    'auth/signup' => ['controller' => 'AuthController\Signup', 'action' => 'signup'],
    'nutrition/index' => ['controller' => 'NutritionController\Index', 'action' => 'index'],
    'nutrition/create' => ['controller' => 'NutritionController\Create', 'action' => 'create'],
    'nutrition/edit/{id}' => ['controller' => 'NutritionController\Edit', 'action' => 'edit'],
    'nutrition/update/{id}' => ['controller' => 'NutritionController\Edit', 'action' => 'update'],
    'nutrition/delete/{id}' => ['controller' => 'NutritionController\Delete', 'action' => 'delete'],
    'nutrition/show/{id}' => ['controller' => 'NutritionController\Show', 'action' => 'show'],
    'progress/index' => ['controller' => 'ProgressController\Index', 'action' => 'index'],
    'progress/create' => ['controller' => 'ProgressController\Create', 'action' => 'create'],
    'progress/edit/{id}' => ['controller' => 'ProgressController\Edit', 'action' => 'edit'],
    'progress/update/{id}' => ['controller' => 'ProgressController\Edit', 'action' => 'update'],
    'progress/delete/{id}' => ['controller' => 'ProgressController\Delete', 'action' => 'delete'],
    'progress/show/{id}' => ['controller' => 'ProgressController\Show', 'action' => 'show'],
    'user/profile' => ['controller' => 'UserController\Profile', 'action' => 'show'],
    'user/edit/{id}' => ['controller' => 'UserController\Edit', 'action' => 'edit'],
    'user/update/{id}' => ['controller' => 'UserController\Edit', 'action' => 'update'],
    'user/delete/{id}' => ['controller' => 'UserController\Delete', 'action' => 'delete'],
    'workout/index' => ['controller' => 'WorkoutController\Index', 'action' => 'index'],
    'workout/create' => ['controller' => 'WorkoutController\Create', 'action' => 'create'],
    'workout/edit/{id}' => ['controller' => 'WorkoutController\Edit', 'action' => 'edit'],
    'workout/update/{id}' => ['controller' => 'WorkoutController\Edit', 'action' => 'update'],
    'workout/delete/{id}' => ['controller' => 'WorkoutController\Delete', 'action' => 'delete'],
    'workout/show/{id}' => ['controller' => 'WorkoutController\Show', 'action' => 'show'],
    'workout/custom' => ['controller' => 'WorkoutController\Custom', 'action' => 'createCustomWorkout'],
    'language/change' => ['controller' => 'Language', 'action' => 'changeLanguage'],
    'fitness/index' => ['controller' => 'Fitness', 'action' => 'index'],
    'fitness/calculate' => ['controller' => 'Fitness', 'action' => 'calculate'],
    'fitness/results' => ['controller' => 'Fitness', 'action' => 'results'],
    'fitness/workout' => ['controller' => 'Fitness', 'action' => 'workout'],
    'fitness/nutrition' => ['controller' => 'Fitness', 'action' => 'nutrition'],
];