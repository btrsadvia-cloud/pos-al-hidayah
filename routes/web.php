<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ----- Unauthenticated Routes -----
Route::get('/', fn () => redirect()->route('login'));

// BuatQris Webhook Callback (Public endpoint with HMAC-SHA256 signature verification)
Route::post('/api/buatqris/webhook', [\App\Http\Controllers\BuatQrisWebhookController::class, 'handle'])->name('buatqris.webhook');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ----- Authenticated Routes -----
Route::middleware(['auth', 'active'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Shift
    Route::get('/shift', [ShiftController::class, 'index'])->name('shift.index');
    Route::post('/shift/open', [ShiftController::class, 'open'])->name('shift.open');
    Route::post('/shift/close', [ShiftController::class, 'close'])->name('shift.close');

    // POS (requires open shift for cashiers)
    Route::get('/pos', [\App\Http\Controllers\PosController::class, 'index'])->name('pos.index');
    Route::post('/pos/checkout', [\App\Http\Controllers\PosController::class, 'checkout'])->name('pos.checkout');

    // Sales
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
    Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel')->middleware('can:cancel-sale');

    // Inventory
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/stock-in', [InventoryController::class, 'stockInForm'])->name('inventory.stock-in');
    Route::post('/inventory/stock-in', [InventoryController::class, 'storeStockIn'])->name('inventory.stock-in.store');
    Route::get('/inventory/stock-out', [InventoryController::class, 'stockOutForm'])->name('inventory.stock-out');
    Route::post('/inventory/stock-out', [InventoryController::class, 'storeStockOut'])->name('inventory.stock-out.store');
    Route::get('/inventory/card/{product}', [InventoryController::class, 'stockCard'])->name('inventory.card');

    // Customers
    Route::resource('customers', CustomerController::class)->except(['destroy']);
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Admin-only routes
    Route::middleware('can:admin')->group(function () {

        // Master data (Laman kategori dihapus sesuai permintaan, dialihkan ke katalog produk)
        Route::redirect('/categories', '/products');
        Route::resource('units', UnitController::class)->except(['show']);
        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/image', [ProductController::class, 'updateImage'])->name('products.image.update');
        Route::get('/products/{product}/prices', [ProductController::class, 'editPrices'])->name('products.prices');
        Route::put('/products/{product}/prices', [ProductController::class, 'updatePrices'])->name('products.prices.update');

        // Suppliers
        Route::resource('suppliers', SupplierController::class)->except(['destroy']);
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');

        // Reports
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('/reports/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');

        // User management
        Route::resource('users', UserController::class)->except(['destroy']);
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');

        // Gateway BuatQris Configuration
        Route::get('/settings/buatqris', [\App\Http\Controllers\BuatQrisSettingController::class, 'index'])->name('settings.buatqris');
        Route::post('/settings/buatqris', [\App\Http\Controllers\BuatQrisSettingController::class, 'update'])->name('settings.buatqris.update');
        Route::get('/settings/buatqris/check/{transactionId}', [\App\Http\Controllers\BuatQrisSettingController::class, 'checkStatus'])->name('settings.buatqris.check-status');
    });
});
