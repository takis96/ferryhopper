# Ferry Booking API

## Overview
This project implements a Laravel-based (which I decided to use) API for booking ferry trips. It interacts with two ferry companies: Havana Ferries and Banana Lines.

## Features
- **Get Itineraries**: Fetches available itineraries for both Havana Ferries and Banana Lines.
- **Get Prices**: Retrieves pricing information based on itineraries and passenger types.
- **Caching**: Utilizes Laravel's caching to optimize performance for fetching itineraries.


## Authentication Problem
When making requests to the pricing endpoints, you may encounter a **403 Missing Authentication Token** error. This is typically due to authentication requirements on the API. Ensure that you provide any necessary tokens or credentials. I did not have any information about this and could not surpass it. If you have any, it will be simple to correct it.

## Installation
1. Clone the repository.
2. Run `composer install` to install dependencies.
3. Set up your `.env` file and database configuration.
4. Run `php artisan migrate` to set up the database.

## Usage
Use Postman or any HTTP client to test the API endpoints. Make sure to include the required parameters in your requests.

