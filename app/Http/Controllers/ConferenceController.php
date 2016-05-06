<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\ActiveCall;
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

    public function connect_client(Request $request, TwilioRestClient $client){
        $conference_id = $request->input('CallSid');

        $response = new Services_Twilio_Twiml;
        $dial = $response->dial();
        $dial->conference($conference_id, array(
            'startConferenceOnEnter' => false,
            'endConferenceOnExit' => true,
            'waitUrl' => '/conference/wait',
        ));

        $twilioNumber = config('services.twilio')['number'];
        $path = str_replace($request->path(), '', $request->url()) . 'conference/connect/' . $conference_id . '/agent1';
        $this->call('agent1', $path, $client);

        $active_call = ActiveCall::firstOrNew(['agent_id' => 'agent1']);
        $active_call->conference_id = $conference_id;
        $active_call->save();
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
        $conference_id = ActiveCall::where('agent_id', $agent_id)->first()->conference_id;
        $path = str_replace($request->path(), '', $request->url()) . 'conference/connect/' . $conference_id . '/agent2';
        return $this->call('agent2', $path, $client);
    }

    private function call($agent_id, $callback_path, $client) {
        $destinationNumber = 'client:' . $agent_id;
        $twilioNumber = config('services.twilio')['number'];
        try {
            $client->account->calls->create(
                $twilioNumber, // The number of the phone initiating the call
                'client:' . $agent_id, // The agent_id that will receive the call
                $callback_path // The URL Twilio will request when the call is answered
            );
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
        return 'ok';
    }
}
