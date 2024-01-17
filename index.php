<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Components\Database;
use App\Components\Env;
use App\Components\Template;

require_once './vendor/autoload.php';
require_once './src/Components/RouterHelper.php';
require_once './src/routes/routes.php';

// Initialize Components
Env::init();
Database::init();
Template::init();
SimpleRouter::start();
