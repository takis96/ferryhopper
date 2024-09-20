<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use App\Services\BananaLinesService;

class BananaLinesServiceTest extends TestCase
{
    public function test_banana_itineraries_are_fetched_and_cached()
    {
        // Mock the Cache::remember() method to simulate caching
        Cache::shouldReceive('remember')
            ->once()
            ->with('banana_itineraries', \Mockery::any(), \Mockery::type('Closure'))
            ->andReturn([
                [
                    'tripId' => 1,
                    'vessel' => 'Joey',
                    'operatorName' => 'Banana Lines'
                ]
            ]);

        // Mock the Guzzle Client response
        $clientMock = $this->createMock(Client::class);
        $responseMock = new Response(200, [], json_encode([
            [
                'tripId' => 1,
                'vessel' => 'Joey',
                'adults' => 6,
                'children' => 5,
                'date' => '22-6-2020',
                'departsAt' => '13:00',
                'tripDuration' => 40
            ]
        ]));

        $clientMock->method('get')->willReturn($responseMock);

        // Call the actual service
        $service = new BananaLinesService($clientMock);
        $itineraries = $service->getItineraries();

        // Assert the response structure
        $this->assertIsArray($itineraries);
        $this->assertEquals(1, $itineraries[0]['tripId']);
        $this->assertEquals('Joey', $itineraries[0]['vessel']);
        $this->assertEquals('Banana Lines', $itineraries[0]['operatorName']);
    }
}
