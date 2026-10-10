<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommercialDocumentController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PartnerLogoController;
use App\Http\Controllers\PermissionManagementController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ServiceJobController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\TenderRabController;
use App\Http\Controllers\TenderProjectController;
use App\Http\Controllers\UserManagementController;
use App\Models\PartnerLogo;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SignalNiagaJasaTender
|--------------------------------------------------------------------------
*/

// Public Landing / Home Page
Route::get('/', function () {
    $partnerLogos = PartnerLogo::where('is_active', true)->orderBy('sort_order', 'asc')->get();
    return view('welcome', compact('partnerLogos'));
})->name('home');

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider'])->name('oauth.redirect');
Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('oauth.callback');
Route::post('/register', [AuthController::class, 'register'])->middleware('guest')->name('register');
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware(['guest', 'throttle:6,1'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('guest')->name('password.update');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Protected System Routes
Route::middleware(['auth', 'audit'])->group(function () {

    // Dashboard (Semua role bisa akses)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Modul Tender (Submenu 1: Administrasi, Submenu 2: RAB, Submenu 3: Lapangan/Proyek)
    Route::middleware(['role:owner,manager,admin'])->group(function () {
        // 1. Administrasi Tender
        Route::get('/tender', [TenderController::class, 'index'])->name('tender.index');
        Route::get('/tender/administrasi', [TenderController::class, 'index'])->name('tender.administrasi');
        Route::post('/tender', [TenderController::class, 'store'])->name('tender.store');
        Route::put('/tender/{tender}', [TenderController::class, 'update'])->name('tender.update');
        Route::delete('/tender/{tender}', [TenderController::class, 'destroy'])->name('tender.destroy');
        Route::post('/tender/{tender}/request-deletion', [TenderController::class, 'requestDeletion'])->name('tender.request-deletion');
        Route::post('/tender/{tender}/approve-deletion', [TenderController::class, 'approveDeletion'])->name('tender.approve-deletion');
        Route::post('/tender/{tender}/reject-deletion', [TenderController::class, 'rejectDeletion'])->name('tender.reject-deletion');
        Route::post('/tender/{tender}/upload', [TenderController::class, 'uploadDocument'])->name('tender.upload');
        Route::get('/tender/{tender}/documents/{document}', [TenderController::class, 'viewDocument'])->name('tender.documents.view');
        Route::post('/tender/{tender}/submit', [TenderController::class, 'submitForApproval'])->name('tender.submit');
        Route::post('/tender/{tender}/approve', [TenderController::class, 'approve'])->name('tender.approve');
        Route::post('/tender/{tender}/reject', [TenderController::class, 'reject'])->name('tender.reject');
        Route::post('/tender/{tender}/revise', [TenderController::class, 'revise'])->name('tender.revise');
        Route::post('/tender/{tender}/evaluations', [TenderController::class, 'storeEvaluation'])->name('tender.evaluations.store');
        Route::post('/tender/{tender}/convert-contract', [TenderController::class, 'convertToContract'])->name('tender.convert');

        // 2. Estimasi / RAB
        Route::get('/tender/rab', [TenderRabController::class, 'index'])->name('tender.rab.index');
        Route::get('/tender/{tender}/rab', [TenderRabController::class, 'show'])->name('tender.rab.show');
        Route::post('/tender/{tender}/rab/items', [TenderRabController::class, 'storeItem'])->name('tender.rab.items.store');
        Route::put('/tender/{tender}/rab/items/{item}', [TenderRabController::class, 'updateItem'])->name('tender.rab.items.update');
        Route::delete('/tender/{tender}/rab/items/{item}', [TenderRabController::class, 'destroyItem'])->name('tender.rab.items.destroy');
        Route::post('/tender/{tender}/rab/submit', [TenderRabController::class, 'submitRab'])->name('tender.rab.submit');
        Route::post('/tender/{tender}/rab/approve', [TenderRabController::class, 'approveRab'])->name('tender.rab.approve');
        Route::post('/tender/{tender}/rab/reject', [TenderRabController::class, 'rejectRab'])->name('tender.rab.reject');
        Route::post('/tender/{tender}/rab/revise', [TenderRabController::class, 'reviseRab'])->name('tender.rab.revise');

        // 3. Lapangan / Proyek
        Route::get('/tender/proyek', [TenderProjectController::class, 'index'])->name('tender.proyek.index');
        Route::get('/tender/{tender}/proyek', [TenderProjectController::class, 'show'])->name('tender.proyek.show');
        Route::match(['post', 'put'], '/tender/{tender}/proyek/status', [TenderProjectController::class, 'updateStatus'])->name('tender.proyek.status');
        Route::match(['post', 'put'], '/tender/{tender}/proyek/status/update', [TenderProjectController::class, 'updateStatus'])->name('tender.proyek.status.update');
        Route::post('/tender/{tender}/proyek/allocate-material', [TenderProjectController::class, 'allocateMaterial'])->name('tender.proyek.allocate-material');
        Route::post('/tender/{tender}/proyek/material/allocate', [TenderProjectController::class, 'allocateMaterial'])->name('tender.proyek.material.allocate');
        Route::post('/tender/{tender}/proyek/record-material-usage', [TenderProjectController::class, 'recordMaterialUsage'])->name('tender.proyek.record-material-usage');
        Route::post('/tender/{tender}/proyek/material/use', [TenderProjectController::class, 'recordMaterialUsage'])->name('tender.proyek.material.use');
        Route::post('/tender/{tender}/proyek/assignments', [TenderProjectController::class, 'storeAssignment'])->name('tender.proyek.assignments.store');
        Route::match(['post', 'put'], '/tender/{tender}/proyek/assignments/{assignment}', [TenderProjectController::class, 'updateAssignment'])->name('tender.proyek.assignments.update');
        Route::delete('/tender/{tender}/proyek/assignments/{assignment}', [TenderProjectController::class, 'destroyAssignment'])->name('tender.proyek.assignments.destroy');
        Route::post('/tender/{tender}/proyek/logs', [TenderProjectController::class, 'storeLog'])->name('tender.proyek.logs.store');
        Route::delete('/tender/{tender}/proyek/logs/{log}', [TenderProjectController::class, 'destroyLog'])->name('tender.proyek.logs.destroy');
        Route::match(['post', 'put'], '/tender/{tender}/proyek/vendor-progress', [TenderProjectController::class, 'updateVendorProgress'])->name('tender.proyek.vendor-progress');
        Route::match(['post', 'put'], '/tender/{tender}/proyek/vendor/update', [TenderProjectController::class, 'updateVendorProgress'])->name('tender.proyek.vendor.update');
    });

    // Modul Klien & Jasa & Kontrak (Admin input, Manager approve, Owner monitoring)
    Route::middleware(['role:owner,manager,admin'])->group(function () {
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

        Route::get('/jasa', [ServiceJobController::class, 'index'])->name('jasa.index');
        Route::post('/jasa', [ServiceJobController::class, 'store'])->name('jasa.store');
        Route::put('/jasa/{serviceJob}', [ServiceJobController::class, 'update'])->name('jasa.update');
        Route::delete('/jasa/{serviceJob}', [ServiceJobController::class, 'destroy'])->name('jasa.destroy');
        Route::post('/jasa/{serviceJob}/bill', [ServiceJobController::class, 'generateBill'])->name('jasa.bill');
        Route::post('/jasa/{serviceJob}/approve', [ServiceJobController::class, 'approve'])->name('jasa.approve');
        Route::post('/jasa/{serviceJob}/reject', [ServiceJobController::class, 'reject'])->name('jasa.reject');

        Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
        Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
        Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
        Route::delete('/contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');
    });

    // Quotation & Customer Order (Admin input, Manager approve, Owner monitoring)
    Route::middleware(['role:owner,manager,admin'])->group(function () {
        Route::get('/commercial-documents', [CommercialDocumentController::class, 'index'])->name('commercial.index');
        Route::post('/commercial-documents/service-quotation', [CommercialDocumentController::class, 'storeServiceQuotation'])->name('commercial.service.store');
        Route::post('/commercial-documents/service-quotation/{quotation}/convert', [CommercialDocumentController::class, 'convertServiceQuotation'])->name('commercial.service.convert');
        Route::post('/commercial-documents/sales-quotation', [CommercialDocumentController::class, 'storeSalesQuotation'])->name('commercial.sales.store');
        Route::post('/commercial-documents/customer-order', [CommercialDocumentController::class, 'storeOrder'])->name('commercial.order.store');
        Route::post('/commercial-documents/customer-order/{order}/convert', [CommercialDocumentController::class, 'convertOrder'])->name('commercial.order.convert');
    });

    // Modul Supplier & Pengadaan (Admin input, Manager approve, Owner monitoring)
    Route::middleware(['role:owner,manager,admin'])->group(function () {
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        Route::get('/procurements', [ProcurementController::class, 'index'])->name('procurements.index');
        Route::post('/procurements', [ProcurementController::class, 'store'])->name('procurements.store');
        Route::post('/procurements/{procurement}/receive', [ProcurementController::class, 'receive'])->name('procurements.receive');
    });

    // Modul Produk & Warehouse (Admin input, Manager approve, Owner monitoring)
    Route::middleware(['role:owner,manager,admin'])->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::post('/products/{product}/adjust', [ProductController::class, 'adjustStock'])->name('products.adjust');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    // Modul Penjualan / Trade / Barang (Admin input, Manager approve, Owner monitoring)
    Route::middleware(['role:owner,manager,admin'])->group(function () {
        Route::get('/perdagangan', [SalesController::class, 'index'])->name('perdagangan.index');
        Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
        Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
        Route::post('/sales/{sale}/approve', [SalesController::class, 'approve'])->name('sales.approve');
        Route::post('/sales/{sale}/reject', [SalesController::class, 'reject'])->name('sales.reject');
    });

    // Modul Keuangan / Finance (Admin input, Manager approve, Owner monitoring)
    Route::middleware(['role:owner,manager,admin'])->group(function () {
        Route::get('/finance', [InvoiceController::class, 'index'])->name('finance.index');
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');

        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    });

    // Modul Laporan (Admin riwayat sendiri, Manager & Owner laporan komprehensif)
    Route::middleware(['role:owner,manager,admin'])->group(function () {
        Route::get('/laporan', [ReportController::class, 'index'])->middleware('permission:reports.view')->name('laporan.index');
        Route::get('/laporan/export', [ReportController::class, 'export'])->middleware('permission:reports.export')->name('laporan.export');
        Route::get('/laporan/export/pdf', [ReportController::class, 'exportPdf'])->middleware('permission:reports.export')->name('laporan.export.pdf');
        Route::get('/laporan/export/xlsx', [ReportController::class, 'exportXlsx'])->middleware('permission:reports.export')->name('laporan.export.xlsx');
    });

    // Pengaturan Sistem (Owner saja)
    Route::middleware(['role:owner'])->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/data', [ActivityLogController::class, 'data'])->name('activity-logs.data');
        Route::get('/users', [UserManagementController::class, 'index'])->middleware('permission:users.manage')->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->middleware('permission:users.manage')->name('users.store');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->middleware('permission:users.manage')->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->middleware('permission:users.manage')->name('users.destroy');
        Route::get('/permissions', [PermissionManagementController::class, 'index'])->middleware('permission:users.manage')->name('permissions.index');
        Route::put('/permissions/{role}', [PermissionManagementController::class, 'update'])->middleware('permission:users.manage')->name('permissions.update');

        Route::get('/partner-logos', [PartnerLogoController::class, 'index'])->name('partner-logos.index');
        Route::post('/partner-logos', [PartnerLogoController::class, 'store'])->name('partner-logos.store');
        Route::put('/partner-logos/{partnerLogo}', [PartnerLogoController::class, 'update'])->name('partner-logos.update');
        Route::delete('/partner-logos/{partnerLogo}', [PartnerLogoController::class, 'destroy'])->name('partner-logos.destroy');
    });
});
