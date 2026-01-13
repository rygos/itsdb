<?php

use App\Http\Controllers\ChangelogController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\EnvController;
use App\Http\Controllers\HoursController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\TestController;
use Illuminate\Support\Facades\Route;

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
Route::group(['middleware' => ['auth']], function(){
    Route::get('/', 'App\Http\Controllers\IndexController@index')->name('index');
    Route::get('/test', [TestController::class, 'test'])->name('test.test');

    //Compose Routes
    Route::get('/compose', 'App\Http\Controllers\ComposeController@index')->name('compose.index');
    Route::get('/compose/update', 'App\Http\Controllers\ComposeController@update')->name('compose.update');
    Route::get('/compose/file/{filename}', 'App\Http\Controllers\ComposeController@show')->name('compose.show');
    Route::post('/compose/file/{filename}', 'App\Http\Controllers\ComposeController@store')->name('compose.store');
    Route::get('/compose/generate/server/{server_id}', 'App\Http\Controllers\ComposeController@generate')->name('compose.generate');

    //Container Route
    Route::get('/container/{title}', 'App\Http\Controllers\ContainerController@show')->name('container.show');
    Route::post('/container/{title}', 'App\Http\Controllers\ContainerController@store')->name('container.store');

    //Routes for Customers
    Route::get('/customers', [CustomersController::class,'index'])->name('customers.index');
    Route::get('/customers/add', 'App\Http\Controllers\CustomersController@add')->name('customers.add');
    Route::post('/customers/store', 'App\Http\Controllers\CustomersController@store')->name('customers.store');
    Route::get('/customers/{id}', '\App\Http\Controllers\CustomersController@view')->name('customers.view');
    Route::post('/contacts/create', 'App\Http\Controllers\CustomersController@contact_create')->name('contact.create');
    Route::post('/contacts/update', 'App\Http\Controllers\CustomersController@contact_update')->name('contact.update');
    Route::get('/contacts/delete/{id}', 'App\Http\Controllers\CustomersController@contact_delete')->name('contact.delete');
    Route::post('/city/add', [CustomersController::class, 'store_city'])->name('city.add');

    //Routes for Hours
    Route::get('/hours', [HoursController::class, 'index'])->name('hours.index');

    //Routes for Projects
    Route::get('/projects/add', 'App\Http\Controllers\ProjectsController@add')->name('projects.add');
    Route::post('/projects/store', 'App\Http\Controllers\ProjectsController@store')->name('projects.store');
    Route::get('/projects/{id}', 'App\Http\Controllers\ProjectsController@view')->name('projects.view');
    Route::post('/projects/status_change', 'App\Http\Controllers\ProjectsController@change_status')->name('projects.change_status');
    Route::post('/projects/update', [ProjectsController::class, 'update'])->name('projects.update');

    //Server Routes
    Route::post('/servers/store', 'App\Http\Controllers\ServerController@store')->name('servers.store');
    Route::get('/servers/{id}', 'App\Http\Controllers\ServerController@view')->name('servers.view');
    Route::post('/servers/{id}/add_composer', 'App\Http\Controllers\ServerController@add_composer')->name('servers.add_composer');
    Route::get('/servers/{server_id}/del_composer/{compose_id}', 'App\Http\Controllers\ServerController@del_composer')->name('servers.del_composer');
    Route::post('/servers/update', 'App\Http\Controllers\ServerController@update')->name('servers.update');
    Route::post('/servers/update_serverconfig', 'App\Http\Controllers\ServerController@update_serverconfig')->name('servers.update_serverconfig');

    //Remark Routes
    Route::post('/remarks/store', 'App\Http\Controllers\RemarksController@store')->name('remarks.store');

    //Credentials Route
    Route::post('/credentials/store', 'App\Http\Controllers\CredentialsController@store')->name('credentials.store');
    Route::get('/credentials/{id}/delete', 'App\Http\Controllers\CredentialsController@delete')->name('credentials.delete');

    //Certificate Routes
    Route::post('/certificate/update', 'App\Http\Controllers\CertificateController@update')->name('certificate.update');

    //Calendar Routes
    Route::get('/calendar/{year?}/{month?}', 'App\Http\Controllers\CalendarController@index')->name('calendar.index');

    //ENV Routes
    Route::get('/env/generate/server/{server_id}', 'App\Http\Controllers\EnvController@generate')->name('env.generate');
    Route::post('/env/generate/server/{server_id}', 'App\Http\Controllers\EnvController@update')->name('env.update');
    Route::get('/env/generate_from_raw/server/{server_id}', [EnvController::class, 'generate_from_raw'])->name('env.generate_from_raw');
    Route::get('/env/generate_raw/server/{server_id}', [EnvController::class, 'generate_raw'])->name('env.generate_raw');

    //Changelog Routes
    Route::get('/changelog', [ChangelogController::class, 'index'])->name('changelog.index');
    Route::post('/changelog/add_changelog', [ChangelogController::class, 'add_changelog'])->name('changelog.add_changelog');
    Route::post('/changelog/add_version', [ChangelogController::class, 'add_version'])->name('changelog.add_version');
    Route::post('/changelog/publish', [ChangelogController::class, 'publish_version'])->name('changelog.publish_version');
});

require __DIR__.'/auth.php';
