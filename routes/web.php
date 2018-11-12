<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// 使用網頁看 export_by_id.blade.php(匯出課表的view)
// Route::group([
//     'namespace' => 'Admin',
// ], function ($router) {
//     $router->get('email/{service_id}', 'ServiceController@email');
// });        

Route::get('/shops.html', 'APIs\ParameterController@getClinic');

Route::group([
    'prefix' => 'admin',
    'namespace' => 'Admin',
], function ($router) {
    $router->get('login', 'AdminController@showLoginForm')
        ->name('admin-login');
    $router->post('auth', 'AdminController@loginAuth')
        ->name('admin-login-auth');
    $router->post('logout', 'AdminController@logout')
        ->name('admin-logout');
});

Route::group([
    'prefix' => 'admin',
    'namespace' => 'Admin',
    'middleware' => ['auth:admin,manager'],
], function ($router) {
    $router->get('dashboard', 'DashboardController@showDashboardPage')
        ->name('dashboard')->middleware('auth:admin');

    $router->group([
        'prefix' => 'doctors',
    ], function ($router) {
        $router->get('', 'DoctorController@showListPage')
            ->name('admin-doctors-list')->middleware('auth:admin');
        $router->get('add', 'DoctorController@showAddForm')
            ->name('admin-doctors-add-form');
        $router->get('{doctor_id}', 'DoctorController@showEditForm')
            ->name('admin-doctors-edit-form')->middleware('auth:admin');
    });

    $router->group([
        'prefix' => 'members',
        'middleware' => ['auth:admin'],
    ], function ($router) {
        $router->get('', 'MemberController@showListPage')
            ->name('admin-members-list');
        $router->get('{member_id}', 'MemberController@showDetailPage')
            ->name('admin-members-detail');
    });

    $router->group([
        'prefix' => 'services',
        'middleware' => ['auth:admin'],
    ], function ($router) {
        $router->get('', 'ServiceController@showListPage')
            ->name('admin-services-list');
        $router->get('{service_id}', 'ServiceController@showDetailPage')
            ->name('admin-services-detail');
        $router->get('doctors/{doctor_id}', 'ServiceController@showListByDoctorPage')
            ->name('admin-services-list-by-doctor');
        $router->get('members/{member_id}', 'ServiceController@showListByMemberPage')
            ->name('admin-services-list-by-member');
        $router->get('month/this', 'ServiceController@showListByThisMonthPage')
            ->name('admin-services-list-by-thismonth');    
    });
    
    $router->group([
        'prefix' => 'videos',
        'middleware' => ['auth:admin'],
    ], function($router) {
        $router->get('', 'VideoController@showListPage')
            ->name('admin-videos-list');
        $router->get('service/{service_id}', 'VideoController@showDetailPage')
            ->name('admin-videos-detail');
    });

    $router->group([
        'prefix' => 'managers',
        'middleware' => ['auth:admin'],
    ], function ($router) {
        $router->get('', 'ManagerController@showListPage')
            ->name('admin-managers-list');
        $router->get('add', 'ManagerController@showAddForm')
            ->name('admin-managers-add-form');
        $router->get('{manager_id}', 'ManagerController@showEditForm')
            ->name('admin-managers-edit-form');
    });
    
    $router->group([
        'prefix' => 'settings',
        'middleware' => ['auth:admin'],
    ], function($router) {
        $router->get('', 'SettingController@showSettingsForm')
            ->name('admin-settins-form');
    });
});

Route::group([
    'prefix' => 'point',
    'namespace' => 'APIs',
], function ($router) {
    $router->get('{users_id}', 'PointController@index')
            ->name('point-index');
        $router->get('{users_id}/get', 'PointController@showListProduce')
            ->name('point-list-produce');
        $router->get('{users_id}/use', 'PointController@showListConsume')
            ->name('point-list-consume');
        $router->get('{users_id}/all', 'PointController@showListAllTransaction')
            ->name('point-list-all-transaction'); 
        $router->get('{users_id}/transfer', 'PointController@showTransfer')  
            ->name('point-transfer');
        $router->post('{users_id}/transfer', 'PointController@PointTransfer');
});

Route::group([
    'prefix' => 'services',
], function ($router) {
    $router->get('{service_id}/purchase', 'ServiceController@showPurchaseForm')
        ->name('services_purchase');
    $router->post('{service_id}/return', 'ServiceController@returnProcess')
        ->name('services_purchase_return');
    $router->get('{service_id}/success', 'ServiceController@showSuccessPage')
        ->name('services_purchase_success');
    $router->get('{service_id}/failure', 'ServiceController@showFailurePage')
        ->name('services_purchase_failure');
});

Route::group([
    'prefix' => 'pay2go',
], function ($router) {
    $router->post('notify', 'Pay2GoController@notifyProcess')
        ->name('pay2go_notify');
});
