<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\MasterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CountryController;
use App\Http\Controllers\Admin\StateController;
use App\Http\Controllers\Admin\CityController;
use Illuminate\Support\Facades\Artisan;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/clear-caches', function () {
    Artisan::call('route:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:cache');
    Artisan::call('config:cache');
    Artisan::call('optimize:clear');
    return 'Caches cleared and optimized!';
});

Route::group(['middleware' => ['auth']], function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::get('roles-list', [RoleController::class, 'roleList'])->name('roles.list');
    Route::resource('roles', RoleController::class);
    Route::get('permissions-list', [PermissionController::class, 'permissionsList'])->name('permissions.list');
    Route::resource('permissions', PermissionController::class);
    Route::get('userList', [UserController::class, 'userList'])->name('users.list');
    Route::resource('users', UserController::class);
    Route::get('activity-logs',       [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])->name('activity-logs.show');
    Route::get('customers-list', [CustomerController::class,'list'])->name('customers.list');
    Route::resource('customers', CustomerController::class);

    // Master data consolidated index
    Route::get('master', [MasterController::class, 'index'])->name('master.index');
    Route::get('countries-list', [CountryController::class,'list'])->name('country.list');
    Route::resource('country', CountryController::class);
    Route::get('states-list', [StateController::class,'list'])->name('state.list');
    Route::resource('state', StateController::class);
    Route::get('cities-list', [CityController::class,'list'])->name('city.list');
    Route::resource('city', CityController::class);

    // Admin profile (admin area)
    Route::get('admin/profile', [AdminController::class, 'edit'])->name('admin.profile.edit');
    Route::get('admin/profile/password', [AdminController::class, 'password'])->name('admin.profile.password');
    Route::patch('admin/profile', [AdminController::class, 'update'])->name('admin.profile.update');
    Route::post('admin/profile/password', [AdminController::class, 'updatePassword'])->name('admin.profile.updatePassword');
});

require __DIR__.'/auth.php';
