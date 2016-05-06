<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Services_Twilio_Capability as TwilioCapability;

class TokenController extends Controller
{
    public function token($agentId, TwilioCapability $capability)
    {
        $capability->allowClientIncoming($agentId);

        $token = $capability->generateToken();
        return response()->json(['token' => $token, 'agentId' => $agentId]);
    }
}
