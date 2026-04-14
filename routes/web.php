<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminAgentsController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBookingsController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminNotifyController;
use App\Http\Controllers\Admin\AllBookingImportController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\OldBookingUploadController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Agent\Auth\AgentAuthController;
use App\Http\Controllers\Agent\bookings\AgentBookingSearchController;
use App\Http\Controllers\Agent\ChargingController;
use App\Http\Controllers\Agent\DashboardController;
use App\Http\Controllers\AgentBookingController;
use App\Http\Controllers\Auth\ChargeLoginController;
use App\Http\Controllers\AuthConsentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Charge\BookingPaymentLinkController;
use App\Http\Controllers\Charge\ChargeBookingStatusController;
use App\Http\Controllers\Charge\ChargeController;
use App\Http\Controllers\Charge\ChargingDashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PublicPaymentController;
use App\Http\Controllers\Mis\MisLoginController;
use App\Http\Controllers\Mis\MisDashboardController;
use App\Http\Controllers\Mis\MisBookingsController;
// payment contollers
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\Support\CsLoginController;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('public.home');

Route::get('/pay/{token}', [PublicPaymentController::class, 'show'])->name('public.pay.show');
Route::post('/pay/{token}', [PublicPaymentController::class, 'process'])->name('public.pay.process');
Route::get('/pay/{token}/success', [PublicPaymentController::class, 'success'])->name('public.pay.success');

// Customer access route (Signed for security)
Route::get('/consent/{id}', [AuthConsentController::class, 'customerConsentView'])
    ->name('customer.consent.view')
    ->middleware('signed'); // This prevents tampering with the ID

// agent auth routes
Route::get('/agent/login', [AgentAuthController::class, 'showLogin'])->name('agent.login');
Route::post('/agent/login', [AgentAuthController::class, 'login']);
Route::post('/agent/logout', [AgentAuthController::class, 'logout'])->name('agent.logout');

// admin auth routes
Route::get('/Admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/Admin/login', [AdminLoginController::class, 'login']);
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Customer supporrt auth routes
Route::get('/support/login', [CsLoginController::class, 'showLoginForm'])->name('support.login');
Route::post('/support/login', [CsLoginController::class, 'login']);
Route::post('/support/logout', [CsLoginController::class, 'logout'])->name('support.logout');

// charge auth routes
Route::get('/charge/login', [ChargeLoginController::class, 'showLoginForm'])->name('charge.login');
Route::post('/charge/login', [ChargeLoginController::class, 'login']);
Route::post('/charge/logout', [ChargeLoginController::class, 'logout'])->name('charge.logout');

// MIS routes
Route::get('/mis/login', [MisLoginController::class, 'showLoginForm'])->name('mis.login');
Route::post('/mis/login', [MisLoginController::class, 'login']);
Route::post('/mis/logout', [MisLoginController::class, 'logout'])->name('mis.logout');

// CHARGING TEAM
Route::middleware(['auth', 'role:charge'])->prefix('charge')->name('charge.')->group(function () {
    // Route::get('/dashboard', [ChargeController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [ChargingDashboardController::class, 'index'])->name('dashboard');

    Route::get('/assignments/{assignment}/details', [ChargeController::class, 'showDetails'])->name('assignments.details');
    Route::get('/assignments/{assignment}/accept-form', [ChargeController::class, 'showAcceptForm'])->name('assignments.accept-form');
    Route::post('/assignments/{assignment}/accept', [ChargeController::class, 'accept'])->name('assignments.accept');
    Route::post('/assignments/{assignment}/reject', [ChargeController::class, 'reject'])->name('assignments.reject');

    Route::post('/bookings/{booking}/mark-viewed', [ChargeController::class, 'markAsViewed'])->name('bookings.mark-viewed');
    Route::get('/bookings/{booking}', [ChargeController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/accept', [ChargeController::class, 'acceptAssignment'])->name('bookings.accept');

    Route::get('/booking/{id}/authorize-edit', [AuthConsentController::class, 'edit'])->name('authorize.edit');
    Route::post('/booking/{id}/authorize-preview', [AuthConsentController::class, 'preview'])->name('authorize.preview');
    Route::get('/booking/{id}/authorize-preview', [AuthConsentController::class, 'previewPage'])->name('authorize.preview.page');
    Route::post('/booking/{id}/authorize-send', [AuthConsentController::class, 'send'])->name('authorize.send');
    Route::post('/booking/{id}/authorize-resend', [AuthConsentController::class, 'resend'])->name('authorize.resend');
    Route::patch('/charge/booking/{id}/auth-done', [AuthConsentController::class, 'markAuthDone'])->name('auth.done');
    Route::post('/bookings/{id}/update-status', [ChargeBookingStatusController::class, 'update'])->name('bookings.update-status');

    Route::get('/bookings/{booking}/payment-link/create', [BookingPaymentLinkController::class, 'create'])->name('bookings.payment-link.create');
    Route::post('/bookings/{booking}/payment-link', [BookingPaymentLinkController::class, 'store'])->name('bookings.payment-link.store');
    Route::post('/bookings/{booking}/payment-link/{link}/send-mail', [BookingPaymentLinkController::class, 'sendMail'])->name('bookings.payment-link.send-mail');

    Route::get('/test-auth-email', function () {
        $booking = Booking::first(); // get one booking from database

        if (! $booking) {
            return 'No booking found in database.';
        }

        $emailBody = '
        <p>Dear Customer,</p>
        <p>This is a test payment authorization email for preview.</p>
        <p>Please review your booking and confirm the charges.</p>
    ';

        Mail::send('emails.customer-final-auth', [
            'booking' => $booking,
            'emailBody' => $emailBody,
        ], function ($message) {
            $message->to('prashant.saini@trafficpirates.com')
                ->subject('Test Payment Authorization Email');
        });

        return 'Test email sent successfully.';
    });
});

// AGENT DASHBOARD ROUTES ONLY (POST-LOGIN)
Route::middleware(['auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'Index'])->name('dashboard');

    // Route::get('/dashboard', [BookingController::class, 'agentIndex'])->name('dashboard');
    Route::get('/bookings', [BookingController::class, 'agentIndex'])->name('bookings.index');
    Route::get('/bookings/create', [AgentBookingController::class, 'create'])->name('bookings.create');

    Route::post('/bookings', [AgentBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'agentShow'])->name('bookings.show');
    Route::get('/{booking}/edit', [BookingController::class, 'agentEdit'])->name('bookings.edit');
    Route::get('bookings/{booking}/update-pnr', [AgentBookingController::class, 'editPnr'])->name('bookings.update-pnr');
    Route::patch('bookings/{booking}/update-pnr', [AgentBookingController::class, 'updatePnr'])->name('bookings.update');
    Route::get('/bookings/{booking}/charge', [ChargingController::class, 'chargeByAgent'])->name('bookings.charge');
    Route::post('/bookings/{booking}/charge/assign', [ChargingController::class, 'assignForCharging'])->name('bookings.charge.assign');

    Route::get('/booking-search', [AgentBookingSearchController::class, 'index'])->name('bookings.search');
    Route::get('/booking-search/results', [AgentBookingSearchController::class, 'search'])->name('bookings.search.results');
});

// ADMIN ROUTES
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])
            ->name('activity.logs');

        Route::get('/activity-logs/latest', [ActivityLogController::class, 'latest'])
            ->name('activity.logs.latest');
        Route::resource('merchants', MerchantController::class)->except(['show']);

        // export csv of single booking
        Route::get('/bookings/{booking}/export-csv', [App\Http\Controllers\Admin\BookingExportController::class, 'exportSingle'])
            ->name('bookings.export.csv');
        Route::post('/bookings/export/all', [AllBookingImportController::class, 'export'])
            ->name('bookings.export.all');
        Route::post('/bookings/export-selected', [AllBookingImportController::class, 'exportSelected'])
            ->name('bookings.export.selected');

        // upload old bookings feature
        Route::get('/bookings/upload-old', [OldBookingUploadController::class, 'index'])
            ->name('bookings.upload-old');

        Route::post('/bookings/upload-old', [OldBookingUploadController::class, 'store'])
            ->name('bookings.upload-old.store');

    });
Route::middleware(['auth', 'role:admin|manager'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/agents-list', [\App\Http\Controllers\Admin\AdminAgentsController::class, 'index'])->name('agents.index');
    Route::get('/bookings/all', [\App\Http\Controllers\Admin\AdminBookingsController::class, 'all'])->name('bookings.all');
    Route::get('/bookings', [\App\Http\Controllers\Admin\AdminBookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [\App\Http\Controllers\Admin\AdminBookingsController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{id}/edit', [\App\Http\Controllers\Admin\AdminBookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [\App\Http\Controllers\Admin\AdminBookingsController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{id}', [\App\Http\Controllers\Admin\AdminBookingsController::class, 'destroy'])->name('bookings.destroy');
});
// customer support ROUTES
Route::middleware(['auth', 'role:support'])->prefix('support')->name('support.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Support\SupportDashboardController::class, 'index'])->name('dashboard');
    // Route::get('/agents-list', [\App\Http\Controllers\Support\SupportAgentsController::class, 'index'])->name('agents.index');
    Route::get('/bookings/all', [\App\Http\Controllers\Support\SupportBookingsController::class, 'all'])->name('bookings.all');
    Route::get('/bookings', [\App\Http\Controllers\Support\SupportBookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [\App\Http\Controllers\Support\SupportBookingsController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{id}/edit', [\App\Http\Controllers\Support\SupportBookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [\App\Http\Controllers\Support\SupportBookingsController::class, 'update'])->name('bookings.update');
    Route::put('/bookings/{id}/support-status', [\App\Http\Controllers\Support\SupportBookingsController::class, 'updateStatus'])->name('bookings.update-status');
});
// MIS PANEL ROUTES - Role: mis
Route::middleware(['auth', 'role:mis'])->prefix('mis')->name('mis.')->group(function () {
    Route::get('/bookings', [AdminBookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}/edit', [AdminBookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [AdminBookingsController::class, 'update'])->name('bookings.update');

    Route::get('/bookings/import', [\App\Http\Controllers\Mis\BookingImportController::class, 'create'])->name('bookings.import.form');
    Route::post('/bookings/import', [\App\Http\Controllers\Mis\BookingImportController::class, 'store'])->name('bookings.import.store');
});

// GENERAL LOGOUT
Route::middleware('auth')->group(function () {
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});

// Admin Authentication Routes (No middleware needed here)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// Protected Admin Routes (Both Admin and Manager can access)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|manager'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('dashboard');

    // admin can change the status of the agent by clicking on the button in the agents list page,
    Route::post('/agents/{agent}/toggle-status', [AdminAgentsController::class, 'toggleStatus'])
        ->name('admin.agents.toggleStatus');
    Route::post('/agents/{agent}/toggle-status', [AdminAgentsController::class, 'toggleStatus'])->name('agents.toggleStatus');

    // User Management Routes (Only Admin can access these)
    Route::prefix('users')->name('users.')->middleware(['role:admin'])->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::patch('/{id}/toggle-active', [UserController::class, 'toggleActive'])->name('toggle-active');
        Route::patch('/{id}/toggle-block', [UserController::class, 'toggleBlock'])->name('toggle-block');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });
    // Notification Management Routes (Only Admin)
    Route::prefix('notifications')->name('notifications.')->middleware(['role:admin'])->group(function () {
        Route::get('/', [AdminNotifyController::class, 'index'])->name('index');
        // Route::get('/count', [NotificationController::class, 'getUnreadCount'])->name('count');
        Route::get('/create', [AdminNotifyController::class, 'create'])->name('create');
        Route::post('/', [AdminNotifyController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminNotifyController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminNotifyController::class, 'update'])->name('update');
        Route::post('/{id}/duplicate', [AdminNotifyController::class, 'duplicate'])->name('duplicate');
        Route::patch('/{id}/toggle-active', [AdminNotifyController::class, 'toggleActive'])->name('toggle-active');
        Route::delete('/{id}', [AdminNotifyController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/stats', [AdminNotifyController::class, 'stats'])->name('stats');
    });
    // Settings (Both Admin and Manager can access)
    Route::get('/settings/bookings', [SettingsController::class, 'bookings'])
        ->name('settings.bookings'); // used for the page itself

    // Add new option (all three forms currently call these names)
    Route::post('/settings/bookings', [SettingsController::class, 'store'])
        ->name('settings.store');

    // Delete option
    Route::delete('/settings/bookings/{id}', [SettingsController::class, 'destroy'])
        ->name('settings.destroy');

    // Reports (Both Admin and Manager can access)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // Agent Management (Both Admin and Manager can access)
    Route::get('/agents', [AdminAgentsController::class, 'index'])->name('agents.index');
    Route::get('/agents/{agent}', [AdminAgentsController::class, 'show'])->name('agents.show');

    // Bookings (Both Admin and Manager can access)
    Route::get('/bookings', [AdminBookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingsController::class, 'show'])->name('bookings.show');

});

Route::middleware(['auth'])->group(function () {
    // Notification routes for all authenticated users
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::get('/unread', [NotificationController::class, 'getUnreadNotifications'])->name('unread');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });
});

Route::middleware(['auth'])->group(function () {
    Route::get('/booking-status/{bookingId}', [StatusController::class, 'showByBooking'])->name('status.show');
    Route::post('/booking-status/create/{bookingId}', [StatusController::class, 'storeFromBooking'])->name('status.store');
    Route::post('/booking-status/sync/{bookingId}', [StatusController::class, 'syncFromBooking'])->name('status.sync');
    Route::put('/booking-status/{id}', [StatusController::class, 'update'])->name('status.update');
});

Route::get('/agent/test-notifications', function () {
    return view('agent.test-notifications');
})->middleware(['auth', 'role:agent'])->name('agent.test');

// clear all cache
Route::get('/clear-all-cache', function () {
    // Clear config cache
    Artisan::call('config:clear');

    // Clear route cache
    Artisan::call('route:clear');

    // Optimize the application (clears all other caches like application cache and views)
    Artisan::call('optimize:clear');

    return 'Configuration, Routes, and all other caches cleared and application optimized!';
});

// MIS PANEL ROUTES - Role: mis
Route::middleware(['auth', 'role:mis'])->prefix('mis')->name('mis.')->group(function () {
    Route::get('/dashboard', [MisDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings/all', [MisBookingsController::class, 'all'])->name('bookings.all');
    // Route::get('/agents-list', [\App\Http\Controllers\Admin\AdminAgentsController::class, 'index'])->name('agents.index');
    Route::get('/bookings', [MisBookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [MisBookingsController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{id}/edit', [MisBookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [MisBookingsController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{id}', [MisBookingsController::class, 'destroy'])->name('bookings.destroy');
});