<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class GeocodingService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GOOGLE_MAPS_API_KEY');
    }

    public function getCoordinates($country, $state, $city, $pincode, $area, $address)
    {
        try {
            $fullAddress = implode(', ', array_filter([$address, $area, $city, $state, $pincode, $country]));

            \Log::info('Geocoding address: ' . $fullAddress);

            $response = $this->client->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'query' => [
                    'address' => $fullAddress,
                    'key' => $this->apiKey,
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            \Log::info('Geocoding API response: ' . json_encode($data));

            if ($data['status'] == 'OK') {
                $location = $data['results'][0]['geometry']['location'];
                return [
                    'latitude' => $location['lat'],
                    'longitude' => $location['lng'],
                ];
            } else {
                \Log::warning('Geocoding failed for address: ' . $fullAddress . '. Status: ' . $data['status'] . '. Error message: ' . (isset($data['error_message']) ? $data['error_message'] : 'No error message provided.'));
            }

            return null;
        } catch (RequestException $e) {
            \Log::error('Geocoding API request failed: ' . $e->getMessage());
            return null;
        }
    }
}
