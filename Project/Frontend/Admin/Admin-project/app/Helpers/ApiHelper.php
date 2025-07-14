<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiHelper
{
    /**
     * Send API Request
     *
     * @param string $endpoint
     * @param array  $payload
     * @param string $method
     * @param array  $headers
     * @return array
     */
    public static function sendApiRequest($endpoint, $payload = [], $method = 'POST', $headers = [])
    {
        $apiBaseUrl = config('services.hygeiaes.api_url');
        $url = rtrim($apiBaseUrl, '/') . '/' . ltrim($endpoint, '/');

        $defaultHeaders = [
            'Accept' => 'application/json',
        ];

        try {
            $response = Http::withHeaders(array_merge($defaultHeaders, $headers))
                ->{$method}($url, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json()['message'] ?? 'API request failed.',
                'status_code' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('API Request Error', [
                'url' => $url,
                'method' => $method,
                'payload' => $payload,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'An unexpected error occurred.',
                'exception' => $e->getMessage(),
            ];
        }
    }
}
