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
    //'middleware' => ['auth:admin'],
], function ($router) {
    $router->get('dashboard', 'DashboardController@showDashboardPage')
        ->name('dashboard');

    $router->group([
        'prefix' => 'doctors',
    ], function ($router) {
        $router->get('', 'DoctorController@showListPage')
            ->name('admin-doctors-list');
        $router->get('add', 'DoctorController@showAddForm')
            ->name('admin-doctors-add-form');
        $router->get('{doctor_id}', 'DoctorController@showEditForm')
            ->name('admin-doctors-edit-form');
    });

    $router->group([
        'prefix' => 'members',
    ], function ($router) {
        $router->get('', 'MemberController@showListPage')
            ->name('admin-members-list');
        $router->get('{member_id}', 'MemberController@showDetailPage')
            ->name('admin-members-detail');
    });

    $router->group([
        'prefix' => 'services',
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
        'prefix' => 'videos'
    ], function($router) {
        $router->get('', 'VideoController@showListPage')
            ->name('admin-videos-list');
        $router->get('service/{service_id}', 'VideoController@showDetailPage')
            ->name('admin-videos-detail');
    });
    
    $router->group([
        'prefix' => 'settings'
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
