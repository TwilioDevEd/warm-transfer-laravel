<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Agent;
use App\ActiveCall;
use Twilio\Rest\Client;

class ConferenceControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function testWait()
    {
        // When
        $joinResponse = $this->call('POST', route('conference-wait'));
        $joinDocument = new SimpleXMLElement($joinResponse->getContent());
        // Then
        $this->assertNotNull($joinDocument->Say);
        $this->assertNotEmpty($joinDocument->Say);
        $this->assertNotNull($joinDocument->Play);
        $this->assertNotEmpty($joinDocument->Play);
        $this->assertEquals(strval($joinDocument->Say), 'Thank you for calling. Please wait in line for a few seconds. An agent will be with you shortly.');
        $this->assertEquals(strval($joinDocument->Play), 'http://com.twilio.music.classical.s3.amazonaws.com/BusyStrings.mp3');
    }

    public function testConnectClient()
    {
        //Given
        $this->startSession();
        $twilioNumber = config('services.twilio')['number'];
        $mockTwilioService = Mockery::mock(Client::class)->makePartial();
        $mockTwilioCalls = Mockery::mock();
        $mockTwilioService->calls = $mockTwilioCalls;
        $mockTwilioCalls->shouldReceive('create')->once()
            ->with(
                'client:agent1',
                $twilioNumber,
                [
                    'url' => 'http://localhost/conference/connect/callsid123/agent1'
                ]
            );
        $this->app->instance(
            Client::class,
            $mockTwilioService
        );
        // When
        $connectResponse = $this->call(
            'POST',
            route('conference-connect-client'),
            ['CallSid' => 'callsid123']
        );

        $connectDocument = new SimpleXMLElement($connectResponse->getContent());
        // Then
        $this->assertNotNull($connectDocument->Dial);
        $this->assertNotEmpty($connectDocument->Dial, $connectResponse);
        $this->assertNotNull($connectDocument->Dial->Conference);
        $this->assertNotEmpty($connectDocument->Dial->Conference);
        $this->assertEquals(strval($connectDocument->Dial->Conference), 'callsid123');
        $this->assertEquals(strval($connectDocument->Dial->Conference['startConferenceOnEnter']), 'false');
        $this->assertEquals(strval($connectDocument->Dial->Conference['endConferenceOnExit']), 'true');
        $this->assertEquals(strval($connectDocument->Dial->Conference['waitUrl']), '/conference/wait');
    }

    public function testConnectAgent1()
    {
        // When
        $connectResponse = $this->call('POST', route('conference-connect-agent1', ['conference_id' => 'conference123']));
        $connectDocument = new SimpleXMLElement($connectResponse->getContent());
        // Then
        $this->assertNotNull($connectDocument->Dial);
        $this->assertNotEmpty($connectDocument->Dial);
        $this->assertNotNull($connectDocument->Dial->Conference);
        $this->assertNotEmpty($connectDocument->Dial->Conference);
        $this->assertEquals(strval($connectDocument->Dial->Conference), 'conference123');
        $this->assertEquals(strval($connectDocument->Dial->Conference['startConferenceOnEnter']), 'true');
        $this->assertEquals(strval($connectDocument->Dial->Conference['endConferenceOnExit']), 'false');
        $this->assertEquals(strval($connectDocument->Dial->Conference['waitUrl']), 'http://twimlets.com/holdmusic?Bucket=com.twilio.music.classical');
    }

    public function testConnectAgent2()
    {
        // When
        $connectResponse = $this->call('POST', route('conference-connect-agent2', ['conference_id' => 'conference123']));
        $connectDocument = new SimpleXMLElement($connectResponse->getContent());
        // Then
        $this->assertNotNull($connectDocument->Dial);
        $this->assertNotEmpty($connectDocument->Dial);
        $this->assertNotNull($connectDocument->Dial->Conference);
        $this->assertNotEmpty($connectDocument->Dial->Conference);
        $this->assertEquals(strval($connectDocument->Dial->Conference), 'conference123');
        $this->assertEquals(strval($connectDocument->Dial->Conference['startConferenceOnEnter']), 'true');
        $this->assertEquals(strval($connectDocument->Dial->Conference['endConferenceOnExit']), 'true');
        $this->assertEquals(strval($connectDocument->Dial->Conference['waitUrl']), 'http://twimlets.com/holdmusic?Bucket=com.twilio.music.classical');
    }

    public function testCallAgent2()
    {
        // Given
        $this->startSession();
        $twilioNumber = config('services.twilio')['number'];
        $mockTwilioService = Mockery::mock(Client::class)->makePartial();
        $mockTwilioCalls = Mockery::mock();
        $mockTwilioService->calls = $mockTwilioCalls;
        $mockTwilioCalls->shouldReceive('create')->once()
            ->with(
                'client:agent2',
                $twilioNumber,
                [
                    'url' => 'http://localhost/conference/connect/conference123/agent2'
                ]
            );
        $this->app->instance(
            Client::class,
            $mockTwilioService
        );
        $activeCall = ActiveCall::firstOrNew(['agent_id' => 'agent1']);
        $activeCall->conference_id = 'conference123';
        $activeCall->save();
        // When
        $response = $this->call(
            'POST',
            route('conference-call', ['agent_id' => 'agent1'])
        );
        // Then
        $this->assertEquals($response->getContent(), 'ok');
    }
}
