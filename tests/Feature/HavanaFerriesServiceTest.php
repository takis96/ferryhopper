<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use App\Services\HavanaFerriesService;

class HavanaFerriesServiceTest extends TestCase
{
    public function test_havana_itineraries_are_fetched_and_cached()
    {
        // Simulate Cache: Ensure it stores the itineraries correctly
        Cache::shouldReceive('remember')
            ->once()
            ->with('havana_itineraries', \Mockery::any(), \Mockery::type('Closure'))
            ->andReturn([
                [
                    'itineraryId' => 1,
                    'vesselName' => 'Vessel A',
                    'operatorName' => 'Havana Ferries'
                ]
            ]);

        // Mock the Guzzle Client response
        $clientMock = $this->createMock(Client::class);
        $responseMock = new Response(200, [], json_encode([
            'trips' => [
                [
                    'itinerary' => 1,
                    'vesselName' => 'Vessel A',
                    'date' => '20200622',
                    'departure' => '10:00',
                    'arrival' => '11:00'
                ]
            ],
            'prices' => [
                'AD' => '500',
                'CH' => '400',
                'IN' => '300'
            ]
        ]));

        $clientMock->method('get')->willReturn($responseMock);

        // Call the actual service
        $service = new HavanaFerriesService($clientMock);
        $itineraries = $service->getItineraries();

        // Assert the response structure
        $this->assertIsArray($itineraries);
        $this->assertEquals(1, $itineraries[0]['itineraryId']);
        $this->assertEquals('Vessel A', $itineraries[0]['vesselName']);
        $this->assertEquals('Havana Ferries', $itineraries[0]['operatorName']);
    }
}
