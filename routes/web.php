<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\SpinController;
use App\Http\Controllers\FrameController;
use App\Http\Controllers\ChitFund\TokenGeneratorController;
use App\Http\Controllers\ChitFund\ChitFundController;
use App\Http\Controllers\ChitFund\ChitFundUserImportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'home')->name('home');

Route::get('/sitemap', function () {
    return response()->file(public_path('sitemap.html'), ['Content-Type' => 'text/html; charset=UTF-8']);
});

/*
| Each module keeps its admin pages under /<module>/admin, behind the shared
| admin login (same accounts for all three). Guests are sent to that
| module's own /<module>/admin/login page.
*/

/* ================== Attendance ================== */
Route::prefix('attendance')->group(function () {

    Route::redirect('/', '/attendance/admin');

    /* Admin: login */
    Route::get('/admin/login', [AdminController::class, 'login_form'])->defaults('module', 'attendance')->name('attendance.login');
    Route::post('/admin/login', [AdminController::class, 'login_validate'])->defaults('module', 'attendance')->name('attendance.login.validate');

    /* Salary receipt for staff: opened from the WhatsApp message, no login, signed link only */
    Route::get('/salary/receipt/{id}/{month}', [SalaryController::class, 'receipt'])
        ->whereNumber('id')->where('month', '\d{4}-\d{2}')
        ->middleware('signed')->name('salary.receipt');

    Route::prefix('admin')->middleware('admin:attendance.login')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('attendance.index');
        Route::get('/logs', [AttendanceController::class, 'allLogs'])->name('attendance.logs');
        Route::get('/logs/{id}', [AttendanceController::class, 'userlog'])->name('userlog');

        Route::get('/export/{id}', [AttendanceController::class, 'exportAttendenceLogs'])->name('attendancelogs.export.get');
        Route::post('/export', [AttendanceController::class, 'exportAttendenceLogs'])->name('attendancelogs.export.post');

        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users/store', [UserController::class, 'store'])->name('users.store');

        Route::get('/users/edit/{id}', [UserController::class, 'edit'])->name('users.edit');
        Route::post('/users/update', [UserController::class, 'update'])->name('users.update');

        Route::post('/users/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');
        Route::get('/users/trash', [UserController::class, 'trashedUsers'])->name('users.trashed');
        Route::post('/users/deleteforever/{id}', [UserController::class, 'deleteforever'])->name('users.deleteforever');
        Route::post('/users/restore/{id}', [UserController::class, 'restore'])->name('users.restore');

        Route::get('/users/shift/create/{id}', [UserController::class, 'createUserShift'])->name('shift.create');
        Route::post('/users/shift/store', [UserController::class, 'storeUserShift'])->name('shift.store');

        /* Salary by the minute, from punch in / punch out */
        Route::get('/salary', [SalaryController::class, 'index'])->name('salary.index');
        Route::post('/salary/working-days', [SalaryController::class, 'saveWorkingDays'])->name('salary.workingDays');
        Route::get('/salary/working-days', [SalaryController::class, 'workingDaysYear'])->name('salary.workingDaysYear');
        Route::post('/salary/working-days/year', [SalaryController::class, 'saveWorkingDaysYear'])->name('salary.workingDaysYear.save');
        Route::get('/salary/{id}', [SalaryController::class, 'show'])->whereNumber('id')->name('salary.show');
        Route::post('/salary/{id}', [SalaryController::class, 'saveSalary'])->whereNumber('id')->name('salary.save');
        Route::post('/salary/{id}/delete/{salaryId}', [SalaryController::class, 'deleteSalary'])->whereNumber('id')->name('salary.delete');

        /* Attendance reports: daily / weekly / monthly / custom, as page, Excel or PDF */
        Route::get('/report', [ReportController::class, 'index'])->name('report.index');
        Route::get('/report/download/{format}', [ReportController::class, 'download'])->whereIn('format', ['excel', 'pdf'])->name('report.download');
        Route::post('/salary/{id}/working-days', [SalaryController::class, 'saveStaffWorkingDays'])->whereNumber('id')->name('salary.staffWorkingDays');

        Route::post('/logout', [AdminController::class, 'logout'])->defaults('module', 'attendance')->name('attendance.logout');
    });
});

/* ================== Chit Fund ================== */
Route::prefix('chitfund')->group(function () {

    Route::redirect('/', '/chitfund/admin');

    /* Admin: login */
    Route::get('/admin/login', [AdminController::class, 'login_form'])->defaults('module', 'chitfund')->name('chitfund.login');
    Route::post('/admin/login', [AdminController::class, 'login_validate'])->defaults('module', 'chitfund')->name('chitfund.login.validate');

    Route::prefix('admin')->middleware('admin:chitfund.login')->group(function () {
        Route::get('/', [ChitFundController::class, 'chitfundIndex'])->name('chitfundIndex');
        Route::get('/generateToken', [TokenGeneratorController::class, 'tokenGenerate'])->name('tokenGenerate');
        Route::post('/createplan', [ChitFundController::class, 'chitfundCreatePlan'])->name('chitfund.createPlan');
        Route::get('/plan/{id}', [ChitFundController::class, 'chitfundShowPlan'])->name('chitfund.showPlan');
        Route::post('/createuser', [ChitFundController::class, 'chitfundCreateUser'])->name('chitfund.createUser');
        Route::post('/edituser', [ChitFundController::class, 'chitfundEditUser'])->name('chitfund.editUser');
        Route::get('/adddue/{planid}/{id}', [ChitFundController::class, 'chitfundAddDue'])->name('chitfund.addDue');
        Route::get('/user-details/{id}', [ChitFundController::class, 'chitfundUserDetails'])->name('chitfund.userDetails');
        Route::post('/update/duestatus', [ChitFundController::class, 'updateDueStatus'])->name('chitfund.updateDueStatus');
        Route::get('/print-invoice/{id}/{date}', [ChitFundController::class, 'printInvoice'])->name('chitfund.printInvoice');

        //Bulk Upload
        Route::post('/bulkUpload', [ChitFundUserImportController::class, 'bulkUpload'])->name('chitfund.bulkUpload');

        /* Chit Fund Users - Import from Excel*/
        Route::get('/import-user', [ChitFundUserImportController::class, 'chitfundImportIndex'])->name('chitfund.showimport');
        Route::post('/save-import-user', [ChitFundUserImportController::class, 'chitfundImportExcel'])->name('chitfund.importExcel');

        Route::post('/logout', [AdminController::class, 'logout'])->defaults('module', 'chitfund')->name('chitfund.logout');
    });
});

/* ================== Spin Wheel ================== */
Route::prefix('spin')->group(function () {

    /* Customer flow: form -> wheel -> thank you */
    Route::get('/', [SpinController::class, 'showForm'])->name('showForm');
    Route::post('/', [SpinController::class, 'saveInvoiceForm'])->name('saveInvoiceForm');
    Route::get('/wheel/{invoice_number}', [SpinController::class, 'showSpinWheel'])->name('showSpinWheel');
    Route::get('/wheel/{invoice_number}/{discount}', [SpinController::class, 'saveSpinWheel'])->name('saveSpinWheel');

    Route::group(['middleware' => 'prevent-back-button'], function () {
        Route::get('/thankyou/{invoice_number}', [SpinController::class, 'thankYou'])->name('thankYou');
    });

    /* Admin: login */
    Route::get('/admin/login', [AdminController::class, 'login_form'])->defaults('module', 'spin')->name('spin.login');
    Route::post('/admin/login', [AdminController::class, 'login_validate'])->defaults('module', 'spin')->name('spin.login.validate');

    /* Admin: spin results and invoices */
    Route::prefix('admin')->middleware('admin:spin.login')->group(function () {
        Route::get('/', [SpinController::class, 'index'])->name('spin.admin');
        Route::get('/redeem/{id}', [SpinController::class, 'updateInvoiceForm'])->name('updateInvoiceForm');

        Route::get('/invoices', [SpinController::class, 'spinInvoices'])->name('spinInvoices');
        Route::get('/invoices/import', [SpinController::class, 'ImportInvoice'])->name('importInvoice');
        Route::post('/invoices/import', [SpinController::class, 'spinImportInvoiceExcel'])->name('importInvoiceExcel');

        Route::post('/logout', [AdminController::class, 'logout'])->defaults('module', 'spin')->name('spin.logout');
    });
});

/* ================== Old URLs -> new URLs ================== */
Route::permanentRedirect('/spin-form', '/spin');
Route::permanentRedirect('/admin/users', '/attendance/admin');
Route::permanentRedirect('/admin/users/logs', '/attendance/admin/logs');
Route::permanentRedirect('/admin/users/spins', '/spin/admin');
Route::permanentRedirect('/admin/chitfund', '/chitfund/admin');
Route::permanentRedirect('/admin/chitfund/login', '/chitfund/admin/login');
Route::permanentRedirect('/spin-invoice/invoices', '/spin/admin/invoices');

/* Happy Customer - Frame Routes */
Route::get('/frame', [FrameController::class, 'frameIndex'])->name('frameIndex');
Route::post('/frame/saveframe', [FrameController::class, 'saveFrame'])->name('saveFrame');
Route::post('/frame/downloadframe', [FrameController::class, 'downloadFrame'])->name('downloadFrame');

/* ================== Clear Cache Routes ================== */
Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function() {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

//Route cache:
Route::get('/route-cache', function() {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

//Clear Route cache:
Route::get('/route-clear', function() {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

//Clear View cache:
Route::get('/view-clear', function() {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

//Clear Config cache:
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Clear Config cleared</h1>';
});

//Clear All
Route::get('/clear', function() {
   Artisan::call('cache:clear');
   Artisan::call('config:clear');
   Artisan::call('config:cache');
   Artisan::call('view:clear');
   Artisan::call('route:clear');
   return "All Cleared!";
});

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return "Cache is cleared";
});
