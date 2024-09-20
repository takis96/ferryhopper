<?php

namespace App\Services\Contracts;

interface FerryServiceInterface
{
    public function getItineraries();
    public function getPrices(array $request);
}
