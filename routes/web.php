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

/* Route::get('/', function () {
    return redirect('home');
}); */


Auth::routes();

Route::group(['middleware' => ['web']], function(){

    Route::group(['prefix' => 'admin', 'middleware' => ['role:admin']], function() {
        //Agency routes
        Route::get('/agency/index', ['middleware' => ['permission:admin-permission'], 'uses' => 'AgencyController@index']);
        Route::post('/agency/getAgencies', ['middleware' =>  ['permission:admin-permission'], 'uses' => 'AgencyController@getAgencies']);
        //Route::get('/agency/create', ['middleware' =>  ['permission:admin-permission'], 'as'=> 'agency.create', 'uses' => 'AgencyController@create']);
        Route::get('/agency/edit', ['middleware' =>  ['permission:admin-permission'], 'as'=> 'agency.edit', 'uses' => 'AgencyController@edit']);
        //Route::post('/agency/add', ['middleware' =>  ['permission:admin-permission'], 'as'=> 'agency.add', 'uses' => 'AgencyController@add']);
        Route::post('/agency/update/{id}', ['middleware' =>  ['permission:admin-permission'], 'as'=> 'agency.update', 'uses' => 'AgencyController@update']);
        //Route::post('/agency/delete/{id}', ['middleware' =>  ['permission:admin-permission'], 'as'=> 'agency.delete', 'uses' => 'AgencyController@delete']);
		
        // Users routes
        Route::get('/user/index', ['middleware' =>  ['permission:admin-permission'], 'uses' => 'UserController@index']);
        Route::post('/user/getUsers', ['middleware' =>  ['permission:admin-permission'], 'uses' => 'UserController@getUsers']);
        Route::get('/user/create', ['middleware' =>  ['permission:admin-permission'], 'as'=> 'user.create','uses' => 'UserController@create']);
        Route::get('/user/edit', ['middleware' =>  ['permission:admin-permission'], 'as'=> 'user.edit','uses' => 'UserController@edit']);
        Route::post('/user/add', ['middleware' =>  ['permission:admin-permission'], 'as'=> 'user.add','uses' => 'UserController@add']);
        Route::post('/user/update/{id}', ['middleware' => ['permission:admin-permission'], 'as'=> 'user.update','uses' => 'UserController@update']);
        Route::post('/user/delete/{id}', ['middleware' =>  ['permission:admin-permission'], 'as'=> 'user.delete','uses' => 'UserController@delete']);
        Route::post('/user/resendPassword', ['middleware' =>  ['permission:admin-permission'], 'as'=> 'user.resendPassword','uses' => 'UserController@resendPassword']);

        // Schedule routes
        Route::get('/schedule/index', ['middleware' =>  ['permission:admin-permission'],'uses' => 'ScheduleController@index']);
        Route::post('/schedule/saveScheduleData', ['middleware' =>  ['permission:admin-permission'],'as'=> 'schedule.saveScheduleData', 'uses' => 'ScheduleController@saveScheduleData']);
        Route::post('/schedule/getSchedules', ['middleware' =>  ['permission:admin-permission'],'as'=> 'schedule.getSchedules', 'uses' => 'ScheduleController@getSchedules']);
        Route::post('/schedule/getAgencyUserById', ['middleware' =>  ['permission:admin-permission'],'uses' => 'ScheduleController@getAgencyUserById']);
        Route::post('/schedule/deleteSlot', ['middleware' =>  ['permission:admin-permission'],'uses' => 'ScheduleController@deleteSlot']);
        Route::post('/schedule/getAgencyLocationsById', ['middleware' =>  ['permission:admin-permission'],'uses' => 'ScheduleController@getAgencyLocationsById']);
        Route::post('/schedule/getUserLocationsById', ['middleware' =>  ['permission:admin-permission'],'uses' => 'ScheduleController@getUserLocationsById']);

        // Setting routes
        Route::get('/setting/index', ['middleware' =>  ['permission:admin-permission'],'uses' => 'SettingController@index']);
        Route::post('/setting/save', ['middleware' =>  ['permission:admin-permission'],'as'=> 'setting.save', 'uses' => 'SettingController@save']);
        Route::post('/setting/getHolidays', ['middleware' =>  ['permission:admin-permission'],'uses' => 'SettingController@getHolidays']);
        Route::post('/setting/addHoliday', ['middleware' =>  ['permission:admin-permission'],'as'=> 'holiday.add', 'uses' => 'SettingController@addHoliday']);
        Route::post('/setting/deleteHoliday/{id}', ['middleware' =>  ['permission:admin-permission'],'as'=> 'holiday.delete', 'uses' => 'SettingController@deleteHoliday']);
        Route::post('/setting/getLocations', ['middleware' =>  ['permission:admin-permission'],'uses' => 'SettingController@getLocations']);
        Route::post('/setting/addLocation', ['middleware' =>  ['permission:admin-permission'],'as'=> 'location.add', 'uses' => 'SettingController@addLocation']);
        Route::post('/setting/deleteLocation/{id}', ['middleware' =>  ['permission:admin-permission'],'as'=> 'location.delete', 'uses' => 'SettingController@deleteLocation']);
        Route::post('/setting/editLocation/{id}', ['middleware' =>  ['permission:admin-permission'],'as'=> 'location.edit', 'uses' => 'SettingController@editLocation']);
        Route::post('/setting/updateLocation', ['middleware' =>  ['permission:admin-permission'],'as'=> 'location.update', 'uses' => 'SettingController@updateLocation']);
    });

    Route::get('admin/home', 'HomeController@index');

    // response routes
    Route::get('admin/response/index', 'ResponseController@index');
    Route::post('admin/response/getResponses', 'ResponseController@getResponses');
    Route::post('admin/response/getQuestionnaireData', 'ResponseController@getQuestionnaireData');

    Route::post('admin/response/getServices', 'ResponseController@getServices');
    Route::post('admin/response/cancelAppointmentAndSchedule', 'ResponseController@cancelAppointmentAndSchedule');
    Route::post('admin/response/rescheduleAppointment', 'ResponseController@rescheduleAppointment');

    Route::post('admin/schedule/getAppointments', 'ScheduleController@getAppointments');


//    // Temp routes for Scheduling in case agency user login
//    Route::get('admin/schedule/index', ['uses' => 'ScheduleController@index']);
//    Route::post('admin/schedule/saveScheduleData', ['as'=> 'schedule.saveScheduleData', 'uses' => 'ScheduleController@saveScheduleData']);
//    Route::post('admin/schedule/getSchedules', ['as'=> 'schedule.getSchedules', 'uses' => 'ScheduleController@getSchedules']);
//    Route::post('admin/schedule/getAgencyUserById', ['uses' => 'ScheduleController@getAgencyUserById']);


    // Change Password routes...
    Route::get('password/change', 'UserController@getChangePassword');
    Route::post('password/change', 'UserController@postChangePassword');
	

});

Route::get('/home', 'PublicController@home');
Route::get('/home1', 'PublicController@home1');
Route::get('/index', 'PublicController@index');
Route::get('/index1', 'PublicController@index1');
Route::get('/aboutus', 'PublicController@about_us');
Route::get('/service', 'PublicController@service');
Route::get('/location', 'PublicController@location');
Route::get('/trauma', 'PublicController@trauma');
Route::get('/traumaindex', 'PublicController@traumaindex');
Route::get('/contactus', 'PublicController@contact_us');
Route::get('/agency/{id}', 'PublicController@agency');
Route::get('/insert','PublicController@insertform');
Route::post('/sendMailFromContactUs', 'PublicController@sendMailFromContactUs');
Route::post('/getQuestionnaireDataByStep', 'PublicController@getQuestionnaireDataByStep');
Route::post('/saveQuestionnaireData', 'PublicController@saveQuestionnaireData');
Route::post('/saveBasicInfo', 'PublicController@saveBasicInfo');
Route::get('/service/index', ['uses' => 'ServiceController@index']);
Route::get('/service/appointment', ['uses' => 'ServiceController@appointment']);
Route::get('/service/print_appointment', ['uses' => 'ServiceController@printAppointment']);
Route::post('/service/goToPrevious', 'ServiceController@goToPrevious');
Route::post('/service/getAvailableSlotsForBooking', ['uses' => 'ServiceController@getAvailableSlotsForBooking']);
Route::post('/service/bookAppointment', ['as'=>'service.book', 'uses' => 'ServiceController@bookAppointment']);
Route::post('/service/cancelAppointment', ['as'=> 'service.cancel','uses' => 'ServiceController@cancelAppointment']);
Route::post('/service/getLocationsWiseAvailableSlots', ['uses' => 'ServiceController@getLocationsWiseAvailableSlots']);
Route::post('/service/getServiceAgencies', ['uses' => 'ServiceController@getServiceAgencies']);
Route::get('/service/appointmentNotAvailable', ['uses' => 'ServiceController@appointmentNotAvailable']);
Route::post('traumaindex', array('uses'=>'TraumaController@create'));
Route::get('/session_expire', 'PublicController@session_expire');
//Route::post('', 'PublicController@create');
//Route::get('/insert','PublicController@insertform');
//Route::post('/create','PublicController@insert'); 
//Route::post('/create', ['middleware' =>  ['permission:admin-permission'], 'uses' => 'PublicController@insert']);
Route::get('/create','PublicController@create');
Route::get('/qcreateview','PublicController@qcreateview');
Route::get('/score','PublicController@score');
Route::get('/haward','PublicController@haward');
Route::get('/greenville','PublicController@greenville');
Route::get('/stanton','PublicController@stanton');
Route::get('/test','PublicController@test');
Route::post('/result','PublicController@result');
Route::post('/insert','PublicController@add');
