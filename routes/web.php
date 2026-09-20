<?php

use App\Http\Controllers\AuthController;
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
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->middleware(['guest', 'throttle:6,1'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->middleware('guest')->name('password.update');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Protected System Routes
Route::middleware(['auth', 'audit'])->group(function () {

    // Dashboard (Super Admin & Management & fallback for logged-in users)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Modul Tender (Super Admin & Tender Officer)
    Route::middleware(['role:super_admin,tender_officer'])->group(function () {
        Route::get('/tender', [TenderController::class, 'index'])->name('tender.index');
        Route::post('/tender', [TenderController::class, 'store'])->name('tender.store');
        Route::put('/tender/{tender}', [TenderController::class, 'update'])->name('tender.update');
        Route::delete('/tender/{tender}', [TenderController::class, 'destroy'])->name('tender.destroy');
        Route::post('/tender/{tender}/upload', [TenderController::class, 'uploadDocument'])->name('tender.upload');
        Route::post('/tender/{tender}/convert-contract', [TenderController::class, 'convertToContract'])->name('tender.convert');
    });

    // Modul Klien & Jasa & Kontrak (Super Admin & Service Officer)
    Route::middleware(['role:super_admin,service_officer'])->group(function () {
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');

        Route::get('/jasa', [ServiceJobController::class, 'index'])->name('jasa.index');
        Route::post('/jasa', [ServiceJobController::class, 'store'])->name('jasa.store');
        Route::put('/jasa/{serviceJob}', [ServiceJobController::class, 'update'])->name('jasa.update');
        Route::post('/jasa/{serviceJob}/bill', [ServiceJobController::class, 'generateBill'])->name('jasa.bill');

        Route::get('/contracts', [ContractController::class, 'index'])->name('contracts.index');
        Route::post('/contracts', [ContractController::class, 'store'])->name('contracts.store');
        Route::put('/contracts/{contract}', [ContractController::class, 'update'])->name('contracts.update');
        Route::delete('/contracts/{contract}', [ContractController::class, 'destroy'])->name('contracts.destroy');
    });

    // Quotation and Customer Order (Service Officer & Sales)
    Route::middleware(['role:super_admin,service_officer,sales'])->group(function () {
        Route::get('/commercial-documents', [CommercialDocumentController::class, 'index'])->name('commercial.index');
        Route::post('/commercial-documents/service-quotation', [CommercialDocumentController::class, 'storeServiceQuotation'])->name('commercial.service.store');
        Route::post('/commercial-documents/service-quotation/{quotation}/convert', [CommercialDocumentController::class, 'convertServiceQuotation'])->name('commercial.service.convert');
        Route::post('/commercial-documents/sales-quotation', [CommercialDocumentController::class, 'storeSalesQuotation'])->name('commercial.sales.store');
        Route::post('/commercial-documents/customer-order', [CommercialDocumentController::class, 'storeOrder'])->name('commercial.order.store');
        Route::post('/commercial-documents/customer-order/{order}/convert', [CommercialDocumentController::class, 'convertOrder'])->name('commercial.order.convert');
    });

    // Modul Supplier & Pengadaan (Super Admin & Purchasing)
    Route::middleware(['role:super_admin,purchasing'])->group(function () {
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        Route::get('/procurements', [ProcurementController::class, 'index'])->name('procurements.index');
        Route::post('/procurements', [ProcurementController::class, 'store'])->name('procurements.store');
        Route::post('/procurements/{procurement}/receive', [ProcurementController::class, 'receive'])->name('procurements.receive');
    });

    // Modul Produk & Warehouse (Super Admin & Warehouse)
    Route::middleware(['role:super_admin,warehouse'])->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::post('/products/{product}/adjust', [ProductController::class, 'adjustStock'])->name('products.adjust');
    });

    // Modul Penjualan / Trade (Super Admin & Sales)
    Route::middleware(['role:super_admin,sales'])->group(function () {
        Route::get('/perdagangan', [SalesController::class, 'index'])->name('perdagangan.index');
        Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
        Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
    });

    // Modul Keuangan / Finance (Super Admin & Finance)
    Route::middleware(['role:super_admin,finance'])->group(function () {
        Route::get('/finance', [InvoiceController::class, 'index'])->name('finance.index');
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');

        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    });

    // Modul Laporan (Super Admin & Management)
    Route::middleware(['role:super_admin,management'])->group(function () {
        Route::get('/laporan', [ReportController::class, 'index'])->middleware('permission:reports.view')->name('laporan.index');
        Route::get('/laporan/export', [ReportController::class, 'export'])->middleware('permission:reports.export')->name('laporan.export');
        Route::get('/laporan/export/pdf', [ReportController::class, 'exportPdf'])->middleware('permission:reports.export')->name('laporan.export.pdf');
        Route::get('/laporan/export/xlsx', [ReportController::class, 'exportXlsx'])->middleware('permission:reports.export')->name('laporan.export.xlsx');
    });

    // Modul Khusus Super Admin (Kelola Logo Klien Landing Page)
    Route::middleware(['role:super_admin'])->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->middleware('permission:users.manage')->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->middleware('permission:users.manage')->name('users.store');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->middleware('permission:users.manage')->name('users.update');
        Route::get('/permissions', [PermissionManagementController::class, 'index'])->middleware('permission:users.manage')->name('permissions.index');
        Route::put('/permissions/{role}', [PermissionManagementController::class, 'update'])->middleware('permission:users.manage')->name('permissions.update');

        Route::get('/partner-logos', [PartnerLogoController::class, 'index'])->name('partner-logos.index');
        Route::post('/partner-logos', [PartnerLogoController::class, 'store'])->name('partner-logos.store');
        Route::put('/partner-logos/{partnerLogo}', [PartnerLogoController::class, 'update'])->name('partner-logos.update');
        Route::delete('/partner-logos/{partnerLogo}', [PartnerLogoController::class, 'destroy'])->name('partner-logos.destroy');
    });
});
