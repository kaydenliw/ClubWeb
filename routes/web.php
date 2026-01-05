<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Super Admin Routes
Route::prefix('admin')->middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('organizations', App\Http\Controllers\Admin\OrganizationController::class)->names('admin.organizations');
    Route::get('organizations/export/csv', [App\Http\Controllers\Admin\OrganizationController::class, 'exportCsv'])->name('admin.organizations.export.csv');
    Route::get('organizations/export/excel', [App\Http\Controllers\Admin\OrganizationController::class, 'exportExcel'])->name('admin.organizations.export.excel');
    Route::get('organizations/export/pdf', [App\Http\Controllers\Admin\OrganizationController::class, 'exportPdf'])->name('admin.organizations.export.pdf');
    Route::get('members', [App\Http\Controllers\Admin\MemberController::class, 'index'])->name('admin.members.index');
    Route::get('members/export/csv', [App\Http\Controllers\Admin\MemberController::class, 'exportCsv'])->name('admin.members.export.csv');
    Route::get('members/export/excel', [App\Http\Controllers\Admin\MemberController::class, 'exportExcel'])->name('admin.members.export.excel');
    Route::get('members/export/pdf', [App\Http\Controllers\Admin\MemberController::class, 'exportPdf'])->name('admin.members.export.pdf');
    Route::get('members/{member}', [App\Http\Controllers\Admin\MemberController::class, 'show'])->name('admin.members.show');
    Route::get('members/{member}/edit', [App\Http\Controllers\Admin\MemberController::class, 'edit'])->name('admin.members.edit');
    Route::put('members/{member}', [App\Http\Controllers\Admin\MemberController::class, 'update'])->name('admin.members.update');
    Route::delete('members/{member}', [App\Http\Controllers\Admin\MemberController::class, 'destroy'])->name('admin.members.destroy');
    Route::get('transactions', [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('admin.transactions.index');
    Route::get('transactions/export/csv', [App\Http\Controllers\Admin\TransactionController::class, 'exportCsv'])->name('admin.transactions.export.csv');
    Route::get('transactions/export/excel', [App\Http\Controllers\Admin\TransactionController::class, 'exportExcel'])->name('admin.transactions.export.excel');
    Route::get('transactions/export/pdf', [App\Http\Controllers\Admin\TransactionController::class, 'exportPdf'])->name('admin.transactions.export.pdf');
    Route::post('transactions/sync', [App\Http\Controllers\Admin\TransactionController::class, 'syncToAccounting'])->name('admin.transactions.sync');
    Route::post('transactions/settlement-preview', [App\Http\Controllers\Admin\TransactionController::class, 'getSettlementPreview'])->name('admin.transactions.settlement-preview');
    Route::post('transactions/mark-as-settled', [App\Http\Controllers\Admin\TransactionController::class, 'markAsSettled'])->name('admin.transactions.mark-as-settled');
    Route::get('transactions/{transaction}', [App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('admin.transactions.show');
    Route::delete('transactions/{transaction}', [App\Http\Controllers\Admin\TransactionController::class, 'destroy'])->name('admin.transactions.destroy');
    Route::get('settlements', [App\Http\Controllers\Admin\SettlementController::class, 'index'])->name('admin.settlements.index');
    Route::get('settlements/create', [App\Http\Controllers\Admin\SettlementController::class, 'create'])->name('admin.settlements.create');
    Route::post('settlements', [App\Http\Controllers\Admin\SettlementController::class, 'store'])->name('admin.settlements.store');
    Route::get('settlements/{settlement}', [App\Http\Controllers\Admin\SettlementController::class, 'show'])->name('admin.settlements.show');
    Route::get('settlements/{settlement}/edit', [App\Http\Controllers\Admin\SettlementController::class, 'edit'])->name('admin.settlements.edit');
    Route::put('settlements/{settlement}', [App\Http\Controllers\Admin\SettlementController::class, 'update'])->name('admin.settlements.update');
    Route::delete('settlements/{settlement}', [App\Http\Controllers\Admin\SettlementController::class, 'destroy'])->name('admin.settlements.destroy');
    Route::put('settlements/{settlement}/approve', [App\Http\Controllers\Admin\SettlementController::class, 'approve'])->name('admin.settlements.approve');
    Route::put('settlements/{settlement}/reject', [App\Http\Controllers\Admin\SettlementController::class, 'reject'])->name('admin.settlements.reject');
    Route::post('settlements/bulk-approve', [App\Http\Controllers\Admin\SettlementController::class, 'bulkApprove'])->name('admin.settlements.bulk-approve');
    Route::post('settlements/bulk-reject', [App\Http\Controllers\Admin\SettlementController::class, 'bulkReject'])->name('admin.settlements.bulk-reject');
    Route::post('maintenance/toggle', [App\Http\Controllers\Admin\MaintenanceController::class, 'toggle'])->name('admin.maintenance.toggle');

    // Charge Management & Approval Routes
    Route::get('charges', [App\Http\Controllers\Admin\ChargeController::class, 'index'])->name('admin.charges.index');
    Route::get('charges/{charge}', [App\Http\Controllers\Admin\ChargeController::class, 'show'])->name('admin.charges.show');
    Route::put('charges/{charge}/approve', [App\Http\Controllers\Admin\ChargeController::class, 'approve'])->name('admin.charges.approve');
    Route::put('charges/{charge}/reject', [App\Http\Controllers\Admin\ChargeController::class, 'reject'])->name('admin.charges.reject');
    Route::put('charges/{charge}/update-fee', [App\Http\Controllers\Admin\ChargeController::class, 'updateFee'])->name('admin.charges.update-fee');
    Route::post('charges/bulk-approve', [App\Http\Controllers\Admin\ChargeController::class, 'bulkApprove'])->name('admin.charges.bulk-approve');
    Route::post('charges/bulk-reject', [App\Http\Controllers\Admin\ChargeController::class, 'bulkReject'])->name('admin.charges.bulk-reject');

    // Announcement Approval Routes
    Route::get('announcements', [App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->name('admin.announcements.index');
    Route::get('announcements/{announcement}', [App\Http\Controllers\Admin\AnnouncementController::class, 'show'])->name('admin.announcements.show');
    Route::put('announcements/{announcement}/approve', [App\Http\Controllers\Admin\AnnouncementController::class, 'approve'])->name('admin.announcements.approve');
    Route::put('announcements/{announcement}/reject', [App\Http\Controllers\Admin\AnnouncementController::class, 'reject'])->name('admin.announcements.reject');
    Route::post('announcements/bulk-approve', [App\Http\Controllers\Admin\AnnouncementController::class, 'bulkApprove'])->name('admin.announcements.bulk-approve');
    Route::post('announcements/bulk-reject', [App\Http\Controllers\Admin\AnnouncementController::class, 'bulkReject'])->name('admin.announcements.bulk-reject');

    // Bank Details Approval Routes
    Route::get('bank-details', [App\Http\Controllers\Admin\OrganizationController::class, 'pendingBankDetails'])->name('admin.bank-details.index');
    Route::put('organizations/{organization}/bank-details/approve', [App\Http\Controllers\Admin\OrganizationController::class, 'approveBankDetails'])->name('admin.bank-details.approve');
    Route::put('organizations/{organization}/bank-details/reject', [App\Http\Controllers\Admin\OrganizationController::class, 'rejectBankDetails'])->name('admin.bank-details.reject');

    // Support Ticket Routes
    Route::get('tickets/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'show'])->name('admin.tickets.show');
    Route::put('tickets/{ticket}/status', [App\Http\Controllers\Admin\TicketController::class, 'updateStatus'])->name('admin.tickets.updateStatus');
    Route::post('tickets/{ticket}/reply', [App\Http\Controllers\Admin\TicketController::class, 'reply'])->name('admin.tickets.reply');

    // Activity Logs
    Route::get('activity-logs', [App\Http\Controllers\Admin\ActivityLogsController::class, 'index'])->name('admin.activity-logs.index');
});

// Organization Admin Routes
Route::prefix('organization')->middleware(['auth', 'role:organization_admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Organization\DashboardController::class, 'index'])->name('organization.dashboard');
    Route::resource('members', App\Http\Controllers\Organization\MemberController::class)->names('organization.members');
    Route::post('members/bulk-update-status', [App\Http\Controllers\Organization\MemberController::class, 'bulkUpdateStatus'])->name('organization.members.bulk-update-status');
    Route::post('members/bulk-delete', [App\Http\Controllers\Organization\MemberController::class, 'bulkDelete'])->name('organization.members.bulk-delete');
    Route::get('members/export/csv', [App\Http\Controllers\Organization\MemberController::class, 'exportCsv'])->name('organization.members.export');
    Route::get('members/export/excel', [App\Http\Controllers\Organization\MemberController::class, 'exportExcel'])->name('organization.members.export.excel');
    Route::get('members/export/pdf', [App\Http\Controllers\Organization\MemberController::class, 'exportPdf'])->name('organization.members.export.pdf');
    Route::get('members/{member}/transactions/create', [App\Http\Controllers\Organization\MemberController::class, 'createTransaction'])->name('organization.members.transactions.create');
    Route::post('members/{member}/transactions', [App\Http\Controllers\Organization\MemberController::class, 'storeTransaction'])->name('organization.members.transactions.store');
    Route::resource('charges', App\Http\Controllers\Organization\ChargeController::class)->names('organization.charges');
    Route::post('charges/{charge}/submit', [App\Http\Controllers\Organization\ChargeController::class, 'submitForApproval'])->name('organization.charges.submit');
    Route::post('charges/bulk-delete', [App\Http\Controllers\Organization\ChargeController::class, 'bulkDelete'])->name('organization.charges.bulk-delete');
    Route::post('charges/bulk-update-status', [App\Http\Controllers\Organization\ChargeController::class, 'bulkUpdateStatus'])->name('organization.charges.bulk-update-status');
    Route::get('transactions', [App\Http\Controllers\Organization\TransactionController::class, 'index'])->name('organization.transactions.index');
    Route::get('transactions/export/csv', [App\Http\Controllers\Organization\TransactionController::class, 'exportCsv'])->name('organization.transactions.export.csv');
    Route::get('transactions/export/excel', [App\Http\Controllers\Organization\TransactionController::class, 'exportExcel'])->name('organization.transactions.export.excel');
    Route::get('transactions/export/pdf', [App\Http\Controllers\Organization\TransactionController::class, 'exportPdf'])->name('organization.transactions.export.pdf');
    Route::get('transactions/{transaction}', [App\Http\Controllers\Organization\TransactionController::class, 'show'])->name('organization.transactions.show');
    Route::get('settlements', [App\Http\Controllers\Organization\SettlementController::class, 'index'])->name('organization.settlements.index');
    Route::get('settlements/{settlement}', [App\Http\Controllers\Organization\SettlementController::class, 'show'])->name('organization.settlements.show');
    Route::get('settlements/{settlement}/receipt', [App\Http\Controllers\Organization\SettlementController::class, 'downloadReceipt'])->name('organization.settlements.receipt');
    Route::get('settlements/bank/edit', [App\Http\Controllers\Organization\SettlementController::class, 'editBankDetails'])->name('organization.settlements.edit-bank');
    Route::put('settlements/bank/update', [App\Http\Controllers\Organization\SettlementController::class, 'updateBankDetails'])->name('organization.settlements.update-bank');
    Route::resource('announcements', App\Http\Controllers\Organization\AnnouncementController::class)->names('organization.announcements');
    Route::post('announcements/{announcement}/submit', [App\Http\Controllers\Organization\AnnouncementController::class, 'submitForApproval'])->name('organization.announcements.submit');
    Route::post('announcements/bulk-delete', [App\Http\Controllers\Organization\AnnouncementController::class, 'bulkDelete'])->name('organization.announcements.bulk-delete');
    Route::post('announcements/bulk-update-status', [App\Http\Controllers\Organization\AnnouncementController::class, 'bulkUpdateStatus'])->name('organization.announcements.bulk-update-status');
    Route::resource('faqs', App\Http\Controllers\Organization\FaqController::class)->except(['show'])->names('organization.faqs');
    Route::post('faqs/bulk-delete', [App\Http\Controllers\Organization\FaqController::class, 'bulkDelete'])->name('organization.faqs.bulk-delete');
    Route::post('faqs/reorder', [App\Http\Controllers\Organization\FaqController::class, 'reorder'])->name('organization.faqs.reorder');
    Route::get('tickets', [App\Http\Controllers\Organization\ContactTicketController::class, 'index'])->name('organization.tickets.index');
    Route::get('tickets/{ticket}', [App\Http\Controllers\Organization\ContactTicketController::class, 'show'])->name('organization.tickets.show');
    Route::put('tickets/{ticket}', [App\Http\Controllers\Organization\ContactTicketController::class, 'update'])->name('organization.tickets.update');

    // Profile Settings
    Route::get('profile', [App\Http\Controllers\Organization\ProfileController::class, 'edit'])->name('organization.profile.edit');
    Route::put('profile', [App\Http\Controllers\Organization\ProfileController::class, 'update'])->name('organization.profile.update');
    Route::put('profile/password', [App\Http\Controllers\Organization\ProfileController::class, 'updatePassword'])->name('organization.profile.password');

    // Reports & Analytics
    Route::get('reports', [App\Http\Controllers\Organization\ReportsController::class, 'index'])->name('organization.reports.index');

    // Activity Logs
    Route::get('activity-logs', [App\Http\Controllers\Organization\ActivityLogsController::class, 'index'])->name('organization.activity-logs.index');
});
