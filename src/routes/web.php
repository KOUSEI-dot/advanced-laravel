<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\WorkController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRequestController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminAttendanceRequestController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\Auth\CustomLoginController;

/*
|--------------------------------------------------------------------------
| 認証メール関連（Fortify使用）
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect('/attendance');
});

// 認証メール確認ページ
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/attendance');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証リンクを再送しました！');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


/*
|--------------------------------------------------------------------------
| 管理者ログイン（一般アクセス可）
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])
    ->name('admin.login.form');

Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->name('admin.login');


/*
|--------------------------------------------------------------------------
| 一般ユーザー（auth + verified）
|--------------------------------------------------------------------------
*/
Route::post('/login', [CustomLoginController::class, 'login'])->name('login');

Route::middleware(['auth', 'verified'])->group(function () {

    /* ▼▼ 勤怠トップ / 出勤・退勤 ▼▼ */
    Route::get('/attendance', [WorkController::class, 'attendance'])
        ->name('attendance');

    Route::post('/start-work', [WorkController::class, 'startWork'])
        ->name('start.work');

    Route::post('/end-work', [WorkController::class, 'endWork'])
        ->name('end.work');

    /* ▼▼ 休憩 ▼▼ */
    Route::post('/start-break', [AttendanceController::class, 'startBreak'])
        ->name('start.break');

    Route::post('/end-break', [AttendanceController::class, 'endBreak'])
        ->name('end.break');

    /* ▼▼ 勤怠一覧・詳細 ▼▼ */
    Route::get('/attendance/list', [AttendanceController::class, 'list'])
        ->name('attendance.list');

    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])
        ->name('attendance.detail');

    /* ▼▼ 修正申請（ユーザー側） ▼▼ */
    Route::post('/attendance/request', [AttendanceRequestController::class, 'store'])
        ->name('attendance.request');

    // 新仕様・申請一覧（旧 /stamp_correction_request/list を廃止）
    Route::get('/attendance/request/list', [AttendanceRequestController::class, 'userList'])
        ->name('attendance.request.userlist');

    // 新仕様・申請詳細
    Route::get('/attendance/request/detail/{id}', [AttendanceRequestController::class, 'userShow'])
        ->name('attendance.request.usershow');

    /* ▼▼ ログアウト ▼▼ */
    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/login');
    })->name('logout');
});


/*
|--------------------------------------------------------------------------
| 管理者専用（auth + admin）
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    // ログアウト
    Route::post('/logout', [AdminAuthController::class, 'logout'])
        ->name('admin.logout');

    /* ▼▼ 管理者：勤怠 ▼▼ */
    Route::get('/attendance', [AdminAttendanceController::class, 'index'])
        ->name('admin.attendance');

    Route::get('/attendance/list', [AdminAttendanceController::class, 'list'])
        ->name('admin.attendance.list');

    Route::get('/attendance/detail/{id}', [AdminAttendanceController::class, 'detail'])
        ->name('admin.attendance.detail');

    Route::put('/attendance/update/{id}', [AdminAttendanceController::class, 'update'])
        ->name('admin.attendance.update');

    /* ▼▼ 管理者：修正申請 ▼▼ */
    Route::get('/requests', [AdminAttendanceRequestController::class, 'index'])
        ->name('admin.requests.list');

    Route::get('/requests/detail/{id}', [AdminAttendanceRequestController::class, 'show'])
        ->name('admin.requests.detail');

    Route::post('/requests/approve/{id}', [AdminAttendanceRequestController::class, 'approve'])
        ->name('admin.requests.approve');

    Route::post('/requests/reject/{id}', [AdminAttendanceRequestController::class, 'reject'])
        ->name('admin.requests.reject');

    /* ▼▼ スタッフ情報 ▼▼ */
    Route::get('/staff/list', [AdminStaffController::class, 'index'])
        ->name('admin.staff.list');

    Route::get('/attendance/staff/{id}', [AdminStaffController::class, 'show'])
        ->name('admin.attendance.staff.detail');

    Route::get('/attendance/export/{staff_id}', [AdminAttendanceController::class, 'exportCsv'])
        ->name('admin.attendance.export');
});
