<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class GuzzleHttpClient
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api-admin.hygeiaes.com',
            'timeout'  => 10.0,
        ]);
    }

    public function request($method, $uri, $options)
    {
        try {
          

            $response = $this->client->request($method, $uri, $options);

            $responseBody = json_decode($response->getBody()->getContents(), true);
            $statusCode = $response->getStatusCode();

         

            return $this->handleApiResponse($statusCode, $responseBody);
        } catch (RequestException $e) {
           
            return $this->handleError('Request failed: ' . $e->getMessage());
        } catch (\Exception $e) {
          
            return $this->handleError('An unexpected error occurred: ' . $e->getMessage());
        }
    }

    private function handleApiResponse($statusCode, $responseBody)
    {
        switch ($statusCode) {
            case 200:
            case 201:
                return [
                    'success' => true,
                    'message' => $responseBody['message'] ?? 'Request was successful.',
                    'data'    => $responseBody,
                ];

            case 400:
                return $this->handleError('Bad Request. The request was invalid or cannot be served.');
            case 401:
                return $this->handleError('Unauthorized. Authentication credentials were missing or incorrect.');
            case 403:
                return $this->handleError('Forbidden. The server refuses to authorize it.');
            case 404:
                return $this->handleError('Not Found. The requested resource could not be found.');
            case 500:
                return $this->handleError('Internal Server Error. Something went wrong on the server.');
            default:
                return $this->handleError('Unexpected status code: ' . $statusCode);
        }
    }

    private function handleError($message)
    {
    
        return [
            'success' => false,
            'error'   => $message,
        ];
    }
}
