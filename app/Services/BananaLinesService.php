<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use App\Services\Contracts\FerryServiceInterface;

class BananaLinesService implements FerryServiceInterface
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getItineraries()
    {
        // Cache for 60 minutes to optimize performance
        return Cache::remember('banana_itineraries', now()->addMinutes(60), function () {
            $response = $this->client->get('https://fat3lw9sr6.execute-api.eu-west-3.amazonaws.com/prod/trips/banana');
            $data = json_decode($response->getBody(), true);
            return $this->mapItineraries($data);
        });
    }

    public function getPrices(array $request)
{
    // Build the query parameters from the request
    $queryParams = [
        'tripId' => $request['itineraryId'], // Assuming itineraryId corresponds to tripId
        'adults' => $request['pricePerPassenger']['adults'],
        'children' => $request['pricePerPassenger']['children']
    ];

    // Make the GET request with query parameters
    try {
        $response = $this->client->get('https://fat3lw9sr6.execute-api.eu-west-3.amazonaws.com/prod/trips/banana', [
            'query' => $queryParams
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
        return collect($data)->map(function ($trip) {
            $departureDateTime = \Carbon\Carbon::createFromFormat('d-m-Y H:i', $trip['date'] . ' ' . $trip['departsAt']);
            return [
                'itineraryId' => $trip['tripId'],
                'originPortCode' => 'HAV', // Example static data for Havana origin
                'destinationPortCode' => 'BAN', // Example static data for Banana destination
                'operatorCode' => 'banana',
                'operatorName' => 'Banana Lines',
                'vesselName' => $trip['vessel'],
                'departureDateTime' => $departureDateTime->toDateTimeString(),
                'arrivalDateTime' => $departureDateTime->addMinutes($trip['tripDuration'])->toDateTimeString(),
                'pricePerPassengerType' => [
                    [
                        'passengerType' => 'AD',
                        'passengerPriceInCents' => $trip['adults'] * 100 // Example price per adult
                    ],
                    [
                        'passengerType' => 'CH',
                        'passengerPriceInCents' => $trip['children'] * 100 // Example price per child
                    ]
                ]
            ];
        })->toArray();
    }

}
