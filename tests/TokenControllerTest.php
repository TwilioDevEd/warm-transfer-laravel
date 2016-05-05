<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Agent;
use App\ActiveCall;

class TokenControllerTest extends TestCase
{
    public function testAgentToken()
    {
        //Given
        $mockToken = 'th1stot3satok3n';

        $mockTwilioCapability = Mockery::mock('Services_Twilio_Capability')
                              ->makePartial();

        $mockTwilioCapability
            ->shouldReceive('allowClientIncoming')
            ->with('agent1');

        $mockTwilioCapability
            ->shouldReceive('generateToken')
            ->andReturn($mockToken);

        $this->app->instance(
            'Services_Twilio_Capability',
            $mockTwilioCapability
        );

        // When
        $newTokenResponse = $this->call(
            'POST',
            route('agent-token', ['agent_id' => 'agent1'])
        );

        // Then
        $newToken = json_decode($newTokenResponse->getContent());
        $this->assertEquals(
            $mockToken,
            $newToken->token
        );
    }
}
