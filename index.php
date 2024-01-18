<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Components\Database;
use App\Components\Env;
use App\Components\Template;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Components/RouterHelper.php';
require_once __DIR__ . '/src/routes/routes.php';

// Initialize Components
Env::init();
Database::init();
Template::init();
SimpleRouter::start();
