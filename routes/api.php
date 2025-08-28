<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoutineController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UserRoutineController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;


Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

// Rutas protegidas
Route::middleware(['auth:sanctum', 'inactivity'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);


    //------------------------------------------------- USERS -------------------------------------------------//
    // 👇 Solo Super (1) y Admin (2) pueden crear usuarios
    Route::middleware('role:1,2')->post('/users', [UserController::class, 'store']);
    // Listar y ver (permitido: 1,2,3)
    Route::middleware('role:1,2,3')->get('/users', [UserController::class, 'index']);
    Route::middleware('role:1,2,3')->get('/users/{id_number}', [UserController::class, 'show']);
    
    // Editar y borrar (solo: 1,2)
    Route::middleware('role:1,2')->put('/users/{id_number}', [UserController::class, 'update']);
    Route::middleware('role:1,2')->delete('/users/{id_number}', [UserController::class, 'destroy']);
    

    //------------------------------------------------- PAGOS -------------------------------------------------//
    // payments: listar y ver (roles 1,2,3)
    Route::middleware('role:1,2,3')->get('/payments', [PaymentController::class, 'index']);
    Route::middleware('role:1,2,3')->get('/payments/{id}', [PaymentController::class, 'show']);
    Route::middleware('role:1,2,3')->get('/users/{id_number}/payments', [PaymentController::class, 'byUser']);

    // payment: crear/editar/eliminar (roles 1,2)
    Route::middleware('role:1,2')->post('/payments', [PaymentController::class, 'store']);
    Route::middleware('role:1,2')->put('/payments/{id}', [PaymentController::class, 'update']);
    Route::middleware('role:1,2')->delete('/payments/{id}', [PaymentController::class, 'destroy']);
    

    //------------------------------------------------- PLANES -------------------------------------------------//
    Route::middleware('role:1,2,3')->get('/plans', [PlanController::class, 'index']);
    Route::middleware('role:1,2')->post('/plans', [PlanController::class, 'store']);
    Route::middleware('role:1,2')->put('/plans/{id}', [PlanController::class, 'update']);
    Route::middleware('role:1,2')->delete('/plans/{id}', [PlanController::class, 'destroy']);
    Route::middleware('role:1,2,3')->get('/plans/{id}', [PlanController::class, 'show']);

    
    //------------------------------------------------- ASISTENCIAS -------------------------------------------------//
    Route::middleware('role:1,2,3')->get('/attendances', [AttendanceController::class, 'index']);
    Route::middleware('role:1,2,3')->post('/attendances', [AttendanceController::class, 'store']);


    //------------------------------------------------- RUTINAS -------------------------------------------------//
    // Routines: CRUD (solo 1,2). Consulta (1,2,3) con los GET.
    Route::middleware('role:1,2,3')->get('/routines', [RoutineController::class, 'index']);
    Route::middleware('role:1,2,3')->get('/routines/{id_routines}', [RoutineController::class, 'show']);

    Route::middleware('role:1,2')->post('/routines', [RoutineController::class, 'store']);
    Route::middleware('role:1,2')->put('/routines/{id_routines}', [RoutineController::class, 'update']);
    Route::middleware('role:1,2')->delete('/routines/{id_routines}', [RoutineController::class, 'destroy']);


    //------------------------------------------------- ASIGNACION DE RUTINAS -------------------------------------------------//
    // User Routines: asignar (1,2). Historial/actual (1,2,3).
    // ASIGNACION DE RUTINA
    Route::middleware('role:1,2')->post('/user-routines/assign', [UserRoutineController::class, 'store']);
    // Historial de rutinas 
    Route::middleware('role:1,2,3')->get('/user-routines/history', [UserRoutineController::class, 'history']);
    

    //------------------------------------------------- KPIS -------------------------------------------------//
    Route::middleware('role:1,2,3')->get('/dashboard/kpis', [DashboardController::class, 'kpis']);
    Route::middleware('role:1,2,3')->get('/dashboard/alerts', [DashboardController::class, 'alerts']);


    //------------------------------------------------- Reportes -------------------------------------------------//
    Route::middleware('role:1,2')->get('/reports/revenue', [ReportController::class, 'revenue']);
    Route::middleware('role:1,2')->get('/reports/attendances', [ReportController::class, 'attendancesPdf']);
    Route::middleware('role:1,2')->get('/reports/payments', [ReportController::class, 'paymentsPdf']);
    

    //------------------------------------------------- settings -------------------------------------------------//
    Route::middleware('role:1,2')->get('/settings', [SettingsController::class, 'show']);
    Route::middleware('role:1,2')->put('/settings', [SettingsController::class, 'update']);
    

    //------------------------------------------------- backup y restore -------------------------------------------------//
    // routes/api.php (dentro de auth:sanctum)
    Route::middleware('role:1')->post('/backup', [BackupController::class, 'backup']);
    Route::middleware('role:1,2')->get('/backup/last', [BackupController::class, 'last']);
    Route::middleware('role:1')->post('/restore', [BackupController::class, 'restore']);
    
    
    //------------------------------------------------- cambio de contraseña -------------------------------------------------//
    Route::middleware('role:1,2')->put('/me/password', [AuthController::class, 'changePassword']);
    
}); 


// AUTH
// Route::post('/register', [AuthController::class, 'register']);

// USUARIOS
// Route::get('/users', [UserController::class, 'index']);
// Route::post('/users', [UserController::class, 'store']);
// Route::get('/users/{id_number}', [UserController::class, 'show']);
// Route::put('/users/{id_number}', [UserController::class, 'update']);
// Route::delete('/users/{id_number}', [UserController::class, 'destroy']);

// PAGOS
// Route::get('/payments', [PaymentController::class, 'index']);
// Route::post('/payments', [PaymentController::class, 'store']);
// Route::get('/payments/{id}', [PaymentController::class, 'show']);
// Route::put('/payments/{id}', [PaymentController::class, 'update']);
// Route::delete('/payments/{id}', [PaymentController::class, 'destroy']);

// ASISTENCIAS
// Route::get('/attendances', [AttendanceController::class, 'index']);
// Route::post('/attendances', [AttendanceController::class, 'store']);

// // RUTINAS
// Route::get('/routines', [RoutineController::class, 'index']);
// Route::post('/routines', [RoutineController::class, 'store']);
// Route::put('/routines/{id_routines}', [RoutineController::class, 'update']);
// Route::get('/routines/{id_routines}', [RoutineController::class, 'show']);
// Route::delete('/routines/{id_routines}', [RoutineController::class, 'destroy']);

// ASIGNACION DE RUTINA
// Route::post('/user-routines/assign', [UserRoutineController::class, 'store']);
// Historial de rutinas 
// Route::get('/user-routines/history', [UserRoutineController::class, 'history']);
