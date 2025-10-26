<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;

Route::get('/test-reset-process/{email}', function ($email) {
    try {
        $controller = new LoginController();
        
        // Simular el request
        $request = new \Illuminate\Http\Request();
        $request->merge(['email' => $email]);
        
        // Llamar al método directamente
        $response = $controller->sendResetLinkEmail($request);
        
        return [
            'success' => true,
            'message' => 'Proceso de reset completado',
            'response_type' => get_class($response),
            'session_status' => session('status')
        ];
        
    } catch (\Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
    }
});