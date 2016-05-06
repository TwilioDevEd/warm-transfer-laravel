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
        return $this->generateWaitTwiml();
    }

    public function connectClient(Request $request, TwilioRestClient $client){
        $conferenceId = $request->input('CallSid');
        $twilioNumber = config('services.twilio')['number'];

        $this->createCall('agent1', $conferenceId, $client, $request);

        $activeCall = ActiveCall::firstOrNew(['agent_id' => 'agent1']);
        $activeCall->conference_id = $conferenceId;
        $activeCall->save();

        return $this->generateConferenceTwiml($conferenceId, false, true, '/conference/wait');
    }

    public function connectAgent1($conferenceId){
        return $this->generateConferenceTwiml($conferenceId, true, false);
    }

    public function connectAgent2($conferenceId){
        return $this->generateConferenceTwiml($conferenceId, true, true);
    }

    public function callAgent2($agentId, Request $request, TwilioRestClient $client){
        $destinationNumber = 'client:agent2';
        $twilioNumber = config('services.twilio')['number'];
        $conferenceId = ActiveCall::where('agent_id', $agentId)->first()->conference_id;

        return $this->createCall('agent2', $conferenceId, $client, $request);
    }

    private function createCall($agentId, $conferenceId, $client, $request) {
        $destinationNumber = 'client:' . $agentId;
        $twilioNumber = config('services.twilio')['number'];
        $path = str_replace($request->path(), '', $request->url()) . 'conference/connect/' . $conferenceId . '/' . $agentId;
        try {
            $client->account->calls->create(
                $twilioNumber, // The number of the phone initiating the call
                'client:' . $agentId, // The agent_id that will receive the call
                $path // The URL Twilio will request when the call is answered
            );
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
        return 'ok';
    }

    private function generateConferenceTwiml($conferenceId, $startOnEnter, $endOnExit, $waitUrl = null){
        if ($waitUrl === null){
            $waitUrl = 'http://twimlets.com/holdmusic?Bucket=com.twilio.music.classical';
        }
        $response = new Services_Twilio_Twiml;
        $dial = $response->dial();
        $dial->conference(
            $conferenceId, 
            ['startConferenceOnEnter' => $startOnEnter,
            'endConferenceOnExit' => $endOnExit,
            'waitUrl' => $waitUrl]
        );
        return response($response)->header('Content-Type', 'application/xml');
    }

    private function generateWaitTwiml(){
        $response = new Services_Twilio_Twiml;
        $response->say(
            'Thank you for calling. Please wait in line for a few seconds. An agent will be with you shortly.',
            ['voice' => 'alice', 'language' => 'en-GB']
        );
        $response->play('http://com.twilio.music.classical.s3.amazonaws.com/BusyStrings.mp3');
        return response($response)->header('Content-Type', 'application/xml');
    }
}
