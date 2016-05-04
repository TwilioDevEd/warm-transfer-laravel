<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Agent;
class ConferenceControllerTest extends TestCase
{
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
}
