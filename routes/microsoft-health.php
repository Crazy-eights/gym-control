<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HealthCheckController;

Route::get('/microsoft-health-check', [HealthCheckController::class, 'microsoftHealthCheck']);
