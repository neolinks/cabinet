<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'PersonalController@index');

Route::get('home', 'HomeController@index');

Route::get('/drivers','DriversController@update');
Route::get('/drivers/im','DriversController@im');
Route::get('/drivers/violation','DriversController@violation');
Route::get('/drivers/overmilleage','DriversController@overmilleage');
Route::get('/drivers/overshift','DriversController@overshift');
Route::get('/drivers/shashka','DriversController@shashka');
Route::get('/drivers/obman_gps','DriversController@obman_gps');
Route::get('/drivers/negative_balance','DriversController@negativeBalance');
Route::get('/crews/violation','CrewsController@violation');
Route::get('/crews','CrewsController@update');
Route::get('/crews/crews_inline','CrewsController@crews_inline');
Route::get('/crews/smens','CrewsController@updateSmens');
Route::get('/crews/crews_info','CrewsController@crews_info');
Route::get('/cars','CarsController@update');
Route::get('/cars/cars_info','CarsController@cars_info');
Route::get('/cars/wialon_cars','CrewsController@update_wia_cars');
Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
Route::get('/admin','AdministrationController@createRole');
Route::get('/personal/','PersonalController@index');
Route::get('/personal/engine','PersonalController@engine');
Route::get('/personal/engine_action','PersonalController@engineAction');
Route::get('/personal/exception','PersonalController@exception');
Route::post('/personal/exception','PersonalController@postException');
Route::get('/personal/overmilleage','PersonalController@overmilleage');
Route::get('/personal/overshift','PersonalController@overshift');
Route::get('/personal/orders','PersonalController@ordersFromSite');
Route::get('/personal/carlist','PersonalController@carList');
Route::get('/personal/last_connect','PersonalController@lastConnect');
Route::get('/personal/user/list','UsersController@userlists');
Route::get('/personal/user/edit/{id}','UsersController@userEdit');
Route::post('/personal/user_edit','UsersController@postUserEdit');
Route::get('/personal/user/create','UsersController@createUser');
Route::post('/personal/user/create','UsersController@postCreateUser');
Route::get('/personal/user/delete/{id}','UsersController@deleteUser');
Route::get('/personal/airport',['uses' =>'AirportController@orders']);
Route::post('/personal/airport/note_edit',['uses' =>'AirportController@noteEdit']);
Route::post('/personal/airport/time_edit',['uses' =>'AirportController@timeEdit']);
Route::get('/personal/test/','PersonalController@aircraft');
Route::get('/personal/shifts_to_credit/','PersonalController@shiftsToCredit');
Route::post('/personal/shifts_to_credit/','PersonalController@postShiftsToCredit');
Route::get('/personal/new_debtor_id/','PersonalController@newDebtorID');
Route::get('/personal/compensation/','PersonalController@compensation');
Route::post('/personal/compensation/','PersonalController@postCompensation');
Route::post('/personal/add_for_waiting/','PersonalController@addForWaiting');
Route::get('/personal/has_second_id/','PersonalController@hasSecondID');
Route::get('/personal/get_shifts/','PersonalController@getShifts');
Route::get('/personal/configs','PersonalController@configs');
Route::get('/personal/configActions','PersonalController@configActions');
Entrust::routeNeedsRole('personal/user/*', 'admin');