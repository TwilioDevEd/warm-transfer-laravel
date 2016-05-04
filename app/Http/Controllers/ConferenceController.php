<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Services_Twilio_Twiml;

class ConferenceController extends Controller
{
    public function wait()
    {
        $response = new Services_Twilio_Twiml;
        $response->say(
            'Thank you for calling. Please wait in line for a few seconds. An agent will be with you shortly.',
            ['voice' => 'alice', 'language' => 'en-GB']
        );
        $response->play('http://com.twilio.music.classical.s3.amazonaws.com/BusyStrings.mp3');
        return response($response)->header('Content-Type', 'application/xml');
    }

    public function connect_agent1($conference_id){
        $response = new Services_Twilio_Twiml;
        $dial = $response->dial();
        $dial->conference('RapidResponseRoom', array(
            'startConferenceOnEnter' => true,
            'endConferenceOnExit' => false,
            'waitUrl' => 'http://twimlets.com/holdmusic?Bucket=com.twilio.music.classical',
        ));
        return response($response)->header('Content-Type', 'application/xml');
    }
}
