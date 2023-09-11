<?php

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Session;
use Log;
use Symfony\Component\HttpFoundation\Response;

class AimsiaApi {
    const ACCESS_TOKEN_SESSION_KEY = 'access_token';

    public function sendRequest($method, $endpoint, $form):object {
        if (Session::has(self::ACCESS_TOKEN_SESSION_KEY)) {
            $access_token = Session::get(self::ACCESS_TOKEN_SESSION_KEY);
            Log::info('if');
        } else {
            $access_token = $this->generateAccessToken();
            Session::put(self::ACCESS_TOKEN_SESSION_KEY, $access_token);
            Log::info('else');
        }
        // $access_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5OTFiYmJiNC1kMWQ1LTQzZGQtYTIwMi05ODdkNmJmNzViZDgiLCJqdGkiOiI3NjBmNmNmYmY3NjZiODMzYzllYmZhNDY4OWQ1OTViZjc5ODBkOTFiZDZlNjk5NmVlNzA0Y2ZhZDQwZmJhMDkwNDY4OGM3NzhjMWRiMGU2MiIsImlhdCI6MTY5NDMzMDc2OC43MjIyODEsIm5iZiI6MTY5NDMzMDc2OC43MjIyODMsImV4cCI6MTY5NDQxNzE2OC42Njc2NTYsInN1YiI6IjEiLCJzY29wZXMiOlsiQXV0aC1Vc2VyIl19.wZKavmdd7btCaloHyqwegblFG1mvGxMPpOwswMwk4b_a-utRAn3DMG8VxggvK4KcRM1x2fRgK9eYCPdZDzIdfIr52v0pXCQ2N0aVbeoJSJ_t3aZn92pDRHLVQrBdH3iD09IFMLtnmVDnP56lZ2KoicXFXUAq9_-cMrWOU1cJXK8pNUqL9cwzQzNzxr-N5xC4wZivvjK3zCHiWdZPVFXQADTD5K5lzeUlxRq23zc_WIMZKVJ3eneHOuTSUc8UunabPFeuerq9ljpjh664dQPQwPtIXd3NPC5eERBpBRaYszyYaQNfYexOEwHghGguaqiW6VZEDv8t-fxJ_pMBAeS59URU_d3BYF5xoilvetlejzLst84TKBuNAdsHgHP6oaogCdcxpOmSQcX5L1tUwErSQ2m9aZGXJxP0XAWVUSJIJY8rKtIoX4vlx8Mfpaf2qFHVVSy_Qy6Z3Z8F9meHKz9rWyi2wYJlH47QfdOOwTFm7RceA4mp-r-Gt1NndNfhazZWhgDr1RGr0txROSAul_XQGUzA5814BmNztXjOH1oVbSyg1tasUu5Siv2dnJD18i2jxfCmg2otyNYWIuDa--8DFONfuKIlROcE9f1BsIK5clIdsKYTSS_d6_l8IJ_l64BoOzzL2nHrTK8q9O23i7KbFS7xqe7ZWByBrurQE2n49dU';

        $headers = [
            'Authorization' => 'Bearer ' . $access_token,
        ];
        // Send request
        $client = new Client([
            'headers' => $headers,
        ]);
        $api_url = config('aimsia.api_url') . '/api/v1' . $endpoint;
        
       try {
            $res = $client->request($method, $api_url, [
                'form_params' => $form
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $res = $e->getResponse();
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $res = $e->getResponse();
        }

        return json_decode($res->getBody()->getContents());
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