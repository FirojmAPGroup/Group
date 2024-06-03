<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '', 'namespace' => '\App\Http\Controllers\Api', 'middleware' => ['api']], function () {
    Route::group(['prefix' => '', 'middleware' => []], function () {
        // Without Login
        Route::group(['prefix' => ''], function () {
            Route::post('/login','AuthController@login')->name('api.login');
            Route::post('/register','AuthController@register')->name('api.register');
            Route::post('/verify-email','AuthController@verifyEmail')->name('api.verifyEmail');
            Route::post('/verify-mobile','AuthController@verifyMobile')->name('api.verifyMobile');
            Route::post('/update-password','AuthController@updatePassword')->name('api.updatePassword');
            Route::post('/update-profile','AuthController@updateProfile')->name('api.updateProfile');
            // Route::post('/forgot-password', 'AuthController@forgot_password')->name('api.forgot-password');
        });
    });
    
    Route::group(['prefix' => '', 'middleware' => ["apiuser"]], function () {
        // With Login
        Route::group(['prefix' => ''], function () {
            Route::post('/refresh-token','AuthController@refresh')->name('api.refresh-token');
            Route::post('/logout','AuthController@logout')->name('api.logout');
            Route::post('/update-location','AuthController@updateLocation')->name('api.update-location');
            Route::post('/update-device-token','AuthController@updateFCM')->name('api.update-fcm');
            Route::get('/user-profile','AuthController@userProfile')->name('api.userProfile');
            //leads
            Route::get('/pending-leads','LeadsController@getPendingLeads')->name('api.pendingLeads');
            Route::get('/completed-leads','LeadsController@getDoneLeads')->name('api.completedLeads');
            Route::get('/today-leads','LeadsController@todayLeads')->name('api.todayLeads');
            Route::get('/leads-report','LeadsController@leadReport')->name('api.leadReport');
            Route::post('/add-lead','LeadsController@createLead')->name('api.createLead');
            Route::get('/detail-lead/{id}','LeadsController@detailLeadView')->name('api.detailLeadView');
            Route::post('/update-lead/{id}','LeadsController@updateLead')->name('api.updateLead');

            // Route::get('/notifications', 'Notifications@getNotifications')->name('api.notifications');

        });
    });
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('/notifications', 'Notifications@getNotifications')->name('api.notifications');
        Route::post('/notifications/read/{id}', 'Notifications@markAsRead')->name('api.notificationsread');

    });

});
