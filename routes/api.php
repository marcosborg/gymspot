<?php

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin', 'middleware' => ['auth:sanctum']], function () {
    // Permissions
    Route::apiResource('permissions', 'PermissionsApiController');

    // Roles
    Route::apiResource('roles', 'RolesApiController');

    // Users
    Route::apiResource('users', 'UsersApiController');

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
});