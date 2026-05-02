<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Mobile\AuthController;
use App\Http\Controllers\Api\Mobile\PortalController;
use App\Http\Controllers\Api\Mobile\TalentController;
use App\Http\Controllers\Api\Mobile\CompanyController;
use App\Http\Controllers\Api\Mobile\AdminController;
use App\Http\Controllers\Api\Mobile\NotificationController;
use App\Http\Controllers\Api\Mobile\PelaksanaController;
use App\Http\Controllers\Api\Mobile\ApplicantRequestController;

Route::prefix('mobile')->middleware('mobile.locale')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/public/talent/register', [TalentController::class, 'register']);
    Route::get('/portal/talents', [PortalController::class, 'index']);
    Route::get('/portal/talents/{talent}', [PortalController::class, 'show']);

    // Forgot password (public, no auth required)
    Route::post('/auth/forgot-password/send-otp', [AuthController::class, 'forgotPasswordSendOtp']);
    Route::post('/auth/forgot-password/verify-otp', [AuthController::class, 'forgotPasswordVerifyOtp']);
    Route::post('/auth/forgot-password/reset', [AuthController::class, 'forgotPasswordReset']);

    Route::middleware('mobile.auth')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::put('/auth/language', [AuthController::class, 'updateLanguage']);
        Route::post('/auth/change-password', [AuthController::class, 'changePassword']);

        Route::post('/device-token', [NotificationController::class, 'storeDeviceToken']);
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::post('/notifications/read', [NotificationController::class, 'markRead']);

        Route::prefix('talent')->middleware('mobile.role:talent')->group(function () {
            Route::get('/profile', [TalentController::class, 'profile']);
            Route::put('/profile', [TalentController::class, 'updateProfile']);
            Route::get('/application-status', [TalentController::class, 'applicationStatus']);
            Route::get('/documents', [TalentController::class, 'documents']);
            Route::post('/documents', [TalentController::class, 'storeDocument']);
            Route::delete('/documents/{document}', [TalentController::class, 'deleteDocument']);
            Route::get('/placements/current', [TalentController::class, 'currentPlacement']);
            Route::get('/placements/history', [TalentController::class, 'placementHistory']);
            Route::get('/feedback', [TalentController::class, 'feedback']);
            Route::get('/daily-logs', [TalentController::class, 'dailyLogs']);
            Route::post('/daily-logs', [TalentController::class, 'storeDailyLog']);
            Route::get('/daily-logs/{dailyLog}', [TalentController::class, 'showDailyLog']);
            Route::get('/allowance', [TalentController::class, 'allowance']);
            Route::get('/attendance/summary', [TalentController::class, 'attendanceSummary']);
            Route::get('/letters', [TalentController::class, 'letterStatus']);
            Route::get('/training', [TalentController::class, 'training']);
            Route::get('/issues-alerts', [TalentController::class, 'issuesAndAlerts']);
        });

        Route::prefix('company')->middleware('mobile.role:company_rep,rakan_kolaborasi')->group(function () {
            Route::get('/dashboard', [CompanyController::class, 'dashboard']);
            Route::get('/placements', [CompanyController::class, 'placements']);
            Route::get('/placements/{placement}', [CompanyController::class, 'showPlacement']);
            Route::get('/feedback/pending', [CompanyController::class, 'pendingFeedback']);
            Route::post('/feedback', [CompanyController::class, 'storeFeedback']);
            Route::get('/attendance', [CompanyController::class, 'attendance']);
            Route::get('/logbooks', [CompanyController::class, 'logbooks']);
            Route::put('/logbooks/{id}/review', [CompanyController::class, 'reviewLogbook']);
            Route::get('/training', [CompanyController::class, 'training']);
            Route::get('/available-talents', [PortalController::class, 'availableForCompany']);
            Route::get('/implementation-companies', [ApplicantRequestController::class, 'implementationCompanyOptions']);
            Route::get('/applicant-requests', [ApplicantRequestController::class, 'indexForCompany']);
            Route::post('/applicant-requests', [ApplicantRequestController::class, 'storeForCompany']);
        });

        // Pelaksana (Implementing Company)
        Route::prefix('pelaksana')->middleware('mobile.role:syarikat_pelaksana')->group(function () {
            Route::get('/dashboard', [PelaksanaController::class, 'dashboard']);
            Route::get('/graduates', [PelaksanaController::class, 'graduates']);
            Route::get('/manage-placement', [PelaksanaController::class, 'managePlacement']);
            Route::get('/logbook', [PelaksanaController::class, 'logbook']);
            Route::get('/kewangan', [PelaksanaController::class, 'kewangan']);
            Route::get('/status-surat', [PelaksanaController::class, 'statusSurat']);
            Route::get('/issues', [PelaksanaController::class, 'issues']);
            Route::get('/applicant-requests', [ApplicantRequestController::class, 'indexForPelaksana']);
            Route::post('/applicant-requests/{applicantRequest}/accept', [ApplicantRequestController::class, 'acceptForPelaksana']);
            Route::post('/applicant-requests/{applicantRequest}/reject', [ApplicantRequestController::class, 'rejectForPelaksana']);
            Route::get('/profile', [PelaksanaController::class, 'profile']);
        });

        Route::prefix('admin')->middleware('mobile.role:super_admin,programme_admin,finance_admin,management_viewer,pmo_admin,mindef_viewer')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard']);
            Route::get('/profile', [AdminController::class, 'profile']);
            Route::put('/profile', [AdminController::class, 'updateProfile']);
            Route::get('/applications', [AdminController::class, 'applications']);
            Route::get('/applications/{talent}', [AdminController::class, 'showApplication']);
            Route::post('/applications/{talent}/approve', [AdminController::class, 'approveApplication']);
            Route::post('/applications/{talent}/reject', [AdminController::class, 'rejectApplication']);
            Route::get('/placements', [AdminController::class, 'placements']);
            Route::get('/graduates', [AdminController::class, 'graduates']);
            Route::get('/graduates/{talent}', [AdminController::class, 'showGraduate']);
            Route::get('/issues', [AdminController::class, 'issues']);
            Route::get('/implementing-companies', [AdminController::class, 'implementingCompanies']);
            Route::get('/implementing-companies/{syarikatPelaksana}', [AdminController::class, 'showImplementingCompany']);
            Route::get('/placement-companies', [AdminController::class, 'placementCompanies']);
            Route::get('/placement-companies/{syarikatPenempatan}', [AdminController::class, 'showPlacementCompany']);
            Route::get('/manage-placement', [AdminController::class, 'managePlacements']);
            Route::get('/manage-placement/{talent}', [AdminController::class, 'showManagePlacement']);
            Route::post('/manage-placement/{talent}/assign', [AdminController::class, 'assignManagePlacement']);
            Route::post('/manage-placement/{talent}/complete', [AdminController::class, 'completeManagePlacement']);
            Route::post('/manage-placement/{talent}/terminate', [AdminController::class, 'terminateManagePlacement']);
            Route::post('/manage-placement/{talent}/feedback', [AdminController::class, 'storeManagePlacementFeedback']);
            Route::get('/attendance', [AdminController::class, 'attendance']);
            Route::get('/daily-logs', [AdminController::class, 'dailyLogs']);
            Route::get('/logbook', [AdminController::class, 'logbooks']);
            Route::get('/training', [AdminController::class, 'training']);
            Route::get('/letter-status', [AdminController::class, 'letterStatus']);
            Route::get('/finance', [AdminController::class, 'finance']);
            Route::get('/budget', [AdminController::class, 'budget']);
            Route::get('/budget/transactions', [AdminController::class, 'budgetTransactions']);
            Route::get('/budget/allocations', [AdminController::class, 'budgetAllocations']);
            Route::post('/budget/allocations', [AdminController::class, 'storeBudgetAllocation']);
            Route::delete('/budget/allocations/{allocation}', [AdminController::class, 'destroyBudgetAllocation']);
            Route::get('/kpi', [AdminController::class, 'kpi']);
            Route::get('/reports', [AdminController::class, 'reports']);
            Route::get('/reports/{type}/pdf', [AdminController::class, 'reportPdf']);
            Route::get('/feedback', [AdminController::class, 'feedback']);
            Route::get('/applicant-requests', [ApplicantRequestController::class, 'indexForAdmin']);
            Route::post('/applicant-requests/{applicantRequest}/approve', [ApplicantRequestController::class, 'approve']);
            Route::post('/applicant-requests/{applicantRequest}/reject', [ApplicantRequestController::class, 'reject']);
            Route::get('/settings', [AdminController::class, 'settings']);
            Route::get('/graduate-remarks', [AdminController::class, 'graduateRemarks']);
            Route::get('/surveys', [AdminController::class, 'surveys']);
            Route::post('/settings/clear-cache', [AdminController::class, 'clearCache']);
        });

        // MINDEF masked read-only dashboard
        Route::prefix('mindef')->middleware('mobile.role:mindef_viewer')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'mindefDashboard']);
        });
    });
});
