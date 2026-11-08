<?php

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InfoUserController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\InquirytypesController;
use App\Http\Controllers\OtherServiceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PilgrimageBookingController;
use App\Http\Controllers\PilgrimageVoucherController;
use App\Http\Controllers\SystemIntegrationSettingController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ResetController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SalesReferenceController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 */

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', [HomeController::class, 'home']);
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index')->middleware(['permission:view reports']);
    Route::get('reports/data', [ReportController::class, 'getData'])->name('reports.data')->middleware(['permission:view reports']);
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export')->middleware(['permission:view reports']);
    Route::get('reports/count', [ReportController::class, 'count'])->name('reports.count')->middleware(['permission:view reports']);

    Route::get('billing', function () {
        return view('billing');
    })->name('billing');

    Route::get('/profile', [InfoUserController::class, 'view'])->name('profile.view');
    Route::get('/profile/view', [InfoUserController::class, 'view']);
    Route::get('/profile/edit', [InfoUserController::class, 'create'])->name('profile.edit');
    Route::get('/profile/password', [InfoUserController::class, 'passwordForm'])->name('profile.password');
    Route::post('/profile/password', [InfoUserController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('rtl', function () {
        return view('rtl');
    })->name('rtl');

    Route::get('tables', function () {
        return view('tables');
    })->name('tables');

    Route::get('virtual-reality', function () {
        return view('virtual-reality');
    })->name('virtual-reality');

    Route::get('static-sign-in', function () {
        return view('static-sign-in');
    })->name('sign-in');

    Route::get('static-sign-up', function () {
        return view('static-sign-up');
    })->name('sign-up');

    Route::get('/logout', [SessionsController::class, 'destroy'])->name('logout');
    Route::get('/user-profile', [InfoUserController::class, 'create'])->name('user-profile');
    Route::post('/user-profile', [InfoUserController::class, 'store'])->name('user-profile.update');

    // Customers
    Route::get('customers', [CustomerController::class, 'index'])->name('customers.index')->middleware(['permission:Customers list']);
    Route::get('customers/create', [CustomerController::class, 'create'])->middleware(['permission:Customers add']);
    Route::post('customers/store', [CustomerController::class, 'store'])->middleware(['permission:Customers add']);
    Route::get('customers/edit/{id}', [CustomerController::class, 'edit'])->name('customers.edit')->middleware(['permission:Customers edit']);
    Route::put('customers/{id}', [CustomerController::class, 'update'])->name('customers.update')->middleware(['permission:Customers edit']);
    Route::get('customers/view/{id}', [CustomerController::class, 'view'])->middleware(['permission:Customers view']);
    Route::post('customers/destroy/{id}', [CustomerController::class, 'destroy'])->middleware(['permission:Customers delete']);

    Route::get('get-data', [AjaxController::class, 'getData']);
    Route::get('get_customer_details', [AjaxController::class, 'get_customer_details']);
    Route::get('check_customer_number/{cell}', [AjaxController::class, 'check_customer_number']);
    Route::get('customer_list/{query?}', [AjaxController::class, 'customer_search']);
    Route::get('get_customer_data', [AjaxController::class, 'getCustomerData'])->name('get_customer_data');

    // Inquiry
    Route::get('/inquiry', [InquiryController::class, 'get_inquiry_list'])->middleware(['permission:Inquiry list'])->name('inquiry.index');
    Route::get('/inquiry/create', [InquiryController::class, 'create'])->middleware(['permission:Inquiry add']);
    Route::get('/edit_inquiry/{id}', [InquiryController::class, 'edit'])->name('edit_inquiry')->middleware(['permission:Inquiry edit']);
    Route::post('/update_inquiry/{id}', [InquiryController::class, 'update'])->name('update_inquiry')->middleware(['permission:Inquiry edit']);
    Route::post('/inquiry/delete/{id}', [InquiryController::class, 'destroy'])->name('delete_inquiry')->middleware(['permission:Inquiry delete']);
    Route::get('/inquiry/upload', [InquiryController::class, 'uploadView'])->name('csv.upload.view')->middleware(['permission:Inquiry add']);
    Route::get('/inquiry/csv-template', [InquiryController::class, 'downloadTemplate'])->name('csv.template.download')->middleware(['permission:Inquiry add']);
    Route::post('/inquiry/upload', [InquiryController::class, 'uploadCSV'])->name('csv.upload')->middleware(['permission:Inquiry add']);


    Route::get('/get_followup_details/{id}', [AjaxController::class, 'get_followup_details']);
    Route::get('/get_more_followups', [InquiryController::class, 'get_more_followups']);
    // Route::get('/inquiry_edit/{inquiry_id}', [InquiryController::class, 'edit_inquiry_index']);
    Route::get('/inquiry_ajax_list', [InquiryController::class, 'getdata']);
    Route::get('/inquiry_test/create', [InquiryController::class, 'create_test']);
    Route::post('/inquiry/store', [InquiryController::class, 'store'])->middleware(['permission:Inquiry add']);
    Route::post('/add_inquiry_remarks', [InquiryController::class, 'add_inquiry_remarks']);
    Route::post('/add_followup_remarks', [InquiryController::class, 'add_followup_remarks']);
    Route::get('/get_follow_up/{id}', [InquiryController::class, 'get_follow_up']);
    Route::post('/inquiry_edit_update', [InquiryController::class, 'inquiry_edit_update'])->middleware(['permission:Inquiry edit']);
    Route::get('/append_services_edit/{inq_id}', [InquiryController::class, 'append_services_edit']);
    Route::get('/get_sub_services/{id}', [AjaxController::class, 'get_sub_services'])->name('get_sub_services');
    Route::get('/get_sub_services_id/{id}/{inq_id}', [AjaxController::class, 'get_sub_services_id']);
    Route::get('/add_more_services/{count}', [AjaxController::class, 'add_more_services'])->name('add_more_services');
    Route::get('/add_more_services_users/{count}', [AjaxController::class, 'add_more_services_users'])->name('add_more_services_users');
    Route::get('/get_campaign_data/{id}', [AjaxController::class, 'get_campaign_data'])->name('get_campaign_data');
    Route::get('/follow_up/{id}', [InquiryController::class, 'follow_up'])->middleware(['permission:Inquiry list']);
    Route::post('add_progress_remark', [App\Http\Controllers\InquiryController::class, 'add_progress_remark']);
    Route::get('get_progress_remarks/{id}', [App\Http\Controllers\InquiryController::class, 'get_progress_remarks']);
    Route::post('update_sales_person', [App\Http\Controllers\InquiryController::class, 'updateSalesPerson'])->middleware(['permission:Inquiry edit']);
    Route::post('bulk_update_sales_person', [App\Http\Controllers\InquiryController::class, 'bulkUpdateSalesPerson'])->middleware(['permission:Inquiry edit']);

    Route::get('/create_quotation/{id}', [QuotationController::class, 'create_quotation'])->middleware(['permission:Inquiry edit']);
    Route::get('autocomplete_country', [AjaxController::class, 'autocomplete'])->name('autocomplete_country');
    Route::get('autocomplete_city', [AjaxController::class, 'autocomplete_city'])->name('autocomplete_city');
    Route::get('autocomplete_country_list', [AjaxController::class, 'autocomplete_country'])->name('autocomplete_country_list');
    Route::post('api/geo/store-city', [AjaxController::class, 'store_city'])->name('geo.store_city');


    // User Management
    Route::get('user-management', [UsersController::class, 'index'])->middleware(['permission:Users Management list']);
    Route::get('users/create', [UsersController::class, 'create'])->middleware(['permission:Users Management add']);
    Route::post('users/store', [UsersController::class, 'store'])->middleware(['permission:Users Management add']);
    Route::get('users/edit/{id}', [UsersController::class, 'edit'])->middleware(['permission:Users Management edit']);
    Route::post('users/update/{id}', [UsersController::class, 'update'])->middleware(['permission:Users Management edit']);
    Route::delete('users/delete/{id}', [UsersController::class, 'destroy'])->middleware(['permission:Users Management delete']);
    Route::get('users/impersonate/{id}', [UsersController::class, 'impersonate'])->name('users.impersonate')->middleware(['auth']);
    Route::get('impersonate/leave', [UsersController::class, 'leaveImpersonate'])->name('users.leave_impersonate')->middleware(['auth']);

    // Global Settings Module
    Route::get('settings', [\App\Http\Controllers\SystemSettingController::class, 'index'])->name('settings.index')->middleware(['auth']);
    Route::post('settings', [\App\Http\Controllers\SystemSettingController::class, 'update'])->name('settings.update')->middleware(['auth']);

    // Roles Management
    Route::group(['prefix' => 'roles', 'middleware' => ['auth']], function () {
        Route::get('', [RoleController::class, 'index'])->middleware(['permission:Roles Management list']);
        Route::get('/add', [RoleController::class, 'create'])->middleware(['permission:Roles Management add']);
        Route::post('/store', [RoleController::class, 'store'])->middleware(['permission:Roles Management add']);
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->middleware(['permission:Roles Management edit']);
        Route::post('/edit/{id}', [RoleController::class, 'update'])->middleware(['permission:Roles Management edit']);
        Route::post('/delete', [RoleController::class, 'destroy'])->middleware(['permission:Roles Management delete']);
    });

    // Permission Management (Super-Admin only)
    Route::prefix('permission')->middleware(['permission:Super-Admin'])->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('permission.index');
        Route::get('/create', [PermissionController::class, 'create'])->name('permission.create');
        Route::get('/get-sub-modules', [PermissionController::class, 'getSubModules'])->name('get.sub.modules');
        Route::post('/', [PermissionController::class, 'store'])->name('permission.store');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('permission.edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('permission.update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('permission.destroy');
    });

    // Inquiry Type
    Route::get('/inquiry-type', [InquirytypesController::class, 'index'])->middleware(['permission:Inquiry Type list']);
    Route::get('inquiry-type/create', [InquirytypesController::class, 'create'])->middleware(['permission:Inquiry Type add']);
    Route::post('inquiry-type/store', [InquirytypesController::class, 'store'])->middleware(['permission:Inquiry Type add']);
    Route::get('inquiry-type/edit/{id}', [InquirytypesController::class, 'edit'])->middleware(['permission:Inquiry Type edit']);
    Route::post('inquiry-type/update/{id}', [InquirytypesController::class, 'update'])->middleware(['permission:Inquiry Type edit']);
    Route::delete('/inquiry-type/delete/{id}', [InquirytypesController::class, 'destroy'])->name('inquiry-type.destroy')->middleware(['permission:Inquiry Type delete']);

    // Sales References
    Route::get('/sales-reference', [SalesReferenceController::class, 'index'])->middleware(['permission:Sales References list']);
    Route::get('sales-reference/create', [SalesReferenceController::class, 'create'])->middleware(['permission:Sales References add']);
    Route::post('sales-reference/store', [SalesReferenceController::class, 'store'])->middleware(['permission:Sales References add']);
    Route::get('sales-reference/edit/{id}', [SalesReferenceController::class, 'edit'])->middleware(['permission:Sales References edit']);
    Route::post('sales-reference/update/{id}', [SalesReferenceController::class, 'update'])->middleware(['permission:Sales References edit']);
    Route::delete('sales-reference/delete/{id}', [SalesReferenceController::class, 'destroy'])->middleware(['permission:Sales References delete'])->name('sales-reference.destroy');

    // Currencies & Exchange Rates
    Route::get('/currencies', [CurrencyController::class, 'index'])->name('currencies.index')->middleware(['permission:Currencies list']);
    Route::post('/currencies', [CurrencyController::class, 'store'])->name('currencies.store')->middleware(['permission:Currencies edit']);
    Route::put('/currencies/{id}', [CurrencyController::class, 'update'])->name('currencies.update')->middleware(['permission:Currencies edit']);
    Route::delete('/currencies/{id}', [CurrencyController::class, 'destroy'])->name('currencies.destroy')->middleware(['permission:Currencies edit']);
    Route::get('/currencies/rates-json', [CurrencyController::class, 'ratesJson'])->name('currencies.rates');

    // Hotels & Accommodations (Preferences)
    Route::resource('hotels', HotelController::class);
    // Route::get('/api/hotels-by-city' moved below auth group
    Route::post('/api/hotels/quick-store', [HotelController::class, 'quickStore'])->name('hotels.quick_store');

    // Services
    Route::get('/services', [ServiceController::class, 'index'])->middleware(['permission:Services list']);
    Route::get('services/create', [ServiceController::class, 'create'])->middleware(['permission:Services add']);
    Route::post('services/store', [ServiceController::class, 'store'])->middleware(['permission:Services add']);
    Route::get('services/edit/{id}', [ServiceController::class, 'edit'])->middleware(['permission:Services edit']);
    Route::post('services/update/{id}', [ServiceController::class, 'update'])->middleware(['permission:Services edit']);
    Route::delete('services/delete/{id}', [ServiceController::class, 'destroy'])->name('services.destroy')->middleware(['permission:Services delete']);

    // Assign permission to role
    Route::get('roles/permission/{id?}', [PermissionController::class, 'permission_index'])->middleware(['permission:Roles Permissions list']);
    Route::post('roles/permission/{role_id}', [PermissionController::class, 'assignPermissions'])->middleware(['permission:Roles Permissions edit']);

    // Other Services
    Route::get('/other_services', [OtherServiceController::class, 'index'])->middleware(['permission:Services list']);
    Route::get('/other_services/edit/{id}', [OtherServiceController::class, 'edit'])->middleware(['permission:Services edit']);
    Route::get('/other_services/create', [OtherServiceController::class, 'create'])->middleware(['permission:Services add']);
    Route::post('/other_services/store', [OtherServiceController::class, 'store'])->middleware(['permission:Services add']);
    Route::post('/other_services/update/{id}', [OtherServiceController::class, 'update'])->middleware(['permission:Services edit']);
    Route::match(['post', 'delete'], '/other_services/delete/{id}', [OtherServiceController::class, 'destroy'])->middleware(['permission:Services delete']);

    // Follow-ups
    Route::get('/followups', [App\Http\Controllers\FollowupController::class, 'index'])->name('followups.index')->middleware(['permission:Inquiry list']);
    Route::get('/get-followups-data', [App\Http\Controllers\FollowupController::class, 'getData'])->name('followups.data')->middleware(['permission:Inquiry list']);

    // Pilgrimage & Dynamic Itinerary Operations Suite
    Route::prefix('pilgrimage')->name('pilgrimage.')->group(function () {
        Route::get('/', [PilgrimageBookingController::class, 'index'])->name('index')->middleware(['permission:Pilgrimage list']);
        Route::get('/create', [PilgrimageBookingController::class, 'create'])->name('create')->middleware(['permission:Pilgrimage create']);
        Route::post('/store', [PilgrimageBookingController::class, 'store'])->name('store')->middleware(['permission:Pilgrimage create']);
        Route::get('/{id}/edit', [PilgrimageBookingController::class, 'edit'])->name('edit')->middleware(['permission:Pilgrimage edit']);
        Route::put('/{id}', [PilgrimageBookingController::class, 'update'])->name('update')->middleware(['permission:Pilgrimage edit']);
        Route::delete('/{id}', [PilgrimageBookingController::class, 'destroy'])->name('destroy')->middleware(['permission:Pilgrimage delete']);

        // Interactive AJAX Modal & Leg Status Toggle
        Route::get('/{id}/modal-details', [PilgrimageBookingController::class, 'getDetailModal'])->name('modal.details')->middleware(['permission:Pilgrimage list']);
        Route::post('/legs/{legId}/toggle-status', [PilgrimageBookingController::class, 'toggleLegStatus'])->name('legs.toggle')->middleware(['permission:Pilgrimage edit']);

        // Hotel Accommodation Voucher (PDF / Print View)
        Route::get('/{id}/voucher', [PilgrimageVoucherController::class, 'show'])->name('voucher.show')->middleware(['permission:Pilgrimage list']);
    });

    // System Integration Settings & Auto Lead Assignment Module
    Route::get('/system-integration-settings', [\App\Http\Controllers\SystemIntegrationSettingController::class, 'edit'])->name('integration_settings.edit')->middleware(['permission:Super-Admin']);
    Route::post('/system-integration-settings', [\App\Http\Controllers\SystemIntegrationSettingController::class, 'update'])->name('integration_settings.update')->middleware(['permission:Super-Admin']);
    Route::post('/system-integration-settings/regenerate-api-key', [\App\Http\Controllers\SystemIntegrationSettingController::class, 'regenerateApiKey'])->name('integration_settings.regenerate_key')->middleware(['permission:Super-Admin']);
    Route::post('/system-integration-settings/simulate-lead', [\App\Http\Controllers\SystemIntegrationSettingController::class, 'simulateLead'])->name('integration_settings.simulate_lead')->middleware(['permission:Super-Admin']);

});

// Public/Fast API lookup for Hotels by City (used by Pilgrimage form)
Route::get('/api/hotels-by-city', [HotelController::class, 'getByCity'])->name('hotels.by_city');

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [SessionsController::class, 'create']);
    Route::post('/session', [SessionsController::class, 'store']);
    Route::get('/login/forgot-password', [ResetController::class, 'create']);
    Route::post('/forgot-password', [ResetController::class, 'sendEmail']);
    Route::get('/reset-password/{token}', [ResetController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
});

Route::get('/login', function () {
    return view('session/login-session');
})->name('login');

// Organization settings: login required & permission
Route::middleware('auth')->group(function () {
    Route::get('organization-settings', 'App\Http\Controllers\OrganizationSettingController@edit')->name('organization_settings.edit')->middleware(['permission:Organization Settings edit']);
    Route::post('organization-settings', 'App\Http\Controllers\OrganizationSettingController@update')->name('organization_settings.update')->middleware(['permission:Organization Settings edit']);
});

// Reviews module: customer reviews for completed inquiries
Route::middleware('auth')->prefix('reviews')->name('reviews.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ReviewController::class, 'index'])->name('index')->middleware(['permission:Reviews list']);
    Route::get('/data', [\App\Http\Controllers\ReviewController::class, 'data'])->name('data')->middleware(['permission:Reviews list']);
    Route::get('/counts', [\App\Http\Controllers\ReviewController::class, 'counts'])->name('counts')->middleware(['permission:Reviews list']);
    Route::post('/sync-preview', [\App\Http\Controllers\ReviewController::class, 'syncPreview'])->name('sync_preview')->middleware(['permission:Reviews sync']);
    Route::post('/sync', [\App\Http\Controllers\ReviewController::class, 'sync'])->name('sync')->middleware(['permission:Reviews sync']);
    Route::post('/reassign', [\App\Http\Controllers\ReviewController::class, 'reassign'])->name('reassign')->middleware(['permission:Reviews all']);
    Route::post('/{id}/followup', [\App\Http\Controllers\ReviewController::class, 'followup'])->name('followup')->whereNumber('id')->middleware(['permission:Reviews list']);
    Route::get('/{id}/history', [\App\Http\Controllers\ReviewController::class, 'history'])->name('history')->whereNumber('id')->middleware(['permission:Reviews list']);
});
