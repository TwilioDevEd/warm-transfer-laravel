<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(array('prefix' => 'conference'), function() {
        Route::post('wait',
            ['uses' => 'ConferenceController@wait', 'as' => 'conference-wait']
        );
        Route::post('connect/{conference_id}/agent1',
            ['uses' => 'ConferenceController@connect_agent1', 'as' => 'conference-connect-agent1']
        );
});
