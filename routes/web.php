<?php

use App\Http\Controllers\ChangelogController;
use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\CustomerProjectController;
use App\Http\Controllers\CustomerDocumentController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\EnvController;
use App\Http\Controllers\HoursController;
use App\Http\Controllers\ProductMatrixController;
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
    Route::get('/compose', 'App\Http\Controllers\ComposeController@index')->middleware('area:compose,visible')->name('compose.index');
    Route::post('/compose/upload', 'App\Http\Controllers\ComposeController@upload')->middleware('area:compose,editable')->name('compose.upload');
    Route::get('/compose/file/{filename}', 'App\Http\Controllers\ComposeController@show')->middleware('area:compose,visible')->name('compose.show');
    Route::post('/compose/file/{filename}', 'App\Http\Controllers\ComposeController@store')->middleware('area:compose,editable')->name('compose.store');
    Route::get('/compose/generate/server/{server_id}', 'App\Http\Controllers\ComposeController@generate')->middleware('area:compose,editable')->name('compose.generate');

    //Container Route
    Route::get('/container/{title}', 'App\Http\Controllers\ContainerController@show')->middleware('area:compose,visible')->name('container.show');
    Route::post('/container/{title}', 'App\Http\Controllers\ContainerController@store')->middleware('area:compose,editable')->name('container.store');

    //Routes for Customers
    Route::get('/customers', [CustomersController::class,'index'])->middleware('area:customers,visible')->name('customers.index');
    Route::get('/customers/add', 'App\Http\Controllers\CustomersController@add')->middleware('area:customers,editable')->name('customers.add');
    Route::post('/customers/store', 'App\Http\Controllers\CustomersController@store')->middleware('area:customers,editable')->name('customers.store');
    Route::get('/customers/city/{id}', [CustomersController::class,'city'])->middleware('area:customers,visible')->name('customers.city');
    Route::get('/customers/{id}', '\App\Http\Controllers\CustomersController@view')->middleware('area:customers,visible')->name('customers.view');
    Route::post('/customer-documents/store', [CustomerDocumentController::class, 'store'])->middleware('area:customers,editable')->name('customer_documents.store');
    Route::get('/customer-documents/{id}/download', [CustomerDocumentController::class, 'download'])->middleware('area:customers,visible')->name('customer_documents.download');
    Route::get('/customer-documents/{id}/delete', [CustomerDocumentController::class, 'delete'])->middleware('area:customers,editable')->name('customer_documents.delete');
    Route::post('/contacts/create', 'App\Http\Controllers\CustomersController@contact_create')->middleware('area:customers,editable')->name('contact.create');
    Route::post('/contacts/update', 'App\Http\Controllers\CustomersController@contact_update')->middleware('area:customers,editable')->name('contact.update');
    Route::get('/contacts/delete/{id}', 'App\Http\Controllers\CustomersController@contact_delete')->middleware('area:customers,editable')->name('contact.delete');
    Route::post('/city/add', [CustomersController::class, 'store_city'])->middleware('area:customers,editable')->name('city.add');
    Route::get('/customers-projects/add', [CustomerProjectController::class, 'add'])->middleware('area:projects,editable')->name('customers_projects.add');
    Route::get('/customers-projects/lookup-customer', [CustomerProjectController::class, 'lookup_customer'])->middleware('area:projects,editable')->name('customers_projects.lookup_customer');
    Route::get('/customers-projects/lookup-city', [CustomerProjectController::class, 'lookup_city'])->middleware('area:projects,editable')->name('customers_projects.lookup_city');
    Route::post('/customers-projects/store', [CustomerProjectController::class, 'store'])->middleware('area:projects,editable')->name('customers_projects.store');

    //Routes for Hours
    Route::get('/hours', [HoursController::class, 'index'])->middleware('area:hours,visible')->name('hours.index');

    //Routes for Product Matrix
    Route::get('/product-matrix', [ProductMatrixController::class, 'index'])->middleware('area:product_matrix,visible')->name('product_matrix.index');
    Route::post('/product-matrix/import', [ProductMatrixController::class, 'import'])->middleware('area:product_matrix,editable')->name('product_matrix.import');
    Route::post('/product-matrix/aliases', [ProductMatrixController::class, 'storeAlias'])->middleware('area:product_matrix,editable')->name('product_matrix.aliases.store');
    Route::post('/product-matrix/aliases/{id}', [ProductMatrixController::class, 'updateAlias'])->middleware('area:product_matrix,editable')->name('product_matrix.aliases.update');
    Route::get('/product-matrix/aliases/{id}/delete', [ProductMatrixController::class, 'deleteAlias'])->middleware('area:product_matrix,editable')->name('product_matrix.aliases.delete');

    //Routes for Projects
    Route::get('/projects/add', 'App\Http\Controllers\ProjectsController@add')->middleware('area:projects,editable')->name('projects.add');
    Route::post('/projects/store', 'App\Http\Controllers\ProjectsController@store')->middleware('area:projects,editable')->name('projects.store');
    Route::get('/projects/{id}', 'App\Http\Controllers\ProjectsController@view')->middleware('area:projects,visible')->name('projects.view');
    Route::post('/projects/status_change', 'App\Http\Controllers\ProjectsController@change_status')->middleware('area:projects,editable')->name('projects.change_status');
    Route::post('/projects/update', [ProjectsController::class, 'update'])->middleware('area:projects,editable')->name('projects.update');

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
    Route::post('/credentials/update', 'App\Http\Controllers\CredentialsController@update')->name('credentials.update');
    Route::get('/credentials/{id}/delete', 'App\Http\Controllers\CredentialsController@delete')->name('credentials.delete');

    //Certificate Routes
    Route::post('/certificate/update', 'App\Http\Controllers\CertificateController@update')->name('certificate.update');
    Route::post('/certificate/import-pfx', 'App\Http\Controllers\CertificateController@import_pfx')->name('certificate.import_pfx');

    //Calendar Routes
    Route::get('/calendar/{year?}/{month?}', 'App\Http\Controllers\CalendarController@index')->middleware('area:calendar,visible')->name('calendar.index');

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

    //Administration Routes
    Route::get('/administration', [AdministrationController::class, 'index'])->middleware('area:administration,visible')->name('administration.index');
    Route::get('/administration/users/{user}/edit', [AdministrationController::class, 'editUser'])->middleware('area:administration,administration')->name('administration.users.edit');
    Route::post('/administration/users/{user}', [AdministrationController::class, 'updateUser'])->middleware('area:administration,administration')->name('administration.users.update');
    Route::post('/administration/statuses', [AdministrationController::class, 'storeStatus'])->middleware('area:administration,editable')->name('administration.statuses.store');
    Route::post('/administration/statuses/{status}', [AdministrationController::class, 'updateStatus'])->middleware('area:administration,editable')->name('administration.statuses.update');
    Route::post('/administration/settings', [AdministrationController::class, 'updateSettings'])->middleware('area:administration,administration')->name('administration.settings.update');
    Route::post('/administration/imports/customers', [AdministrationController::class, 'importCustomers'])->middleware('area:administration,editable')->name('administration.imports.customers');
    Route::post('/administration/imports/orbisu-servers', [AdministrationController::class, 'importOrbisUServers'])->middleware('area:administration,editable')->name('administration.imports.orbisu_servers');
    Route::post('/administration/cities/{city}', [AdministrationController::class, 'updateCity'])->middleware('area:administration,editable')->name('administration.cities.update');
});

require __DIR__.'/auth.php';
