<?php

namespace App\Http;

use DB;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use Log;
use Symfony\Component\HttpFoundation\Response;

class AimsiaApi {
    const ACCESS_TOKEN_SESSION_KEY = 'access_token';

    public function sendRequest($method, $endpoint, $form):object {
        if (Session::has(self::ACCESS_TOKEN_SESSION_KEY)) {
            $access_token = Session::get(self::ACCESS_TOKEN_SESSION_KEY);
        } else {
            $access_token = $this->generateAccessToken();
            Session::put(self::ACCESS_TOKEN_SESSION_KEY, $access_token);
        }

        $headers = [
            'Authorization' => 'Bearer ' . $access_token,
        ];
        // Send request
        $client = new Client([
            'headers' => $headers,
        ]);
        $api_url = config('aimsia.api_url') . '/api/v1' . $endpoint;
        
       try {
            if ($method == 'POST') {
                $res = $client->request($method, $api_url, [
                    'form_params' => $form
                ]);
            } else {
                $res = $client->request($method, $api_url, [
                    'query' => $form
                ]);
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $res = $e->getResponse();
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $res = $e->getResponse();
        }
        $res_content = $res->getBody()->getContents();

        DB::table('aimsia_api')->insert([
            'method' => $method,
            'url' => $api_url,
            'request' => json_encode($form),
            'response' => $res_content,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return json_decode($res_content);
    }

    private function generateAccessToken(): ?string {
        try {
            $body = [
                'grant_type' => 'password',
                'client_id' => config('aimsia.api_client_id'),
                'client_secret' => config('aimsia.api_client_secret'),
                'username' => config('aimsia.api_username'),
                'password' => config('aimsia.api_password'),
                'scope' => 'Auth-User'
            ];
            // Send request
            $client = new Client();
            $api_url = config('aimsia.api_url') . '/oauth/token';
            $res = $client->request('POST', $api_url, [
                'form_params' => $body
            ]);
    
            if ($res->getStatusCode() < Response::HTTP_MULTIPLE_CHOICES) {
                $body = json_decode($res->getBody()->getContents());
                return $body->access_token ?? null;
            }
            return null;
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            return null;
        }
    }
}