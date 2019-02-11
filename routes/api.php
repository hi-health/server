<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group([
    'prefix' => 'parameters',
], function ($router) {
    $router->get('cities', 'ParameterController@getCities');
});

Route::group([
    'prefix' => 'settings'
], function($router) {
    $router->get('', 'SettingController@getAll');
    $router->post('', 'SettingController@save');
    $router->post('banners', 'SettingController@saveBanner');
});

Route::group([
    'prefix' => 'users',
], function ($router) {
    $router->post('login', 'UserController@login');
    $router->post('register', 'UserController@register');
    $router->post('sms/code', 'UserController@sendSmsCode');
    $router->delete('account/{account}', 'UserController@deleteByAccount');
    $router->put('{user_id}', 'UserController@update');
    $router->post('{user_id}', 'UserController@update');
    $router->post('{user_id}/online', 'UserController@changeOnline');
    $router->post('{user_id}/offline', 'UserController@changeOffline');
    $router->post('{user_id}/device_token', 'UserController@addToken');
    $router->post('{user_id}/ResetPassword', 'UserController@ResetPassword');
    $router->get('CreateNewPassword', 'UserController@CreateNewPassword');
});

Route::group([
    'prefix' => 'members',
], function ($router) {
    $router->get('{member_id}', 'MemberController@getById');
    $router->get('{member_id}/summary', 'MemberController@getSummary');
    $router->get('{member_id}/requests', 'MemberController@getRequestCollection');
});

Route::group([
    'prefix' => 'doctors',
], function ($router) {
    $router->get('number/{number}', 'DoctorController@getByNumber');
    $router->get('{doctor_id}/services/members', 'DoctorController@getServiceMemberCollection');
    $router->get('{doctor_id}/requests/members', 'DoctorController@getMembersPaginationWithMemberRequestByDoctor');
    $router->get('{doctor_id}', 'DoctorController@getById');
    $router->get('{doctor_id}/summary', 'DoctorController@getSummary');
    $router->post('', 'DoctorController@create');
    $router->post('nearby', 'DoctorController@getCollectionWithNear');
    $router->post('search', 'DoctorController@getCollectionBySearch');
    $router->post('{doctor_id}', 'DoctorController@update');
    $router->put('{doctor_id}', 'DoctorController@update');
});

Route::group([
    'prefix' => 'managers',
], function ($router) {
    $router->post('', 'ManagerController@create');
    $router->post('{manager_id}', 'ManagerController@update');
    $router->put('{manager_id}', 'ManagerController@update');
});

Route::group([
    'prefix' => 'services',
], function ($router) {
    $router->get('{service_id}', 'ServiceController@getById');
    $router->get('{service_id}/videos', 'ServiceController@getUploadedVideo');
    $router->get('{service_id}/invoice', 'ServiceController@getInvoice');
    $router->post('{service_id}/invoice', 'ServiceController@updateOrCreateInvoice');
    $router->get('histories/doctors/{doctor_id}', 'ServiceController@getHistoryByDoctor');
    $router->get('histories/members/{member_id}', 'ServiceController@getHistoryByMember');
    //判斷使用者是否還有未完成的服務
    $router->get('status/members/{member_id}', 'ServiceController@getStatusByMember');
    $router->post('', 'ServiceController@create');
    $router->post('{service_id}/payment', 'ServiceController@setPayment');
    $router->post('{service_id}/payment/confirm', 'ServiceController@invoiceOrCancelPayment');
    $router->post('{service_id}/treatment', 'ServiceController@setTreatment');
    $router->post('{service_id}/start', 'ServiceController@setStartedAt');
    $router->post('{service_id}/stop', 'ServiceController@setStoppedAt');
    $router->post('{service_id}/export', 'ServiceController@exportHistoryById');
    $router->post('export/doctors/{doctor_id}', 'ServiceController@exportHistoryByDoctor');
    // Service Plans
    $router->group([
        'prefix' => '{service_id}/plans',
    ], function($router) {
        $router->get('', 'ServicePlanController@getAll');
        $router->post('', 'ServicePlanController@updateOrCreate');
        $router->post('update', 'ServicePlanController@updateOrCreate');
        $router->post('delete', 'ServicePlanController@delete');
        $router->post('{plan_id}/videos/delete', 'ServicePlanController@deleteVideos');
        $router->put('', 'ServicePlanController@updateOrCreate');
        $router->delete('', 'ServicePlanController@delete');
        
        $router->delete('{plan_id}/videos', 'ServicePlanController@deleteVideos');
        $router->get('{plan_id}/videos/{video_id}/activation_flag', 'ServicePlanController@getActivationFlag');
        $router->post('{plan_id}/videos/{video_id}/activate_record', 'ServicePlanController@updateActivationFlag');
        $router->post('{plan_id}/videos/{video_id}/template', 'ServicePlanController@updateOrCreateTemplate');

        // Service Plan Daily
        $router->get('daily', 'ServicePlanDailyController@getAllDate');
        $router->get('daily/{date}', 'ServicePlanDailyController@getByDate');
        $router->post('{plan_id}/daily', 'ServicePlanDailyController@updateOrCreate');
        $router->post('{plan_id}/daily/update', 'ServicePlanDailyController@updateOrCreate');
        $router->put('{plan_id}/daily', 'ServicePlanDailyController@updateOrCreate');
        $router->post('export', 'ServicePlanDailyController@export');
    });
});

Route::group([
    'prefix' => 'requests',
], function ($router) {
    $router->get('', 'MemberRequestController@getPagination');
    $router->get('{request_id}', 'MemberRequestController@getById');
    $router->get('members/{member_id}', 'MemberRequestController@getCollectionByMember');
    $router->post('members/{member_id}', 'MemberRequestController@add');
});

Route::group([
    'prefix' => 'notes',
], function ($router) {
    $router->get('/doctors/{doctor_id}/members/{member_id}/latest', 'NoteController@getLatest');
    $router->post('', 'NoteController@save');
});

Route::group([
    'prefix' => 'messages',
], function ($router) {
    $router->get('/doctors/{doctor_id}/members/{member_id}', 'MessageController@getChunk');
    $router->post('', 'MessageController@send');
});

Route::group([
    'prefix' => 'treatments',
], function ($router) {
    //    $router->get('list', 'TreatmentController@getList');
//    $router->get('{treatment_id}/daily', 'TreatmentController@getDaily');
//    $router->get('{treatment_id}/notes', 'TreatmentController@getNotesCollection');
//    $router->post('{treatment_id}/tasks', 'TreatmentController@addTask');
//    $router->post('{treatment_id}/pay', 'TreatmentController@pay');
});
Route::group([
    'prefix' => 'point',
], function ($router) {
    $router->get('{users_id}/history', 'PointController@getHistoryByUsersId');
    $router->get('{users_id}/total', 'PointController@getRemainedPoint');
    $router->post('{users_id}/transfer', 'PointController@PointTransfer');
    $router->get('getAllPoint', 'PointController@getAllPoint');
});

Route::group([
    'prefix' => 'ai_develop',
], function ($router) {
    $router->get('{video_id}/{daily_id}', 'ServicePlanDailyController@AIDevelop');
});
