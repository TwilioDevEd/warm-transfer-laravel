<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Services_Twilio_Twiml;
use Services_Twilio as TwilioRestClient;

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
        $dial->conference($conference_id, array(
            'startConferenceOnEnter' => true,
            'endConferenceOnExit' => false,
            'waitUrl' => 'http://twimlets.com/holdmusic?Bucket=com.twilio.music.classical',
        ));
        return response($response)->header('Content-Type', 'application/xml');
    }

    public function connect_agent2($conference_id){
        $response = new Services_Twilio_Twiml;
        $dial = $response->dial();
        $dial->conference($conference_id, array(
            'startConferenceOnEnter' => true,
            'endConferenceOnExit' => true,
            'waitUrl' => 'http://twimlets.com/holdmusic?Bucket=com.twilio.music.classical',
        ));
        return response($response)->header('Content-Type', 'application/xml');
    }

    public function call_agent2($agent_id, Request $request, TwilioRestClient $client){
        $destinationNumber = 'client:agent2';
        $twilioNumber = config('services.twilio')['number'];
        $path = str_replace($request->path(), '', $request->url()) . 'conference/connect/agent2';
        try {
            $client->account->calls->create(
                $twilioNumber, // The number of the phone initiating the call
                'client:agent2', // The agent_id that will receive the call
                $path // The URL Twilio will request when the call is answered
            );
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
        return 'ok';
    }
}
