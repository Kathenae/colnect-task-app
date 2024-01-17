<?php

use Pecee\SimpleRouter\SimpleRouter;
use App\Controllers\CountAPIController;

SimpleRouter::group(['prefix' => "/api"], function () {
   SimpleRouter::post('/count-elements', [CountAPIController::class, 'index']);
});
