<?php

use Pecee\SimpleRouter\SimpleRouter;
use Elemizer\App\Controllers\HomeController;

SimpleRouter::get('/', [HomeController::class, 'home']);
SimpleRouter::get('/about', [HomeController::class, 'about']);

include_once './src/routes/api.php';
