<?php

use App\Http\Controllers\Admin\ADashboardController;
use App\Http\Controllers\Admin\AGamblingDepositController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Customer\CDashboardController;
use App\Http\Controllers\Customer\CGamblingDepositController;
use App\Http\Controllers\Customer\CGamblingReportController;
use App\Http\Controllers\Reviewer\RDashboardController;
use App\Http\Controllers\Reviewer\RGamblingDepositController;
use App\Http\Controllers\Superadmin\SChannelController;
use App\Http\Controllers\Superadmin\SCustomerController;
use App\Http\Controllers\Superadmin\SDashboardController;
use App\Http\Controllers\Superadmin\SGamblingDepositController;
use App\Http\Controllers\Superadmin\SUserController;
use App\Http\Middleware\RoleMiddleware;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/', [AuthController::class, 'showLoginForm']);

Route::middleware(['auth', RoleMiddleware::class . ':superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [SCustomerController::class, 'index'])->name('index');
        Route::get('/{id}/show', [SCustomerController::class, 'show'])->name('show');
        Route::get('/data', [SCustomerController::class, 'data'])->name('data');
        Route::post('/', [SCustomerController::class, 'store'])->name('store');
        Route::put('/{id}', [SCustomerController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [SCustomerController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [SUserController::class, 'index'])->name('index');
        Route::get('/{id}/show', [SUserController::class, 'show'])->name('show');
        Route::get('/data', [SUserController::class, 'data'])->name('data');
        Route::post('/store', [SUserController::class, 'store'])->name('store');
        Route::put('/{id}', [SUserController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [SUserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('channels')->name('channels.')->group(function () {
        Route::get('/', [SChannelController::class, 'index'])->name('index');
        Route::get('/{id}/show', [SChannelController::class, 'show'])->name('show');
        Route::get('/data', [SChannelController::class, 'data'])->name('data');
        Route::post('/store', [SChannelController::class, 'store'])->name('store');
        Route::put('/{id}', [SChannelController::class, 'update'])->name('update');
        Route::delete('/{id}', [SChannelController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('gambling-deposits')->name('gambling_deposits.')->group(function () {
        Route::get('/', [SGamblingDepositController::class, 'index'])->name('index');
        Route::get('/data', [SGamblingDepositController::class, 'data'])->name('data');
        Route::get('/{id}/detail', [SGamblingDepositController::class, 'detail'])->name('detail');
        Route::get('/create', [SGamblingDepositController::class, 'create'])->name('create');
        Route::post('/store', [SGamblingDepositController::class, 'store'])->name('store');
        Route::delete('/{a}', [SGamblingDepositController::class, 'destroy'])->name('destroy');
    });
});


Route::middleware(['auth', RoleMiddleware::class . ':admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [ADashboardController::class, 'index'])->name('dashboard');

    Route::prefix('gambling-deposits')->name('gambling_deposits.')->group(function () {
        Route::get('/', [AGamblingDepositController::class, 'index'])->name('index');
        Route::get('/data', [AGamblingDepositController::class, 'data'])->name('data');
        Route::get('/{id}/detail', [AGamblingDepositController::class, 'detail'])->name('detail');
        Route::get('/create', [AGamblingDepositController::class, 'create'])->name('create');
        Route::post('/store', [AGamblingDepositController::class, 'store'])->name('store');
    });
});

Route::middleware(['auth', RoleMiddleware::class . ':reviewer'])->prefix('reviewer')->name('reviewer.')->group(function () {
    Route::get('/dashboard', [RDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('gambling-deposits')->name('gambling_deposits.')->group(function () {
        Route::get('/', [RGamblingDepositController::class, 'index'])->name('index');
        Route::get('/data', [RGamblingDepositController::class, 'data'])->name('data');
        Route::get('/{id}/edit', [RGamblingDepositController::class, 'edit'])->name('edit');
        Route::put('/{id}/update', [RGamblingDepositController::class, 'update'])->name('update');
        Route::post('/{id}/{status}', [RGamblingDepositController::class, 'changeStatus'])->name('changeStatus');
    });
});

Route::middleware(['auth', RoleMiddleware::class . ':customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('gambling-deposits')->name('gambling_deposits.')->group(function () {
        Route::get('/', [CGamblingDepositController::class, 'index'])->name('index');
        Route::get('/data', [CGamblingDepositController::class, 'data'])->name('data');
        Route::get('/{id}/detail', [CGamblingDepositController::class, 'detail'])->name('detail');
    });

    Route::prefix('gambling-reports')->name('gambling_reports.')->group(function () {
        Route::get('/', [CGamblingReportController::class, 'index'])->name('index');
        Route::get('/data', [CGamblingReportController::class, 'data'])->name('data');

        Route::post('/export', [CGamblingReportController::class, 'export'])->name('export');
    });
});


Route::get('/websites/check-url', function (Request $request) {
    $inputUrl = trim($request->query('url'));

    if (!$inputUrl) {
        return response()->json(['exists' => true, 'message' => 'URL tidak boleh kosong']);
    }

    $parsedUrl = parse_url(strtolower($inputUrl));
    $host = $parsedUrl['host'] ?? '';
    if (!$host) {
        return response()->json(['exists' => true, 'message' => 'URL tidak valid']);
    }

    $host = preg_replace('/^www\./', '', $host);

    $parts = explode('.', $host);
    $domainParts = array_slice($parts, -2);
    $inputDomain = implode('.', $domainParts);

    $websites = Website::all();

    foreach ($websites as $site) {
        $existingHost = parse_url(strtolower($site->website_url), PHP_URL_HOST);
        $existingHost = preg_replace('/^www\./', '', $existingHost);
        $existingParts = explode('.', $existingHost);
        $existingDomain = implode('.', array_slice($existingParts, -2));

        if ($inputDomain === $existingDomain) {
            return response()->json([
                'exists' => true,
                'message' => 'Domain atau subdomain sudah ada di database'
            ]);
        }
    }

    return response()->json(['exists' => false]);
});
