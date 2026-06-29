<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdminAgentsController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBookingsController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminNotifyController;
use App\Http\Controllers\Admin\AirlineController;
use App\Http\Controllers\Admin\AllBookingImportController;
use App\Http\Controllers\Admin\CallLogsController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\OldBookingUploadController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Agent\AgentBookingUpdatesController;
use App\Http\Controllers\Agent\Auth\AgentAuthController;
use App\Http\Controllers\Agent\bookings\AgentBookingSearchController;
use App\Http\Controllers\Agent\CallLogController;
use App\Http\Controllers\Agent\ChargingController;
use App\Http\Controllers\Agent\DashboardController;
use App\Http\Controllers\Agent\ItineraryParserController;
use App\Http\Controllers\AgentBookingController;
use App\Http\Controllers\Auth\ChargeLoginController;
use App\Http\Controllers\AuthConsentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Changes\ChangesBookingsController;
use App\Http\Controllers\Changes\ChangesDashboardController;
use App\Http\Controllers\Changes\ChangesLoginController;
use App\Http\Controllers\Charge\BookingPaymentLinkController;
use App\Http\Controllers\Charge\ChargeBookingStatusController;
use App\Http\Controllers\Charge\ChargeController;
use App\Http\Controllers\Charge\ChargingDashboardController;
use App\Http\Controllers\Mis\MisBookingsController;
use App\Http\Controllers\Mis\MisDashboardController;
use App\Http\Controllers\Mis\MisLoginController;
use App\Http\Controllers\MisManager\MisManagerBookingsController;
use App\Http\Controllers\MisManager\MisManagerDashboardController;
use App\Http\Controllers\MisManager\MisManagerLoginController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
// payment contollers
use App\Http\Controllers\PublicPaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\Support\CsLoginController;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
// tetsing new feature
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

// use App\Mail\TestMail;

Route::get('/', function () {
    return view('welcome');
})->name('public.home');

Route::get('/pay/{token}', [PublicPaymentController::class, 'show'])->name('public.pay.show');
Route::post('/pay/{token}', [PublicPaymentController::class, 'process'])->name('public.pay.process');
Route::get('/pay/{token}/success', [PublicPaymentController::class, 'success'])->name('public.pay.success');

// Customer access route (Signed for security)
Route::get('/consent/{id}', [AuthConsentController::class, 'customerConsentView'])
    ->name('customer.consent.view')
    ->middleware('signed');

// agent auth routes
Route::get('/agent/login', [AgentAuthController::class, 'showLogin'])->name('agent.login');
Route::post('/agent/login', [AgentAuthController::class, 'login']);
Route::post('/agent/logout', [AgentAuthController::class, 'logout'])->name('agent.logout');

// admin auth routes - FIXED: Only one admin login route
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);
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

// Changes routes
Route::get('/changes/login', [ChangesLoginController::class, 'showLoginForm'])->name('changes.login');
Route::post('/changes/login', [ChangesLoginController::class, 'login']);
Route::post('/changes/logout', [ChangesLoginController::class, 'logout'])->name('changes.logout');

// MIS MANAGER routes (login)
Route::get('/mis-manager/login', [MisManagerLoginController::class, 'showLoginForm'])->name('mis-manager.login');
Route::post('/mis-manager/login', [MisManagerLoginController::class, 'login']);
Route::post('/mis-manager/logout', [MisManagerLoginController::class, 'logout'])->name('mis-manager.logout');

// ==================== ADMIN ROUTES ====================
// REMOVED DUPLICATE ADMIN LOGIN ROUTES - Already defined above

// Protected Admin Routes (Only Admin can access)
    Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // ==================== TICKET ROUTES ====================
    // Original route - keep for backward compatibility
     // Show edit form
    Route::get('/bookings/{booking}/ticket/edit', [AdminBookingsController::class, 'editTicket'])
        ->name('bookings.ticket.edit');
    
    // Generate and download PDF
    Route::post('/bookings/{booking}/ticket/generate', [AdminBookingsController::class, 'generateTicket'])
        ->name('bookings.ticket.generate');
    
    // Download existing ticket PDF
    Route::get('/bookings/{booking}/ticket/download', [AdminBookingsController::class, 'downloadTicket'])
        ->name('bookings.ticket.download');

    // ==================== BOOKING ROUTES ====================
    Route::get('/bookings/all', [AdminBookingsController::class, 'all'])->name('bookings.all');
    Route::get('/bookings', [AdminBookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [AdminBookingsController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{id}/edit', [AdminBookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [AdminBookingsController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{id}', [AdminBookingsController::class, 'destroy'])->name('bookings.destroy');

    // ==================== AGENT ROUTES ====================
    Route::post('/agents/{agent}/toggle-status', [AdminAgentsController::class, 'toggleStatus'])->name('agents.toggleStatus');
    Route::get('/agents', [AdminAgentsController::class, 'index'])->name('agents.index');
    Route::get('/agents/{agent}', [AdminAgentsController::class, 'show'])->name('agents.show');

    // ==================== USER MANAGEMENT ROUTES ====================
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

    // ==================== NOTIFICATION MANAGEMENT ROUTES ====================
    Route::prefix('notifications')->name('notifications.')->middleware(['role:admin'])->group(function () {
        Route::get('/', [AdminNotifyController::class, 'index'])->name('index');
        Route::get('/count', [NotificationController::class, 'getUnreadCount'])->name('count');
        Route::get('/create', [AdminNotifyController::class, 'create'])->name('create');
        Route::post('/', [AdminNotifyController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminNotifyController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminNotifyController::class, 'update'])->name('update');
        Route::post('/{id}/duplicate', [AdminNotifyController::class, 'duplicate'])->name('duplicate');
        Route::patch('/{id}/toggle-active', [AdminNotifyController::class, 'toggleActive'])->name('toggle-active');
        Route::delete('/{id}', [AdminNotifyController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/stats', [AdminNotifyController::class, 'stats'])->name('stats');
    });

    // ==================== SETTINGS ROUTES ====================
    Route::get('/settings/bookings', [SettingsController::class, 'bookings'])->name('settings.bookings');
    Route::post('/settings/bookings', [SettingsController::class, 'store'])->name('settings.store');
    Route::delete('/settings/bookings/{key}/{id}', [SettingsController::class, 'destroy'])->name('settings.destroy');

    // ==================== REPORTS ROUTES ====================
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');

    // ==================== OTHER ADMIN RESOURCES ====================
    Route::resource('airlines', AirlineController::class);
    Route::resource('merchants', MerchantController::class)->except(['show']);
    
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity.logs');
    Route::get('/online-users', [ActivityLogController::class, 'onlineUsers'])->name('online.users');

    // ==================== EXPORT ROUTES ====================
    Route::get('/bookings/{booking}/export-csv', [App\Http\Controllers\Admin\BookingExportController::class, 'exportSingle'])->name('bookings.export.csv');
    Route::post('/bookings/export/all', [AllBookingImportController::class, 'export'])->name('bookings.export.all');
    Route::post('/bookings/export-selected', [AllBookingImportController::class, 'exportSelected'])->name('bookings.export.selected');

    // ==================== OLD BOOKINGS UPLOAD ====================
    Route::get('/bookings/upload-old', [OldBookingUploadController::class, 'index'])->name('bookings.upload-old');
    Route::post('/bookings/upload-old', [OldBookingUploadController::class, 'store'])->name('bookings.upload-old.store');

    // ==================== CALL LOGS ====================
    Route::prefix('call-log')->name('call-log.')->group(function () {
        Route::get('/', [CallLogsController::class, 'index'])->name('index');
        Route::get('/{callLog}', [CallLogsController::class, 'show'])->name('show');
        Route::get('/export/csv', [CallLogsController::class, 'export'])->name('export');
    });

    // ==================== PDF EDITOR ROUTES ====================
    Route::get('/pdf-editor/wysiwyg', [\App\Http\Controllers\Admin\PDFEditorController::class, 'wysiwygBuilder'])->name('pdf-editor.wysiwyg');
    Route::get('/pdf-editor/wysiwyg/{bookingId}', [\App\Http\Controllers\Admin\PDFEditorController::class, 'wysiwygBuilder'])->name('pdf-editor.wysiwyg.booking');
    Route::post('/pdf/generate-wysiwyg', [\App\Http\Controllers\Admin\PDFEditorController::class, 'generateFromWysiwyg'])->name('pdf.generate.wysiwyg');
    Route::post('/pdf/save-template', [\App\Http\Controllers\Admin\PDFEditorController::class, 'saveTemplate'])->name('pdf.save-template');
    Route::get('/pdf/load-template', [\App\Http\Controllers\Admin\PDFEditorController::class, 'loadTemplate'])->name('pdf.load-template');
    Route::post('/bookings/{id}/pdf-from-template', [\App\Http\Controllers\Admin\PDFEditorController::class, 'generateBookingPDF'])->name('bookings.pdf-from-template');
    
    // Template endpoints
    Route::get('/bookings/{bookingId}/template-data', [\App\Http\Controllers\Admin\PDFEditorController::class, 'getTicketTemplate'])->name('bookings.template-data');
    Route::post('/bookings/{bookingId}/template-save', [\App\Http\Controllers\Admin\PDFEditorController::class, 'saveTemplateChanges'])->name('bookings.template-save');
});

// ==================== SUPPORT ROUTES ====================
Route::middleware(['auth', 'role:support'])->prefix('support')->name('support.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Support\SupportDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings/all', [\App\Http\Controllers\Support\SupportBookingsController::class, 'all'])->name('bookings.all');
    Route::get('/bookings', [\App\Http\Controllers\Support\SupportBookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [\App\Http\Controllers\Support\SupportBookingsController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{id}/edit', [\App\Http\Controllers\Support\SupportBookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [\App\Http\Controllers\Support\SupportBookingsController::class, 'update'])->name('bookings.update');
    Route::post('/bookings/{id}/chargeback', [\App\Http\Controllers\Support\SupportBookingsController::class, 'storeChargeback'])->name('bookings.chargeback.store');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
});

// ==================== AGENT ROUTES ====================
Route::middleware(['auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'Index'])->name('dashboard');
    Route::get('/bookings', [BookingController::class, 'agentIndex'])->name('bookings.index');
    Route::get('/bookings/create', [AgentBookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings', [AgentBookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'agentShow'])->name('bookings.show');
    Route::get('/{booking}/edit', [BookingController::class, 'agentEdit'])->name('bookings.edit');
    Route::put('/{booking}/edit', [BookingController::class, 'agentUpdate'])->name('bookings.update-passengers');
    Route::get('bookings/{booking}/update-pnr', [AgentBookingController::class, 'editPnr'])->name('bookings.update-pnr');
    Route::patch('bookings/{booking}/update-pnr', [AgentBookingController::class, 'updatePnr'])->name('bookings.update');
    Route::get('/bookings/{booking}/charge', [ChargingController::class, 'chargeByAgent'])->name('bookings.charge');
    Route::post('/bookings/{booking}/charge/assign', [ChargingController::class, 'assignForCharging'])->name('bookings.charge.assign');
    Route::get('/booking-search', [AgentBookingSearchController::class, 'index'])->name('bookings.search');
    Route::post('/booking-search/results', [AgentBookingSearchController::class, 'search'])->name('bookings.search.results');
    Route::post('/booking-updates/search', [AgentBookingUpdatesController::class, 'searchByPnr'])->name('booking-updates.search');
    Route::post('/booking-updates', [AgentBookingUpdatesController::class, 'store'])->name('booking-updates.store');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/bookings/{bookingId}/add-remark', [AgentBookingController::class, 'addRemark'])->name('agent.bookings.add-remark');
    Route::get('/bookings/{booking}/assign', [App\Http\Controllers\Agent\AssignBookingController::class, 'create'])->name('bookings.assign.create');
    Route::post('/bookings/{booking}/assign', [App\Http\Controllers\Agent\AssignBookingController::class, 'store'])->name('bookings.assign.store');
    Route::get('/assignments', [App\Http\Controllers\Agent\AssignBookingController::class, 'index'])->name('assignments.index');
    Route::get('/assignments/{assignment}', [App\Http\Controllers\Agent\AssignBookingController::class, 'show'])->name('assignments.show');
    
    // Call log routes
    Route::prefix('call-log')->name('call-log.')->group(function () {
        Route::get('/', [CallLogController::class, 'index'])->name('index');
        Route::get('/create', [CallLogController::class, 'create'])->name('create');
        Route::post('/', [CallLogController::class, 'store'])->name('store');
        Route::get('/{callLog}', [CallLogController::class, 'show'])->name('show');
    });
    
    // New itinerary parser route
    Route::get('/bookings/createbooking', [ItineraryParserController::class, 'create'])->name('bookings.createbooking');
    Route::post('/itinerary/decode', [ItineraryParserController::class, 'decode'])->name('itinerary.decode');
});

// ==================== CHARGE ROUTES ====================
Route::middleware(['auth', 'role:charge,admin'])->prefix('charge')->name('charge.')->group(function () {
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
    Route::get('/bookings/{booking}/payment-link/create', [BookingPaymentLinkController::class, 'create'])->name('bookings.payment-link.create');
    Route::post('/bookings/{booking}/payment-link', [BookingPaymentLinkController::class, 'store'])->name('bookings.payment-link.store');
    Route::post('/bookings/{booking}/payment-link/{link}/send-mail', [BookingPaymentLinkController::class, 'sendMail'])->name('bookings.payment-link.send-mail');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/assignments/dashboard', [App\Http\Controllers\Charge\ChangesTeamController::class, 'dashboard'])->name('assignments.dashboard');
    Route::get('/assignments/{assignment}', [App\Http\Controllers\Charge\ChangesTeamController::class, 'show'])->name('assignments.show');
    Route::post('/assignments/{assignment}/accept', [App\Http\Controllers\Charge\ChangesTeamController::class, 'accept'])->name('assignments.accept');
    Route::post('/assignments/{assignment}/reject', [App\Http\Controllers\Charge\ChangesTeamController::class, 'reject'])->name('assignments.reject');
    Route::post('/assignments/{assignment}/complete', [App\Http\Controllers\Charge\ChangesTeamController::class, 'complete'])->name('assignments.complete');
    Route::post('/assignments/{assignment}/remarks', [App\Http\Controllers\Charge\ChangesTeamController::class, 'addRemark'])->name('assignments.remarks');
});

// ==================== MIS ROUTES ====================
Route::middleware(['auth', 'role:mis'])->prefix('mis')->name('mis.')->group(function () {
    Route::get('/dashboard', [MisDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings/all', [MisBookingsController::class, 'all'])->name('bookings.all');
    Route::get('/bookings', [MisBookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [MisBookingsController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{id}/edit', [MisBookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [MisBookingsController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{id}', [MisBookingsController::class, 'destroy'])->name('bookings.destroy');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
});

// ==================== MIS MANAGER ROUTES ====================
Route::middleware(['auth', 'role:mis-manager'])->prefix('mis-manager')->name('mis-manager.')->group(function () {
    Route::get('/dashboard', [MisManagerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings/all', [MisManagerBookingsController::class, 'all'])->name('bookings.all');
    Route::get('/bookings', [MisManagerBookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [MisManagerBookingsController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{id}/edit', [MisManagerBookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [MisManagerBookingsController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{id}', [MisManagerBookingsController::class, 'destroy'])->name('bookings.destroy');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
});

// ==================== CHANGES ROUTES ====================
Route::middleware(['auth', 'role:changes'])->prefix('changes')->name('changes.')->group(function () {
    Route::get('/dashboard', [ChangesDashboardController::class, 'index'])->name('dashboard');
    Route::get('/bookings/all', [ChangesBookingsController::class, 'all'])->name('bookings.all');
    Route::get('/bookings', [ChangesBookingsController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [ChangesBookingsController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{id}/edit', [ChangesBookingsController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{id}', [ChangesBookingsController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{id}', [ChangesBookingsController::class, 'destroy'])->name('bookings.destroy');
    Route::get('/booking-requests', function () {
        return view('changes.booking-requests');
    })->name('booking-requests');
    Route::post('/booking-requests/{id}/remark', function (Illuminate\Http\Request $request, $id) {
        $request->validate([
            'remark_text' => 'required|string|min:3|max:1000',
        ]);

        $assignment = \App\Models\BookingAssignment::findOrFail($id);
        $assignment->booking->remarks()->create([
            'agent_id' => auth()->id(),
            'remark_text' => $request->remark_text,
            'remark_type' => 'changes_team',
        ]);

        return redirect()->back()->with('success', 'Remark added successfully.');
    })->name('booking-requests.remark');
    Route::post('/booking-requests/{id}/accept', function ($id) {
        $assignment = \App\Models\BookingAssignment::findOrFail($id);
        $assignment->update(['status' => 'completed']);

        return redirect()->back()->with('success', 'Booking request accepted successfully!');
    })->name('booking-requests.accept');
    Route::post('/booking-requests/{id}/reject', function ($id) {
        $assignment = \App\Models\BookingAssignment::findOrFail($id);
        $assignment->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Booking request rejected successfully!');
    })->name('booking-requests.reject');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
});

// ==================== GENERAL ROUTES ====================
Route::middleware('auth')->group(function () {
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    })->name('logout');

    // Notification routes for all authenticated users
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::get('/unread', [NotificationController::class, 'getUnreadNotifications'])->name('unread');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });

    // Booking status routes
    Route::get('/booking-status/{bookingId}', [StatusController::class, 'showByBooking'])->name('status.show');
    Route::post('/booking-status/create/{bookingId}', [StatusController::class, 'storeFromBooking'])->name('status.store');
    Route::post('/booking-status/sync/{bookingId}', [StatusController::class, 'syncFromBooking'])->name('status.sync');
    Route::put('/booking-status/{id}', [StatusController::class, 'update'])->name('status.update');
});

// ==================== NOTIFICATION PAGES ====================
Route::get('/agent/notifications', function () {
    return view('agent.notifications');
})->middleware(['auth', 'role:agent'])->name('agent.notifications');

Route::get('/agent/booking-requests', function () {
    return view('agent.booking-requests');
})->middleware(['auth', 'role:agent'])->name('agent.booking-requests');

Route::get('/charge/notifications', function () {
    return view('charge.notifications');
})->middleware(['auth', 'role:charge'])->name('charge.notifications');

Route::get('/mis/notifications', function () {
    return view('mis.notifications');
})->middleware(['auth', 'role:mis'])->name('mis.notifications');

Route::get('/mis-manager/notifications', function () {
    return view('mis-manager.notifications');
})->middleware(['auth', 'role:mis-manager'])->name('mis-manager.notifications');

Route::get('/changes/notifications', function () {
    return view('changes.notifications');
})->middleware(['auth', 'role:changes'])->name('changes.notifications');

// ==================== CACHE CLEAR ====================
Route::get('/clear-all-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('optimize:clear');

    return 'Configuration, Routes, and all other caches cleared and application optimized!';
});

// ==================== ADMIN LOGS ====================
Route::get('/admin/logs/latest', [ActivityLogController::class, 'latest'])->name('admin.logs.latest');