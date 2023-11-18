<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CredentialController extends Controller
{
    public function remoteLogin(Request $request)
    {
        $sentWebsite = $request->input('website');
        $username = $request->input('username');
        $password = $request->input('password');

        $urls = [
            "fo1.altius.finance" => "https://fo1.api.altius.finance/api/v0.0.2/login",
            "fo2.altius.finance" => "https://fo2.api.altius.finance/api/v0.0.2/login"
        ];

        // Use Guzzle to make a request to the website
        $client = new Client();

        // Send credentials to the form endpoint
        try {
            $response = $client->request('POST', $urls[$sentWebsite], [
                'json' => [
                    'email' => $username,
                    'password' => $password
                ],
            ]);

            // Parse the response and extract the token
            $responseData = json_decode($response->getBody(), true);
            $token = $responseData['success']['token'];

            // Return the token to the client
            return response()->json(['token' => $token]);
        } catch (RequestException $e) {
              // Handle Guzzle request exceptions
              if ($e->getResponse() && $e->getResponse()->getStatusCode() !== 200) {
                return response()->json(['error' => 'Credentials not correct'], 403);
              }
        }
    }
}
