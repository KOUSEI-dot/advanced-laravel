<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRequestController;
use App\Http\Controllers\StampCorrectionRequestController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminAttendanceRequestController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\Auth\CustomLoginController;

/*
|--------------------------------------------------------------------------
| 管理者ログインページ（ログイン前でもアクセス可能）
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login.form');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');

/*
|--------------------------------------------------------------------------
| 一般ユーザー用ルート（auth ミドルウェア）
|--------------------------------------------------------------------------
*/
Route::post('/login', [CustomLoginController::class, 'login'])->name('login');

Route::middleware('auth')->group(function () {

    // 勤怠管理
    Route::get('/attendance', [WorkController::class, 'attendance'])->name('attendance');
    Route::post('/start-work', [WorkController::class, 'startWork'])->name('start.work');
    Route::post('/start-break', [WorkController::class, 'startBreak'])->name('start.break');
    Route::post('/end-break', [WorkController::class, 'endBreak'])->name('end.break');
    Route::post('/end-work', [WorkController::class, 'endWork'])->name('end.work');

    // 勤怠一覧・詳細
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/{id}', [AttendanceController::class, 'detail'])->name('attendance.detail');

    // 修正申請
    Route::post('/attendance/request', [AttendanceRequestController::class, 'store'])->name('attendance.request');
    Route::get('/attendance/request/{id}', [AttendanceRequestController::class, 'show'])->name('user.attendance.request.detail');

    // 修正申請一覧（ユーザー用）
    Route::get('/stamp_correction_request/list', [StampCorrectionRequestController::class, 'index'])->name('stamp_correction_request.list');

    // ログアウト
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/login');
    })->name('logout');
});

/*
|--------------------------------------------------------------------------
| 管理者専用ルート（auth + admin ミドルウェア）
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {

    // 管理者ログアウト
    Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // 勤怠管理（管理者用）
    Route::get('/admin/attendance', [AdminAttendanceController::class, 'index'])->name('admin.attendance');
    Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'list'])->name('admin.attendance.list');
    Route::get('/admin/attendance/{id}', [AdminAttendanceController::class, 'detail'])->name('admin.attendance.detail');
    Route::put('/admin/attendance/{id}', [AdminAttendanceController::class, 'update'])->name('admin.attendance.update');

    Route::post('/admin/attendance/request', [AdminAttendanceRequestController::class, 'requestAttendanceFix'])->name('admin.attendance.request');
    Route::get('/admin/requests/{id}', [AdminAttendanceRequestController::class, 'show'])->name('attendance.request.detail');

    // スタッフ管理
    Route::get('/admin/staff/list', [AdminStaffController::class, 'index'])->name('admin.staff.list');
    Route::get('/admin/attendance/staff/{id}', [AdminStaffController::class, 'show'])->name('admin.attendance.staff.detail');
    Route::get('/admin/attendance/export/{staff_id}', [AdminAttendanceController::class, 'exportCsv'])->name('admin.attendance.export');

    // 修正申請一覧・詳細（管理者用）
    Route::get('/admin/stamp_correction_request/list', [AdminAttendanceRequestController::class, 'index'])->name('admin.requests.list');
    Route::get('/admin/requests/detail/{id}', [AdminAttendanceRequestController::class, 'show'])->name('admin.attendance.request.detail');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request}', [AdminAttendanceRequestController::class, 'approve'])->name('attendance.approve');
});
