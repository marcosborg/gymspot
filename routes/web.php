<?php

Route::get('link', function() {
   Artisan::call('storage:link'); 
});

Route::get('/', 'WebsiteController@index')->middleware(App\Http\Middleware\RedirectToAppStore::class);

Route::get('/cms/{content_page_id}/{slug}', 'WebsiteController@contentPage');

Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }
    return redirect()->route('admin.home');
});

Route::get('userVerification/{token}', 'UserVerificationController@approve')->name('userVerification');
Route::get('welcome', 'WebsiteController@welcome')->name('welcome');
Auth::routes();

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Content Category
    Route::delete('content-categories/destroy', 'ContentCategoryController@massDestroy')->name('content-categories.massDestroy');
    Route::resource('content-categories', 'ContentCategoryController');

    // Content Tag
    Route::delete('content-tags/destroy', 'ContentTagController@massDestroy')->name('content-tags.massDestroy');
    Route::resource('content-tags', 'ContentTagController');

    // Content Page
    Route::delete('content-pages/destroy', 'ContentPageController@massDestroy')->name('content-pages.massDestroy');
    Route::post('content-pages/media', 'ContentPageController@storeMedia')->name('content-pages.storeMedia');
    Route::post('content-pages/ckmedia', 'ContentPageController@storeCKEditorImages')->name('content-pages.storeCKEditorImages');
    Route::resource('content-pages', 'ContentPageController');

    // Faq Category
    Route::delete('faq-categories/destroy', 'FaqCategoryController@massDestroy')->name('faq-categories.massDestroy');
    Route::resource('faq-categories', 'FaqCategoryController');

    // Faq Question
    Route::delete('faq-questions/destroy', 'FaqQuestionController@massDestroy')->name('faq-questions.massDestroy');
    Route::resource('faq-questions', 'FaqQuestionController');

    // Client
    Route::delete('clients/destroy', 'ClientController@massDestroy')->name('clients.massDestroy');
    Route::post('clients/media', 'ClientController@storeMedia')->name('clients.storeMedia');
    Route::post('clients/ckmedia', 'ClientController@storeCKEditorImages')->name('clients.storeCKEditorImages');
    Route::resource('clients', 'ClientController');

    // Countries
    Route::delete('countries/destroy', 'CountriesController@massDestroy')->name('countries.massDestroy');
    Route::resource('countries', 'CountriesController');

    // Company
    Route::delete('companies/destroy', 'CompanyController@massDestroy')->name('companies.massDestroy');
    Route::post('companies/media', 'CompanyController@storeMedia')->name('companies.storeMedia');
    Route::post('companies/ckmedia', 'CompanyController@storeCKEditorImages')->name('companies.storeCKEditorImages');
    Route::resource('companies', 'CompanyController');

    // Payment
    Route::delete('payments/destroy', 'PaymentController@massDestroy')->name('payments.massDestroy');
    Route::resource('payments', 'PaymentController');

    // Spot
    Route::delete('spots/destroy', 'SpotController@massDestroy')->name('spots.massDestroy');
    Route::post('spots/media', 'SpotController@storeMedia')->name('spots.storeMedia');
    Route::post('spots/ckmedia', 'SpotController@storeCKEditorImages')->name('spots.storeCKEditorImages');
    Route::resource('spots', 'SpotController');

    // Slot
    Route::delete('slots/destroy', 'SlotController@massDestroy')->name('slots.massDestroy');
    Route::resource('slots', 'SlotController');

    // Slider
    Route::delete('sliders/destroy', 'SliderController@massDestroy')->name('sliders.massDestroy');
    Route::post('sliders/media', 'SliderController@storeMedia')->name('sliders.storeMedia');
    Route::post('sliders/ckmedia', 'SliderController@storeCKEditorImages')->name('sliders.storeCKEditorImages');
    Route::resource('sliders', 'SliderController');

    // Steps
    Route::delete('steps/destroy', 'StepsController@massDestroy')->name('steps.massDestroy');
    Route::resource('steps', 'StepsController');

    // About
    Route::delete('abouts/destroy', 'AboutController@massDestroy')->name('abouts.massDestroy');
    Route::post('abouts/media', 'AboutController@storeMedia')->name('abouts.storeMedia');
    Route::post('abouts/ckmedia', 'AboutController@storeCKEditorImages')->name('abouts.storeCKEditorImages');
    Route::resource('abouts', 'AboutController');

    // Call
    Route::delete('calls/destroy', 'CallController@massDestroy')->name('calls.massDestroy');
    Route::post('calls/media', 'CallController@storeMedia')->name('calls.storeMedia');
    Route::post('calls/ckmedia', 'CallController@storeCKEditorImages')->name('calls.storeCKEditorImages');
    Route::resource('calls', 'CallController');

    // Service
    Route::delete('services/destroy', 'ServiceController@massDestroy')->name('services.massDestroy');
    Route::resource('services', 'ServiceController');

    // Gallery
    Route::delete('galleries/destroy', 'GalleryController@massDestroy')->name('galleries.massDestroy');
    Route::post('galleries/media', 'GalleryController@storeMedia')->name('galleries.storeMedia');
    Route::post('galleries/ckmedia', 'GalleryController@storeCKEditorImages')->name('galleries.storeCKEditorImages');
    Route::resource('galleries', 'GalleryController');

    // Testimonial
    Route::delete('testimonials/destroy', 'TestimonialController@massDestroy')->name('testimonials.massDestroy');
    Route::resource('testimonials', 'TestimonialController');

    // Location
    Route::delete('locations/destroy', 'LocationController@massDestroy')->name('locations.massDestroy');
    Route::post('locations/media', 'LocationController@storeMedia')->name('locations.storeMedia');
    Route::post('locations/ckmedia', 'LocationController@storeCKEditorImages')->name('locations.storeCKEditorImages');
    Route::resource('locations', 'LocationController');

    // Menu
    Route::delete('menus/destroy', 'MenuController@massDestroy')->name('menus.massDestroy');
    Route::resource('menus', 'MenuController');

    // Personal Trainer
    Route::delete('personal-trainers/destroy', 'PersonalTrainerController@massDestroy')->name('personal-trainers.massDestroy');
    Route::post('personal-trainers/media', 'PersonalTrainerController@storeMedia')->name('personal-trainers.storeMedia');
    Route::post('personal-trainers/ckmedia', 'PersonalTrainerController@storeCKEditorImages')->name('personal-trainers.storeCKEditorImages');
    Route::resource('personal-trainers', 'PersonalTrainerController');

    // Items
    Route::delete('items/destroy', 'ItemsController@massDestroy')->name('items.massDestroy');
    Route::post('items/media', 'ItemsController@storeMedia')->name('items.storeMedia');
    Route::post('items/ckmedia', 'ItemsController@storeCKEditorImages')->name('items.storeCKEditorImages');
    Route::resource('items', 'ItemsController');

    // Rented Slot
    Route::delete('rented-slots/destroy', 'RentedSlotController@massDestroy')->name('rented-slots.massDestroy');
    Route::resource('rented-slots', 'RentedSlotController');

    // Client Data
    Route::delete('client-datas/destroy', 'ClientDataController@massDestroy')->name('client-datas.massDestroy');
    Route::resource('client-datas', 'ClientDataController');

    // Packs
    Route::delete('packs/destroy', 'PacksController@massDestroy')->name('packs.massDestroy');
    Route::post('packs/media', 'PacksController@storeMedia')->name('packs.storeMedia');
    Route::post('packs/ckmedia', 'PacksController@storeCKEditorImages')->name('packs.storeCKEditorImages');
    Route::resource('packs', 'PacksController');

    // Pack Purchase
    Route::delete('pack-purchases/destroy', 'PackPurchaseController@massDestroy')->name('pack-purchases.massDestroy');
    Route::resource('pack-purchases', 'PackPurchaseController');

    // Promo Code Item
    Route::delete('promo-code-items/destroy', 'PromoCodeItemController@massDestroy')->name('promo-code-items.massDestroy');
    Route::resource('promo-code-items', 'PromoCodeItemController');

    // Promo Code Usage
    Route::delete('promo-code-usages/destroy', 'PromoCodeUsageController@massDestroy')->name('promo-code-usages.massDestroy');
    Route::resource('promo-code-usages', 'PromoCodeUsageController');

    Route::get('system-calendar', 'SystemCalendarController@index')->name('systemCalendar');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});