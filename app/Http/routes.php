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

Route::post('/{agent_id}/token',
    ['uses' => 'TokenController@token', 'as' => 'agent-token']
);
Route::post('conference/connect/client',
    ['uses' => 'ConferenceController@connectClient', 'as' => 'conference-connect-client']
);
Route::post('conference/wait',
    ['uses' => 'ConferenceController@wait', 'as' => 'conference-wait']
);
Route::post('conference/connect/{conference_id}/agent1',
    ['uses' => 'ConferenceController@connectAgent1', 'as' => 'conference-connect-agent1']
);
Route::post('conference/connect/{conference_id}/agent2',
    ['uses' => 'ConferenceController@connectAgent2', 'as' => 'conference-connect-agent2']
);
Route::post('conference/{agent_id}/call',
    ['uses' => 'ConferenceController@callAgent2', 'as' => 'conference-call']
);
