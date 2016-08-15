<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Twilio\Jwt\ClientToken;

class TokenController extends Controller
{
    public function token($agentId, ClientToken $capability)
    {
        $capability->allowClientIncoming($agentId);

        $token = $capability->generateToken();
        return response()->json(['token' => $token, 'agentId' => $agentId]);
    }
}
