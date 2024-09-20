<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HavanaFerriesService;
use App\Services\BananaLinesService;

class FerryController extends Controller
{
    protected $havanaService;
    protected $bananaService;
    
    public function __construct(HavanaFerriesService $havanaService, BananaLinesService $bananaService)
    {
        $this->havanaService = $havanaService;
        $this->bananaService = $bananaService;
    }

    public function getItineraries()
    {
        $havanaItineraries = $this->havanaService->getItineraries();
        $bananaItineraries = $this->bananaService->getItineraries();
        return response()->json(['itineraries' => array_merge($havanaItineraries, $bananaItineraries)]);
    }

    public function getPrices(Request $request)
    {
        $service = $request->operatorCode === 'havana' ? $this->havanaService : $this->bananaService;
        $prices = $service->getPrices($request->all());
        return response()->json($prices);
    }
}
