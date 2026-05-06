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
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\SizeController;
// use App\Http\Controllers\Admin\SetController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\ItemMasterController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\OrderMasterController;
use App\Http\Controllers\Admin\SubGroupController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\FrontendController;    
use Illuminate\Support\Facades\Artisan;

//Route::get('/', function () {
  //  return view('frontend.home');
//});
Route::get('/', [FrontendController::class, 'home'])->name('home');


Route::get('/products', [FrontendController::class, 'products'])->name('products');
Route::get('/category/{category}', [FrontendController::class, 'category'])->name('category.show');
Route::get('/categories', [FrontendController::class, 'categories'])->name('categories');
Route::get('/api/category/{category}/items', [FrontendController::class, 'categoryItems'])->name('api.category.items');
Route::get('/products/{item}', [FrontendController::class, 'show'])->name('products.show');

Route::view('/about', 'frontend.about')->name('about');
Route::view('/contact', 'frontend.contact')->name('contact');
//Route::get('/catalog', [ItemController::class, 'catalogs'])->name('catalog');

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
    // AJAX helper to fetch customer addresses for order form
    Route::get('/customer/{id}', [CustomerController::class, 'getCustomer'])->name('customer.get');

    // Master data consolidated index
    Route::get('master', [MasterController::class, 'index'])->name('master.index');
    Route::get('countries-list', [CountryController::class,'list'])->name('country.list');
    Route::resource('country', CountryController::class);
    Route::get('states-list', [StateController::class,'list'])->name('state.list');
    Route::resource('state', StateController::class);
    Route::get('cities-list', [CityController::class,'list'])->name('city.list');
    Route::resource('city', CityController::class);
    Route::get('states-by-country', [CityController::class, 'getStatesByCountry'])->name('states.by.country');
     Route::get('item-master', [ItemMasterController::class, 'index'])->name('item-master.index');
    Route::get('category-list', [CategoryController::class,'list'])->name('category.list');
    Route::resource('category', CategoryController::class);
    Route::get('group-list', [GroupController::class,'list'])->name('group.list');
    Route::resource('group', GroupController::class);
    Route::get('size-list', [SizeController::class,'list'])->name('size.list');
    Route::resource('size', SizeController::class);
    // Route::get('set-list', [SetController::class,'list'])->name('set.list');
    // Route::resource('set', SetController::class);
    Route::get('items-list', [ItemController::class,'itemList'])->name('items.list');
    Route::resource('items', ItemController::class);
    Route::get('orders-list', [OrderMasterController::class, 'orderList'])->name('orders.list');
    Route::get('customers/{user}/addresses', [OrderMasterController::class, 'customerAddresses'])->name('customers.addresses');
    Route::resource('orders', OrderMasterController::class);
    Route::post('orders/{order}/approve-distributor', [OrderMasterController::class, 'approveByDistributor'])->name('orders.approve.distributor');
    Route::post('order-items/{orderItem}/status', [OrderMasterController::class, 'updateItemStatus'])->name('order-items.status.update');
    Route::get('color-list', [ColorController::class,'list'])->name('color.list');
    Route::resource('color', ColorController::class);
    Route::get('sub-category-list', [SubCategoryController::class,'list'])->name('sub-category.list');
    Route::resource('sub-category', SubCategoryController::class);
    Route::get('sub-group-list', [SubGroupController::class,'list'])->name('sub-group.list');
    Route::resource('sub-group', SubGroupController::class);


    // Admin profile (admin area)
    Route::get('admin/profile', [AdminController::class, 'edit'])->name('admin.profile.edit');
    Route::get('admin/profile/password', [AdminController::class, 'password'])->name('admin.profile.password');
    Route::patch('admin/profile', [AdminController::class, 'update'])->name('admin.profile.update');
    Route::post('admin/profile/password', [AdminController::class, 'updatePassword'])->name('admin.profile.updatePassword');
  //catalog routes (protected by module access middleware)
  Route::get('/catalog', [ItemController::class, 'catalog'])
    ->name('catalog')
    ->middleware(\App\Http\Middleware\EnsureModuleAccess::class . ':catalog');

  Route::get('/catalog/{item}', [ItemController::class, 'showCatalog'])
    ->name('catalog.show')
    ->middleware(\App\Http\Middleware\EnsureModuleAccess::class . ':catalog');

 

});

require __DIR__.'/auth.php';
