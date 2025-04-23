<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailListController;
use App\Http\Controllers\EmailController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\RolesPermissionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});


Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {

    Route::get('/admin/email/send', [EmailController::class, 'form'])->name('email.form');
    Route::post('/admin/email/send', [EmailController::class, 'send'])->name('email.send');
    Route::get('/admin/sent-email-list', [EmailController::class, 'list'])->name('sent.email.list');
    Route::delete('/admin/sent-email-list/bulk-delete', [EmailController::class, 'bulkDelete'])->name('sent.email.bulkDelete');
    
    Route::get('/admin/user-email-list', [EmailListController::class, 'index'])->name('email.list');
    Route::post('/admin/email-list/upload', [EmailListController::class, 'upload'])->name('email.upload');
    Route::delete('/admin/emails/bulk-delete', [EmailListController::class, 'bulkDelete'])->name('email.bulkDelete');


    Route::get('/users', [RolesPermissionController::class, 'all_users_roles_permissions'])->name('all-users');
    Route::get('/edit-user-role-permission/{id}', [RolesPermissionController::class, 'edit_user_role_permissions'])->name('edit-user');
    Route::post('/set-role-permission-to-a-user/{id}', [RolesPermissionController::class, 'set_role_permission_to_a_user'])->name('set-role-permission');
    Route::get('/remove-user-role-permissions/{id}', [RolesPermissionController::class, 'remove_user_role_permissions'])->name('remove-user-role-permissions');
    Route::get('/delete-user/{id}', [RolesPermissionController::class, 'delete_user_role_permissions'])->name('delete-user');
});

require __DIR__.'/auth.php';
