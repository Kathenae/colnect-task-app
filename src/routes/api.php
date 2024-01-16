<?php

use Pecee\SimpleRouter\SimpleRouter;
use Elemizer\App\Controllers\CountAPIController;

SimpleRouter::group(['prefix' => "/api"], function () {
   SimpleRouter::post('/count-elements', [CountAPIController::class, 'index']);
});
