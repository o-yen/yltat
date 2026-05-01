<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TalentController;
use App\Http\Controllers\Admin\PlacementController;
use App\Http\Controllers\Admin\BudgetController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\PublicPortalController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Admin\ApplicationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SyarikatPelaksanaController;
use App\Http\Controllers\Admin\SyarikatPenempatanController;
use App\Http\Controllers\Admin\KehadiranPrestasiController;
use App\Http\Controllers\Admin\KewanganElaunController;
use App\Http\Controllers\Admin\StatusSuratController;
use App\Http\Controllers\Admin\LogbookUploadController;
use App\Http\Controllers\Admin\DailyLogController as AdminDailyLogController;
use App\Http\Controllers\Admin\SystemGuideController;
use App\Http\Controllers\Admin\IsuRisikoController;
use App\Http\Controllers\Admin\TrainingRecordController;
use App\Http\Controllers\Admin\KpiDashboardController;
use App\Http\Controllers\Admin\ApplicantRequestController;
use App\Http\Controllers\Talent\DashboardController as TalentDashboardController;
use App\Http\Controllers\Talent\DailyLogController as TalentDailyLogController;
use App\Http\Controllers\Talent\PlacementInfoController as TalentPlacementInfoController;
use App\Http\Controllers\Talent\AllowanceController as TalentAllowanceController;
use App\Http\Controllers\Company\DashboardController as CompanyDashboardController;
use App\Http\Controllers\Company\PlacementController as CompanyPlacementController;
use App\Http\Controllers\Company\FeedbackController as CompanyFeedbackController;
use App\Http\Controllers\Company\FinanceController as CompanyFinanceController;
use App\Http\Controllers\Talent\ProfileController as TalentProfileController;

// ========================
// Public Routes
// ========================
Route::get('/', function () {
    return redirect()->route('portal.index');
});

Route::get('/portal', [PublicPortalController::class, 'index'])->name('portal.index');
Route::get('/portal/daftar', [RegistrationController::class, 'create'])->name('portal.register');
Route::post('/portal/daftar', [RegistrationController::class, 'store'])->name('portal.register.store');
Route::get('/portal/daftar/berjaya', [RegistrationController::class, 'success'])->name('portal.register.success');
Route::get('/portal/api/suggestions', [PublicPortalController::class, 'suggestions'])->name('portal.suggestions');
Route::get('/portal/{talent}', [PublicPortalController::class, 'show'])->name('portal.show');
Route::post('/portal/{talent}/request-applicant', [ApplicantRequestController::class, 'storeFromPortal'])
    ->middleware(['auth', 'role:syarikat_pelaksana'])
    ->name('portal.request-applicant');

// ========================
// Auth Routes
// ========================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/logout', fn() => redirect()->route('login'));
Route::post('/language', [LoginController::class, 'setLanguage'])->name('language.set');

// Forgot Password (self-service)
Route::get('/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'showForm'])->name('password.forgot');
Route::post('/forgot-password/send-otp', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendOtp'])->name('password.send-otp');
Route::post('/forgot-password/verify-otp', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'verifyOtp'])->name('password.verify-otp');
Route::post('/forgot-password/reset', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'resetPassword'])->name('password.reset');

// ========================
// Admin Routes (auth required)
// ========================
Route::middleware(['auth', 'role:super_admin,pmo_admin,mindef_viewer,syarikat_pelaksana,rakan_kolaborasi'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePasswordForm'])->name('profile.change-password');
    Route::post('/profile/change-password/request-otp', [ProfileController::class, 'requestOtp'])->name('profile.request-otp');
    Route::post('/profile/change-password/verify-otp', [ProfileController::class, 'verifyOtp'])->name('profile.verify-otp');
    Route::post('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    // Talents
    Route::get('/talents', [TalentController::class, 'index'])->name('talents.index')->middleware('module:talents');
    Route::get('/talents/create', [TalentController::class, 'create'])->name('talents.create')->middleware('module:talents,write');
    Route::post('/talents', [TalentController::class, 'store'])->name('talents.store')->middleware('module:talents,write');
    Route::get('/talents/{talent}', [TalentController::class, 'show'])->name('talents.show')->middleware('module:talents');
    Route::get('/talents/{talent}/edit', [TalentController::class, 'edit'])->name('talents.edit')->middleware('module:talents,write');
    Route::put('/talents/{talent}', [TalentController::class, 'update'])->name('talents.update')->middleware('module:talents,write');
    Route::delete('/talents/{talent}', [TalentController::class, 'destroy'])->name('talents.destroy')->middleware('module:talents,write');
    Route::delete('/talent-documents/{document}', [TalentController::class, 'deleteDocument'])->name('talents.delete-document')->middleware('module:talents,write');

    // Syarikat Pelaksana
    Route::get('/syarikat-pelaksana', [SyarikatPelaksanaController::class, 'index'])->name('syarikat-pelaksana.index')->middleware('module:syarikat_pelaksana');
    Route::get('/syarikat-pelaksana/create', [SyarikatPelaksanaController::class, 'create'])->name('syarikat-pelaksana.create')->middleware('module:syarikat_pelaksana,write');
    Route::post('/syarikat-pelaksana', [SyarikatPelaksanaController::class, 'store'])->name('syarikat-pelaksana.store')->middleware('module:syarikat_pelaksana,write');
    Route::get('/syarikat-pelaksana/{syarikatPelaksana}', [SyarikatPelaksanaController::class, 'show'])->name('syarikat-pelaksana.show')->middleware('module:syarikat_pelaksana');
    Route::get('/syarikat-pelaksana/{syarikatPelaksana}/edit', [SyarikatPelaksanaController::class, 'edit'])->name('syarikat-pelaksana.edit')->middleware('module:syarikat_pelaksana,write');
    Route::put('/syarikat-pelaksana/{syarikatPelaksana}', [SyarikatPelaksanaController::class, 'update'])->name('syarikat-pelaksana.update')->middleware('module:syarikat_pelaksana,write');
    Route::delete('/syarikat-pelaksana/{syarikatPelaksana}', [SyarikatPelaksanaController::class, 'destroy'])->name('syarikat-pelaksana.destroy')->middleware('module:syarikat_pelaksana,write');

    // Syarikat Penempatan
    Route::get('/syarikat-penempatan', [SyarikatPenempatanController::class, 'index'])->name('syarikat-penempatan.index')->middleware('module:syarikat_penempatan');
    Route::get('/syarikat-penempatan/create', [SyarikatPenempatanController::class, 'create'])->name('syarikat-penempatan.create')->middleware('module:syarikat_penempatan,write');
    Route::post('/syarikat-penempatan', [SyarikatPenempatanController::class, 'store'])->name('syarikat-penempatan.store')->middleware('module:syarikat_penempatan,write');
    Route::get('/syarikat-penempatan/{syarikatPenempatan}', [SyarikatPenempatanController::class, 'show'])->name('syarikat-penempatan.show')->middleware('module:syarikat_penempatan');
    Route::get('/syarikat-penempatan/{syarikatPenempatan}/edit', [SyarikatPenempatanController::class, 'edit'])->name('syarikat-penempatan.edit')->middleware('module:syarikat_penempatan,write');
    Route::put('/syarikat-penempatan/{syarikatPenempatan}', [SyarikatPenempatanController::class, 'update'])->name('syarikat-penempatan.update')->middleware('module:syarikat_penempatan,write');
    Route::delete('/syarikat-penempatan/{syarikatPenempatan}', [SyarikatPenempatanController::class, 'destroy'])->name('syarikat-penempatan.destroy')->middleware('module:syarikat_penempatan,write');

    // Kehadiran & Prestasi
    Route::get('/kehadiran', [KehadiranPrestasiController::class, 'index'])->name('kehadiran.index')->middleware('module:kehadiran');
    Route::get('/kehadiran/create', [KehadiranPrestasiController::class, 'create'])->name('kehadiran.create')->middleware('module:kehadiran,write');
    Route::post('/kehadiran', [KehadiranPrestasiController::class, 'store'])->name('kehadiran.store')->middleware('module:kehadiran,write');
    Route::get('/kehadiran/{kehadiran}', [KehadiranPrestasiController::class, 'show'])->name('kehadiran.show')->middleware('module:kehadiran');
    Route::get('/kehadiran/{kehadiran}/edit', [KehadiranPrestasiController::class, 'edit'])->name('kehadiran.edit')->middleware('module:kehadiran,write');
    Route::put('/kehadiran/{kehadiran}', [KehadiranPrestasiController::class, 'update'])->name('kehadiran.update')->middleware('module:kehadiran,write');

    // Kewangan Elaun
    Route::get('/kewangan', [KewanganElaunController::class, 'index'])->name('kewangan.index')->middleware('module:kewangan');
    Route::get('/kewangan/create', [KewanganElaunController::class, 'create'])->name('kewangan.create')->middleware('module:kewangan,write');
    Route::post('/kewangan', [KewanganElaunController::class, 'store'])->name('kewangan.store')->middleware('module:kewangan,write');
    Route::get('/kewangan/{kewangan}', [KewanganElaunController::class, 'show'])->name('kewangan.show')->middleware('module:kewangan');
    Route::get('/kewangan/{kewangan}/edit', [KewanganElaunController::class, 'edit'])->name('kewangan.edit')->middleware('module:kewangan,write');
    Route::put('/kewangan/{kewangan}', [KewanganElaunController::class, 'update'])->name('kewangan.update')->middleware('module:kewangan,write');

    // Status Surat
    Route::get('/status-surat', [StatusSuratController::class, 'index'])->name('status-surat.index')->middleware('module:status_surat');
    Route::get('/status-surat/create', [StatusSuratController::class, 'create'])->name('status-surat.create')->middleware('module:status_surat,write');
    Route::post('/status-surat', [StatusSuratController::class, 'store'])->name('status-surat.store')->middleware('module:status_surat,write');
    Route::get('/status-surat/{statusSurat}', [StatusSuratController::class, 'show'])->name('status-surat.show')->middleware('module:status_surat');
    Route::get('/status-surat/{statusSurat}/edit', [StatusSuratController::class, 'edit'])->name('status-surat.edit')->middleware('module:status_surat,write');
    Route::put('/status-surat/{statusSurat}', [StatusSuratController::class, 'update'])->name('status-surat.update')->middleware('module:status_surat,write');
    Route::post('/status-surat/{statusSurat}/advance', [StatusSuratController::class, 'advanceStatus'])->name('status-surat.advance')->middleware('module:status_surat,write');
    Route::post('/status-surat/{statusSurat}/upload', [StatusSuratController::class, 'uploadAttachment'])->name('status-surat.upload')->middleware('module:status_surat,write');

    // Daily Logs (Admin view of protege logbook entries)
    Route::get('/daily-logs', [AdminDailyLogController::class, 'index'])->name('daily-logs.index')->middleware('module:daily_logs');
    Route::get('/daily-logs/{dailyLog}', [AdminDailyLogController::class, 'show'])->name('daily-logs.show')->middleware('module:daily_logs');
    Route::post('/daily-logs/{dailyLog}/review', [AdminDailyLogController::class, 'review'])->name('daily-logs.review')->middleware('module:daily_logs,write');

    // Logbook Upload
    Route::get('/logbook', [LogbookUploadController::class, 'index'])->name('logbook.index')->middleware('module:logbook');
    Route::get('/logbook/create', [LogbookUploadController::class, 'create'])->name('logbook.create')->middleware('module:logbook,write');
    Route::post('/logbook', [LogbookUploadController::class, 'store'])->name('logbook.store')->middleware('module:logbook,write');
    Route::get('/logbook/{logbook}', [LogbookUploadController::class, 'show'])->name('logbook.show')->middleware('module:logbook');
    Route::get('/logbook/{logbook}/edit', [LogbookUploadController::class, 'edit'])->name('logbook.edit')->middleware('module:logbook,write');
    Route::put('/logbook/{logbook}', [LogbookUploadController::class, 'update'])->name('logbook.update')->middleware('module:logbook,write');
    Route::post('/logbook/{logbook}/upload', [LogbookUploadController::class, 'uploadFile'])->name('logbook.upload')->middleware('module:logbook,write');

    // Isu & Risiko
    Route::get('/isu-risiko', [IsuRisikoController::class, 'index'])->name('isu-risiko.index')->middleware('module:isu_risiko');
    Route::get('/isu-risiko/create', [IsuRisikoController::class, 'create'])->name('isu-risiko.create')->middleware('module:isu_risiko,write');
    Route::post('/isu-risiko', [IsuRisikoController::class, 'store'])->name('isu-risiko.store')->middleware('module:isu_risiko,write');
    Route::get('/isu-risiko/{isuRisiko}', [IsuRisikoController::class, 'show'])->name('isu-risiko.show')->middleware('module:isu_risiko');
    Route::get('/isu-risiko/{isuRisiko}/edit', [IsuRisikoController::class, 'edit'])->name('isu-risiko.edit')->middleware('module:isu_risiko,write');
    Route::put('/isu-risiko/{isuRisiko}', [IsuRisikoController::class, 'update'])->name('isu-risiko.update')->middleware('module:isu_risiko,write');

    // Training Records
    Route::get('/training', [TrainingRecordController::class, 'index'])->name('training.index')->middleware('module:training');
    Route::get('/training/create', [TrainingRecordController::class, 'create'])->name('training.create')->middleware('module:training,write');
    Route::post('/training', [TrainingRecordController::class, 'store'])->name('training.store')->middleware('module:training,write');
    Route::get('/training/{training}', [TrainingRecordController::class, 'show'])->name('training.show')->middleware('module:training');
    Route::get('/training/{training}/edit', [TrainingRecordController::class, 'edit'])->name('training.edit')->middleware('module:training,write');
    Route::put('/training/{training}', [TrainingRecordController::class, 'update'])->name('training.update')->middleware('module:training,write');
    Route::post('/training/{training}/participants', [TrainingRecordController::class, 'addParticipant'])->name('training.add-participant')->middleware('module:training,write');
    Route::delete('/training/{training}/participants/{participant}', [TrainingRecordController::class, 'removeParticipant'])->name('training.remove-participant')->middleware('module:training,write');

    // KPI Dashboard
    Route::get('/kpi-dashboard', [KpiDashboardController::class, 'index'])->name('kpi-dashboard.index')->middleware('module:kpi');

    // Companies — REMOVED (replaced by Syarikat Pelaksana & Penempatan)

    // Placements
    Route::get('/placements', [PlacementController::class, 'index'])->name('placements.index')->middleware('module:placements');
    Route::get('/placements/create', [PlacementController::class, 'create'])->name('placements.create')->middleware('module:placements,write');
    Route::post('/placements', [PlacementController::class, 'store'])->name('placements.store')->middleware('module:placements,write');
    Route::get('/placements/{placement}', [PlacementController::class, 'show'])->name('placements.show')->middleware('module:placements');
    Route::get('/placements/{placement}/edit', [PlacementController::class, 'edit'])->name('placements.edit')->middleware('module:placements,write');
    Route::put('/placements/{placement}', [PlacementController::class, 'update'])->name('placements.update')->middleware('module:placements,write');

    // Manage Placement
    Route::get('/manage-placement', [\App\Http\Controllers\Admin\ManagePlacementController::class, 'index'])->name('manage-placement.index')->middleware('module:placements');
    Route::get('/manage-placement/{talent}', [\App\Http\Controllers\Admin\ManagePlacementController::class, 'show'])->name('manage-placement.show')->middleware('module:placements');
    Route::post('/manage-placement/{talent}/feedback', [\App\Http\Controllers\Admin\ManagePlacementController::class, 'storeFeedback'])->name('manage-placement.feedback');
    Route::post('/manage-placement/{talent}/assign', [\App\Http\Controllers\Admin\ManagePlacementController::class, 'assignPlacement'])->name('manage-placement.assign');
    Route::post('/manage-placement/{talent}/complete', [\App\Http\Controllers\Admin\ManagePlacementController::class, 'completePlacement'])->name('manage-placement.complete');
    Route::post('/manage-placement/{talent}/terminate', [\App\Http\Controllers\Admin\ManagePlacementController::class, 'earlyTermination'])->name('manage-placement.terminate');

    // Budget
    Route::get('/budget', [BudgetController::class, 'index'])->name('budget.index')->middleware('module:budget');
    Route::get('/budget/allocations', [BudgetController::class, 'allocations'])->name('budget.allocations')->middleware('module:budget');
    Route::post('/budget/allocations', [BudgetController::class, 'storeAllocation'])->name('budget.allocations.store')->middleware('module:budget,write');
    Route::delete('/budget/allocations/{allocation}', [BudgetController::class, 'destroyAllocation'])->name('budget.allocations.destroy')->middleware('module:budget,write');
    Route::get('/budget/transactions', [BudgetController::class, 'transactions'])->name('budget.transactions')->middleware('module:budget');
    Route::post('/budget/transactions', [BudgetController::class, 'storeTransaction'])->name('budget.transactions.store')->middleware('module:budget,write');
    Route::put('/budget/transactions/{transaction}', [BudgetController::class, 'updateTransaction'])->name('budget.transactions.update')->middleware('module:budget,write');
    Route::delete('/budget/transactions/{transaction}', [BudgetController::class, 'destroyTransaction'])->name('budget.transactions.destroy')->middleware('module:budget,write');

    // Feedback
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index')->middleware('module:feedback');
    Route::get('/feedback/create', [FeedbackController::class, 'create'])->name('feedback.create')->middleware('module:feedback,write');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store')->middleware('module:feedback,write');
    Route::get('/feedback/{feedback}', [FeedbackController::class, 'show'])->name('feedback.show')->middleware('module:feedback');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index')->middleware('module:reports');
    Route::get('/reports/talent', [ReportController::class, 'talentReport'])->name('reports.talent')->middleware('module:reports');
    Route::get('/reports/company', [ReportController::class, 'companyReport'])->name('reports.company')->middleware('module:reports');
    Route::get('/reports/budget', [ReportController::class, 'budgetReport'])->name('reports.budget')->middleware('module:reports');
    Route::get('/reports/placement', [ReportController::class, 'placementReport'])->name('reports.placement')->middleware('module:reports');

    // Report Exports (print-friendly HTML for PDF)
    Route::get('/reports/export/executive', [\App\Http\Controllers\Admin\ReportExportController::class, 'executive'])->name('reports.export.executive')->middleware('module:reports');
    Route::get('/reports/export/company', [\App\Http\Controllers\Admin\ReportExportController::class, 'company'])->name('reports.export.company')->middleware('module:reports');
    Route::get('/reports/export/participant', [\App\Http\Controllers\Admin\ReportExportController::class, 'participant'])->name('reports.export.participant')->middleware('module:reports');
    Route::get('/reports/export/training', [\App\Http\Controllers\Admin\ReportExportController::class, 'training'])->name('reports.export.training')->middleware('module:reports');

    // Applications (self-registration review)
    Route::get('/permohonan', [ApplicationController::class, 'index'])->name('applications.index')->middleware('module:applications');
    Route::get('/permohonan/{talent}', [ApplicationController::class, 'show'])->name('applications.show')->middleware('module:applications');
    Route::post('/permohonan/{talent}/approve', [ApplicationController::class, 'approve'])->name('applications.approve')->middleware('module:applications,write');
    Route::post('/permohonan/{talent}/reject', [ApplicationController::class, 'reject'])->name('applications.reject')->middleware('module:applications,write');

    // Applicant Requests
    Route::get('/applicant-requests', [ApplicantRequestController::class, 'index'])->name('applicant-requests.index')->middleware('role:super_admin,pmo_admin,mindef_viewer,syarikat_pelaksana');
    Route::post('/applicant-requests/{applicantRequest}/approve', [ApplicantRequestController::class, 'approve'])->name('applicant-requests.approve')->middleware('role:super_admin,pmo_admin');
    Route::post('/applicant-requests/{applicantRequest}/reject', [ApplicantRequestController::class, 'reject'])->name('applicant-requests.reject')->middleware('role:super_admin,pmo_admin');

    // System Guide (no module restriction — accessible to all authenticated admin roles)
    Route::get('/system-guide', [SystemGuideController::class, 'index'])->name('system-guide.index');
    Route::get('/system-guide/howto/{key}', [SystemGuideController::class, 'howto'])->name('system-guide.howto');
    Route::get('/system-guide/{module}', [SystemGuideController::class, 'show'])->name('system-guide.show');

    // Settings
    Route::get('/settings/users', [SettingsController::class, 'users'])->name('settings.users')->middleware('module:settings');
    Route::get('/settings/users/create', [SettingsController::class, 'createUser'])->name('settings.users.create')->middleware('module:settings,write');
    Route::post('/settings/users', [SettingsController::class, 'storeUser'])->name('settings.users.store')->middleware('module:settings,write');
    Route::get('/settings/users/{user}/edit', [SettingsController::class, 'editUser'])->name('settings.users.edit')->middleware('module:settings,write');
    Route::put('/settings/users/{user}', [SettingsController::class, 'updateUser'])->name('settings.users.update')->middleware('module:settings,write');
    Route::delete('/settings/users/{user}', [SettingsController::class, 'destroyUser'])->name('settings.users.destroy')->middleware('module:settings,write');
    Route::post('/settings/users/{user}/reset-password', [SettingsController::class, 'resetPassword'])->name('settings.users.reset-password')->middleware('module:settings,write');

    Route::get('/settings/roles', [SettingsController::class, 'roles'])->name('settings.roles')->middleware('module:settings');

    Route::get('/settings/batches', [SettingsController::class, 'batches'])->name('settings.batches')->middleware('module:settings');
    Route::post('/settings/batches', [SettingsController::class, 'storeBatch'])->name('settings.batches.store')->middleware('module:settings,write');
    Route::put('/settings/batches/{batch}', [SettingsController::class, 'updateBatch'])->name('settings.batches.update')->middleware('module:settings,write');
    Route::delete('/settings/batches/{batch}', [SettingsController::class, 'destroyBatch'])->name('settings.batches.destroy')->middleware('module:settings,write');

    // Admin utilities (super_admin only)
    Route::post('/settings/clear-cache', function () {
        if (auth()->user()?->role?->role_name !== 'super_admin') {
            abort(403);
        }
        \Illuminate\Support\Facades\Artisan::call('protege:clear-cache');
        return back()->with('success', 'All caches cleared successfully.');
    })->name('settings.clear-cache')->middleware('module:settings');
});

// ========================
// Talent Portal Routes
// ========================
Route::middleware(['auth', 'role:talent'])->prefix('talent')->name('talent.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [TalentDashboardController::class, 'index'])->name('dashboard');

    // Daily Logs
    Route::get('/daily-logs', [TalentDailyLogController::class, 'index'])->name('daily-logs.index');
    Route::get('/daily-logs/create', [TalentDailyLogController::class, 'create'])->name('daily-logs.create');
    Route::post('/daily-logs', [TalentDailyLogController::class, 'store'])->name('daily-logs.store');
    Route::get('/daily-logs/{dailyLog}', [TalentDailyLogController::class, 'show'])->name('daily-logs.show');
    Route::get('/daily-logs/{dailyLog}/edit', [TalentDailyLogController::class, 'edit'])->name('daily-logs.edit');
    Route::put('/daily-logs/{dailyLog}', [TalentDailyLogController::class, 'update'])->name('daily-logs.update');
    Route::delete('/daily-logs/{dailyLog}', [TalentDailyLogController::class, 'destroy'])->name('daily-logs.destroy');

    // Placement Info (company + reporting manager)
    Route::get('/placement', [TalentPlacementInfoController::class, 'index'])->name('placement.index');

    // Allowance / Payment Status
    Route::get('/allowance', [TalentAllowanceController::class, 'index'])->name('allowance.index');

    // Training
    Route::get('/training', [\App\Http\Controllers\Talent\TrainingController::class, 'index'])->name('training.index');
    Route::post('/training/{training}/join', [\App\Http\Controllers\Talent\TrainingController::class, 'join'])->name('training.join');

    // Logbook
    Route::get('/logbook', [\App\Http\Controllers\Talent\LogbookController::class, 'index'])->name('logbook.index');
    Route::post('/logbook/upload', [\App\Http\Controllers\Talent\LogbookController::class, 'upload'])->name('logbook.upload');

    // Profile
    Route::get('/profile', [TalentProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [TalentProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [TalentProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [TalentProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::get('/profile/change-password', [TalentProfileController::class, 'changePasswordForm'])->name('profile.change-password');
    Route::post('/profile/change-password/request-otp', [TalentProfileController::class, 'requestOtp'])->name('profile.request-otp');
    Route::post('/profile/change-password/verify-otp', [TalentProfileController::class, 'verifyOtp'])->name('profile.verify-otp');
    Route::post('/profile/change-password', [TalentProfileController::class, 'updatePassword'])->name('profile.update-password');
});

// ========================
// Company Rep Portal Routes
// ========================
Route::middleware(['auth', 'role:company_rep'])->prefix('company')->name('company.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [CompanyDashboardController::class, 'index'])->name('dashboard');

    // Placements (their company's only)
    Route::get('/placements', [CompanyPlacementController::class, 'index'])->name('placements.index');
    Route::get('/placements/{placement}', [CompanyPlacementController::class, 'show'])->name('placements.show');

    // Feedback (their company's placements only)
    Route::get('/feedback', [CompanyFeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/feedback/create', [CompanyFeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [CompanyFeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/feedback/{feedback}', [CompanyFeedbackController::class, 'show'])->name('feedback.show');

    // Finance (allocation + disbursement for their company)
    Route::get('/finance', [CompanyFinanceController::class, 'index'])->name('finance.index');
});
