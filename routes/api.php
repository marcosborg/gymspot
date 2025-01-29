<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {

    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    // Users
    Route::apiResource('users', 'UsersApiController');
    Route::get('user', 'UsersApiController@user');
    Route::post('update-user', 'UsersApiController@updateUser');
    Route::post('update-client', 'UsersApiController@updateClient');
    Route::post('update-professional-data', 'UsersApiController@updateProfessionalData');
    Route::post('save-profile-photo', 'UsersApiController@saveProfilePhoto');
    Route::post('save-other-photo', 'UsersApiController@saveOtherPhoto');
    Route::get('delete-photo/{photo_id}', 'UsersApiController@deletePhoto');

    // Content Category
    Route::apiResource('content-categories', 'ContentCategoryApiController');

    // Content Tag
    Route::apiResource('content-tags', 'ContentTagApiController');

    // Content Page
    Route::post('content-pages/media', 'ContentPageApiController@storeMedia')->name('content-pages.storeMedia');
    Route::apiResource('content-pages', 'ContentPageApiController');

    // Faq Category
    Route::apiResource('faq-categories', 'FaqCategoryApiController');

    // Faq Question
    Route::apiResource('faq-questions', 'FaqQuestionApiController');

    // Client
    Route::post('clients/media', 'ClientApiController@storeMedia')->name('clients.storeMedia');
    Route::apiResource('clients', 'ClientApiController');

    // Countries
    Route::apiResource('countries', 'CountriesApiController');

    // Company
    Route::post('companies/media', 'CompanyApiController@storeMedia')->name('companies.storeMedia');
    Route::apiResource('companies', 'CompanyApiController');

    // Payment
    Route::apiResource('payments', 'PaymentApiController');

    // Spot
    Route::post('spots/media', 'SpotApiController@storeMedia')->name('spots.storeMedia');
    Route::apiResource('spots', 'SpotApiController');

    // Slot
    Route::apiResource('slots', 'SlotApiController');

    // Slider
    Route::post('sliders/media', 'SliderApiController@storeMedia')->name('sliders.storeMedia');
    Route::apiResource('sliders', 'SliderApiController');

    // Steps
    Route::apiResource('steps', 'StepsApiController');

    // About
    Route::post('abouts/media', 'AboutApiController@storeMedia')->name('abouts.storeMedia');
    Route::apiResource('abouts', 'AboutApiController');

    // Call
    Route::post('calls/media', 'CallApiController@storeMedia')->name('calls.storeMedia');
    Route::apiResource('calls', 'CallApiController');

    // Service
    Route::apiResource('services', 'ServiceApiController');

    // Gallery
    Route::post('galleries/media', 'GalleryApiController@storeMedia')->name('galleries.storeMedia');
    Route::apiResource('galleries', 'GalleryApiController');

    // Testimonial
    Route::apiResource('testimonials', 'TestimonialApiController');

    // Location
    Route::post('locations/media', 'LocationApiController@storeMedia')->name('locations.storeMedia');
    Route::apiResource('locations', 'LocationApiController');

    // Menu
    Route::apiResource('menus', 'MenuApiController');

    // Personal Trainer
    Route::post('personal-trainers/media', 'PersonalTrainerApiController@storeMedia')->name('personal-trainers.storeMedia');
    Route::apiResource('personal-trainers', 'PersonalTrainerApiController');

    // Items
    Route::post('items/media', 'ItemsApiController@storeMedia')->name('items.storeMedia');
    Route::apiResource('items', 'ItemsApiController');

    // Rented Slot
    Route::apiResource('rented-slots', 'RentedSlotApiController');
    Route::get('rented-slots', 'RentedSlotApiController@rentedSlots');

    // Client Data
    Route::apiResource('client-datas', 'ClientDataApiController');
    Route::get('get-client-data/{client_id}', 'ClientDataApiController@getClientData');
    Route::post('client-datas/create', 'ClientDataApiController@createClientData');
    Route::post('client-datas/update', 'ClientDataApiController@updateClientData');
});

Route::group(['prefix' => 'v2', 'as' => 'api.', 'namespace' => 'Api\V1\Admin'], function () {

    // Slider
    Route::apiResource('sliders', 'SliderApiController');

    // Steps
    Route::apiResource('steps', 'StepsApiController');

    // Menu
    Route::apiResource('menus', 'MenuApiController');

    // Content Page
    Route::apiResource('content-pages', 'ContentPageApiController');

    // About
    Route::apiResource('abouts', 'AboutApiController');

    // Service
    Route::apiResource('services', 'ServiceApiController');

    // Gallery
    Route::apiResource('galleries', 'GalleryApiController');

    // Location
    Route::apiResource('locations', 'LocationApiController');

    // Faq Question
    Route::apiResource('faq-questions', 'FaqQuestionApiController');

    // Spot
    Route::apiResource('spots', 'SpotApiController');

    Route::prefix('calendar')->group(function () {
        Route::get('month/{year?}/{month?}', 'CalendarController@month');
        Route::post('day', 'CalendarController@day');
    });

    // Personal Trainer
    Route::post('personal-trainers/media', 'PersonalTrainerApiController@storeMedia')->name('personal-trainers.storeMedia');
    Route::apiResource('personal-trainers', 'PersonalTrainerApiController');

    // Client Data
    Route::apiResource('client-datas', 'ClientDataApiController');
});

Route::post('register', 'Api\AuthController@register');
Route::post('login', 'Api\AuthController@login');
Route::post('delete-account', 'Api\AuthController@deleteAccount')->middleware(['auth:sanctum']);

Route::prefix('callback')->group(function () {
    Route::get('multibanco', 'Api\PaymentsController@calbackMultibanco');
    Route::get('mbway', 'Api\PaymentsController@calbackMbway');
});

Route::prefix('payments')->middleware('auth:sanctum')->group(function () {
    Route::post('mbway', 'Api\PaymentsController@mbway');
    Route::get('check-mbway-status/{requestId}', 'Api\PaymentsController@checkMbwayStatus');
    Route::post('multibanco', 'Api\PaymentsController@multibanco');
});

//GUIA FITNESS
Route::prefix('guia-fitness')->middleware('auth:sanctum')->group(function () {
    Route::post('start-conversation', 'Api\GuiaFitnessController@startConversation');
    Route::post('send-message', 'Api\GuiaFitnessController@sendMessage');
});

Route::post('save-token', 'Api\AuthController@saveToken');
