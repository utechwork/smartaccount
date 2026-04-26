<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccountStatementImportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FlatController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ImportRuleController;
use App\Http\Controllers\PettyCashController;
use App\Http\Controllers\PdfExportController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Account Statement Routes
Route::get('/account-statement', [AccountStatementImportController::class, 'index'])->name('account-statement.index');
Route::get('/account-statement/import', [AccountStatementImportController::class, 'showForm'])->name('account-statement.import.form');
Route::post('/account-statement/import', [AccountStatementImportController::class, 'import'])->name('account-statement.import');
Route::post('/account-statement/{id}/tag-flat', [AccountStatementImportController::class, 'tagFlat'])->name('account-statement.tag-flat');
Route::post('/account-statement/{id}/untag-flat', [AccountStatementImportController::class, 'untagFlat'])->name('account-statement.untag-flat');
Route::get('/account-statement/{id}/suggest-flat', [AccountStatementImportController::class, 'suggestFlat'])->name('account-statement.suggest-flat');
Route::post('/account-statement/{id}/tag-vendor', [AccountStatementImportController::class, 'tagVendor'])->name('account-statement.tag-vendor');
Route::post('/account-statement/{id}/untag-vendor', [AccountStatementImportController::class, 'untagVendor'])->name('account-statement.untag-vendor');
Route::post('/account-statement/{id}/create-and-tag-vendor', [AccountStatementImportController::class, 'createAndTagVendor'])->name('account-statement.create-and-tag-vendor');
Route::post('/account-statement/bulk-tag-vendor', [AccountStatementImportController::class, 'bulkTagVendor'])->name('account-statement.bulk-tag-vendor');

// PDF Export Routes
Route::get('/account-statement/export/pdf', [PdfExportController::class, 'accountStatements'])->name('account-statement.export.pdf');
Route::get('/vendors-expense-report/export/pdf', [PdfExportController::class, 'vendorExpenseReport'])->name('vendors.expense-report.export.pdf');
Route::get('/flats/{id}/export/pdf', [PdfExportController::class, 'flatDetails'])->name('flats.export.pdf');
Route::get('/vendors/{id}/export/pdf', [PdfExportController::class, 'vendorDetails'])->name('vendors.export.pdf');

// Vendor Routes
Route::resource('vendors', VendorController::class);
Route::get('/vendors-expense-report', [VendorController::class, 'expenseReport'])->name('vendors.expense-report');

// Category Routes (Management)
Route::resource('categories', CategoryController::class, ['except' => ['show']]);
Route::get('/categories/by-type', [CategoryController::class, 'getByType'])->name('categories.by-type');

// Import Rules Routes
Route::resource('import-rules', ImportRuleController::class)->except(['show']);
Route::post('/import-rules/apply', [ImportRuleController::class, 'executeApplyRules'])->name('import-rules.apply');
Route::post('/import-rules/create-vendor', [ImportRuleController::class, 'createVendor'])->name('import-rules.create-vendor');
Route::post('/import-rules/create-category', [ImportRuleController::class, 'createCategory'])->name('import-rules.create-category');

// API Routes for Account Statement Categories
Route::get('/api/account-statement/{statement}/categories', [\App\Http\Controllers\Api\AccountStatementCategoryController::class, 'getCategories']);
Route::post('/api/account-statement/{statement}/categories', [\App\Http\Controllers\Api\AccountStatementCategoryController::class, 'syncCategories']);

// API Routes for Account Statement Expense Details
Route::get('/api/account-statement/{statement}/expense', [\App\Http\Controllers\Api\AccountStatementCategoryController::class, 'getExpense']);
Route::post('/api/account-statement/{statement}/expense', [\App\Http\Controllers\Api\AccountStatementCategoryController::class, 'updateExpense']);

// API Routes for Vendor Categories
Route::get('/api/vendor/{vendor}/categories', [VendorController::class, 'getCategories']);

// Flats Routes
Route::resource('flats', FlatController::class);
Route::get('/flats/floor/{floorNumber}', [FlatController::class, 'byFloor'])->name('flats.floor');
Route::get('/flats-statistics', [FlatController::class, 'statistics'])->name('flats.statistics');

// Petty Cash Routes
Route::resource('petty-cash', PettyCashController::class);
