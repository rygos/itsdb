<?php

use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ChangelogController;
use App\Http\Controllers\ComposeController;
use App\Http\Controllers\ContainerController;
use App\Http\Controllers\CredentialsController;
use App\Http\Controllers\CustomerDocumentController;
use App\Http\Controllers\CustomerProjectController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\EnvController;
use App\Http\Controllers\HoursController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\ProductMatrixController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\RemarksController;
use App\Http\Controllers\ServerController;
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
Route::middleware('auth')->group(function () {
    Route::get('/', [IndexController::class, 'index'])->name('index');
    Route::get('/test', [TestController::class, 'test'])->name('test.test');

    // Controller groups keep route definitions compact and avoid legacy string syntax,
    // which makes framework upgrades less fragile.
    Route::controller(ComposeController::class)->group(function () {
        Route::get('/compose', 'index')->middleware('area:compose,visible')->name('compose.index');
        Route::post('/compose/upload', 'upload')->middleware('area:compose,editable')->name('compose.upload');
        Route::get('/compose/file/{filename}', 'show')->middleware('area:compose,visible')->name('compose.show');
        Route::post('/compose/file/{filename}', 'store')->middleware('area:compose,editable')->name('compose.store');
        Route::get('/compose/generate/server/{server_id}', 'generate')->middleware('area:compose,editable')->name('compose.generate');
    });

    Route::controller(ContainerController::class)->group(function () {
        Route::get('/container/{title}', 'show')->middleware('area:compose,visible')->name('container.show');
        Route::post('/container/{title}', 'store')->middleware('area:compose,editable')->name('container.store');
    });

    Route::controller(CustomersController::class)->group(function () {
        // Route-model binding keeps controllers focused on business logic instead of lookup boilerplate.
        Route::get('/customers', 'index')->middleware('area:customers,visible')->name('customers.index');
        Route::get('/customers/add', 'add')->middleware('area:customers,editable')->name('customers.add');
        Route::post('/customers/store', 'store')->middleware('area:customers,editable')->name('customers.store');
        Route::get('/customers/city/{city}', 'city')->middleware('area:customers,visible')->name('customers.city');
        Route::get('/customers/{customer}/edit', 'edit')->middleware('area:customers,editable')->name('customers.edit');
        Route::post('/customers/{customer}', 'update')->middleware('area:customers,editable')->name('customers.update');
        Route::get('/customers/{customer}', 'view')->middleware('area:customers,visible')->name('customers.view');
        Route::post('/contacts/create', 'contact_create')->middleware('area:customers,editable')->name('contact.create');
        Route::post('/contacts/update', 'contact_update')->middleware('area:customers,editable')->name('contact.update');
        Route::get('/contacts/delete/{id}', 'contact_delete')->middleware('area:customers,editable')->name('contact.delete');
        Route::post('/city/add', 'store_city')->middleware('area:customers,editable')->name('city.add');
    });

    Route::post('/customer-documents/store', [CustomerDocumentController::class, 'store'])->middleware('area:customers,editable')->name('customer_documents.store');
    Route::get('/customer-documents/{id}/download', [CustomerDocumentController::class, 'download'])->middleware('area:customers,visible')->name('customer_documents.download');
    Route::get('/customer-documents/{id}/preview', [CustomerDocumentController::class, 'preview'])->middleware('area:customers,visible')->name('customer_documents.preview');
    Route::get('/customer-documents/{id}/delete', [CustomerDocumentController::class, 'delete'])->middleware('area:customers,editable')->name('customer_documents.delete');
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

    Route::controller(ProjectsController::class)->group(function () {
        // Projects use route-model binding on the detail page, but keep update/status endpoints
        // payload-based for compatibility with the existing forms.
        Route::get('/projects/add', 'add')->middleware('area:projects,editable')->name('projects.add');
        Route::post('/projects/store', 'store')->middleware('area:projects,editable')->name('projects.store');
        Route::get('/projects/{project}', 'view')->middleware('area:projects,visible')->name('projects.view');
        Route::post('/projects/status_change', 'change_status')->middleware('area:projects,editable')->name('projects.change_status');
        Route::post('/projects/update', 'update')->middleware('area:projects,editable')->name('projects.update');
    });

    Route::controller(ServerController::class)->group(function () {
        Route::post('/servers/store', 'store')->name('servers.store');
        Route::get('/servers/{id}', 'view')->name('servers.view');
        Route::post('/servers/{id}/add_composer', 'add_composer')->name('servers.add_composer');
        Route::get('/servers/{server_id}/del_composer/{compose_id}', 'del_composer')->name('servers.del_composer');
        Route::post('/servers/update', 'update')->name('servers.update');
        Route::post('/servers/update_serverconfig', 'update_serverconfig')->name('servers.update_serverconfig');
    });

    Route::post('/remarks/store', [RemarksController::class, 'store'])->name('remarks.store');

    Route::controller(CredentialsController::class)->group(function () {
        Route::post('/credentials/store', 'store')->name('credentials.store');
        Route::post('/credentials/update', 'update')->name('credentials.update');
        Route::get('/credentials/{id}/delete', 'delete')->name('credentials.delete');
    });

    Route::controller(CertificateController::class)->group(function () {
        Route::post('/certificate/update', 'update')->name('certificate.update');
        Route::post('/certificate/import-pfx', 'import_pfx')->name('certificate.import_pfx');
    });

    Route::get('/calendar/{year?}/{month?}', [CalendarController::class, 'index'])->middleware('area:calendar,visible')->name('calendar.index');
    Route::get('/logs', [LogsController::class, 'index'])->name('logs.index');

    Route::get('/env/generate/server/{server_id}', [EnvController::class, 'generate'])->name('env.generate');
    Route::post('/env/generate/server/{server_id}', [EnvController::class, 'update'])->name('env.update');
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
    Route::post('/administration/server-kinds', [AdministrationController::class, 'storeServerKind'])->middleware('area:administration,editable')->name('administration.server_kinds.store');
    Route::post('/administration/server-kinds/{serverKind}', [AdministrationController::class, 'updateServerKind'])->middleware('area:administration,editable')->name('administration.server_kinds.update');
    Route::post('/administration/operating-systems', [AdministrationController::class, 'storeOperatingSystem'])->middleware('area:administration,editable')->name('administration.operating_systems.store');
    Route::post('/administration/operating-systems/{operatingSystem}', [AdministrationController::class, 'updateOperatingSystem'])->middleware('area:administration,editable')->name('administration.operating_systems.update');
    Route::post('/administration/settings', [AdministrationController::class, 'updateSettings'])->middleware('area:administration,administration')->name('administration.settings.update');
    Route::post('/administration/imports/customers', [AdministrationController::class, 'importCustomers'])->middleware('area:administration,editable')->name('administration.imports.customers');
    Route::post('/administration/imports/orbisu-servers', [AdministrationController::class, 'importOrbisUServers'])->middleware('area:administration,editable')->name('administration.imports.orbisu_servers');
    Route::post('/administration/imports/oas-servers', [AdministrationController::class, 'importOasServers'])->middleware('area:administration,editable')->name('administration.imports.oas_servers');
    Route::post('/administration/cities', [AdministrationController::class, 'storeCity'])->middleware('area:administration,editable')->name('administration.cities.store');
    Route::post('/administration/cities/{city}', [AdministrationController::class, 'updateCity'])->middleware('area:administration,editable')->name('administration.cities.update');
    Route::post('/administration/customers/{customer}/city', [AdministrationController::class, 'updateCustomerCity'])->middleware('area:administration,editable')->name('administration.customers.city.update');
});

require __DIR__.'/auth.php';
