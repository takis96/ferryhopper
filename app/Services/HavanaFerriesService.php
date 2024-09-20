<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use App\Services\Contracts\FerryServiceInterface;
use Illuminate\Support\Facades\Log;


class HavanaFerriesService implements FerryServiceInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getItineraries()
    {
        // Cache for 60 minutes to optimize performance
        return Cache::remember('havana_itineraries', now()->addMinutes(60), function () {
            try {
                $response = $this->client->get('https://fat3lw9sr6.execute-api.eu-west-3.amazonaws.com/prod/trips/havana');
                $data = json_decode($response->getBody(), true);
        
                if (isset($data['trips'])) {
                    return $this->mapItineraries($data);
                }
        
                // Handle unexpected response structure
                throw new \Exception('Unexpected response structure');
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return 
                [
                    'status' => false,
                    'errorCode' => 'ITINERARY_FETCH_ERROR',
                    'errorDescription' => 'Could not fetch itineraries.'
                ]; // or handle the error accordingly
            }
        });
    }

    public function getPrices(array $request)
{
    // No caching for prices to ensure real-time accuracy
    try {
        $response = $this->client->post('https://fat3lw9sr6.execute-api.eu-west-3.amazonaws.com/prod/prices/havana', [
            'json' => [
                'itinerary' => $request['itineraryId'], // itinerary ID from request
                'passengers' => [
                    ['type' => 'AD', 'passenger' => $request['pricePerPassenger']['adults']],
                    ['type' => 'IN', 'passenger' => $request['pricePerPassenger']['children']]
                ]
            ]
        ]);
        
        return json_decode($response->getBody(), true);
    } catch (\Exception $e) {
        Log::error('General Exception: ' . $e->getMessage());
        
        return [
            'status' => false,
            'errorCode' => 'GENERAL_EXCEPTION',
            'errorDescription' => 'Could not fetch prices.'
        ];
    }
}

    


    private function mapItineraries($data)
    {
        return collect($data['trips'])->map(function ($trip) {
            return [
                'itineraryId' => $trip['itinerary'],
                'originPortCode' => 'HAV', // Example for Havana Ferries origin
                'destinationPortCode' => 'BAN', // Example for destination
                'operatorCode' => 'havana',
                'operatorName' => 'Havana Ferries',
                'vesselName' => $trip['vesselName'],
                'departureDateTime' => \Carbon\Carbon::createFromFormat('Ymd H:i', $trip['date'] . ' ' . $trip['departure'])->toDateTimeString(),
                'arrivalDateTime' => \Carbon\Carbon::createFromFormat('Ymd H:i', $trip['date'] . ' ' . $trip['arrival'])->toDateTimeString(),
                'pricePerPassengerType' => [
                    [
                        'passengerType' => 'AD',
                        'passengerPriceInCents' => $data['prices']['AD']
                    ],
                    [
                        'passengerType' => 'CH',
                        'passengerPriceInCents' => $data['prices']['CH']
                    ],
                    [
                        'passengerType' => 'IN',
                        'passengerPriceInCents' => $data['prices']['IN']
                    ]
                ]
            ];
        })->toArray();
    }
}
