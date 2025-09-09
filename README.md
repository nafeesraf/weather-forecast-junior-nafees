# Weather Forecast (Modified Project)

This project provides a 5-day weather forecast with:
- A web UI (React + Vite inside Laravel) with a dropdown for **Brisbane**, **Gold Coast**, **Sunshine Coast** that reactively updates.
- A console command: `php artisan forecast {cities?*}` which shows a tabulated 5-day forecast.
- Integrates a 3rd-party weather API (Weatherbit) as suggested in the test.
- **Fixes detail has been provided on 09 Seo 2025 Email**:
  ## API key exposed on frontend
- This was a big security issue. The key was hardcoded in both frontend and backend, with the frontend making direct API calls where anyone could see the key. I've fixed this by:
- Moving all API calls to the backend through proper Laravel routes
- Creating a proper WeatherService in App\Services (and removing the confused one from app\Http\Service)
-  Applied SOLID principles (Single responiblity , Open–closed , Liskov substitution,  Interface segregation, Dependency inversion)  throughout, including moving the city name normalisation to a helper class in App\Helpers since it's reusable functionality

## code duplication

- There were two nearly identical services and React components doing the same thing. I've cleaned this up by:
- Simplifying the React component in Forecast.jsx to only make backend requests
- Creating a single backend service that handles requests from both the React frontend and console commands
  Overly complicated maths - The logic for simple calculations was way more verbose than needed. I've simplified the React file, WeatherService, CityHelper, and ForecastCommand while keeping everything readable and easy to debug.

## No caching

- Since the requirements weren't specific about what type of caching (Redis, Memcache, etc.), I've stuck with Laravel's built-in caching. I used Cache::remember instead if Cache::flexible and created an environment variable for cache time so you can easily adjust it in the .env file. By default it's set to 0 seconds (no caching), but you can change it using the formula: 1 Day = 60 × 60 × 24 = 86400 seconds.

## Additional improvements:
- Fixed Tailwind CSS implementation and applied it properly to React components
- Simplified HTML logic throughout
- Created test cases for the service and helper classes (the WeatherController and ForecastCommand still need unit tests)
- Built a layout component for global use
- One observation: while React is fantastic, it's not great for SEO or helping AI understand page content for LLMS
- No CI\CD no automation testing like selenium

## Getting Started

### 0) Copy the files from this repository
```bash
git clone "https://github.com/nafeesraf/weather-forecast-junior-nafees.git"
```

### 1) Copy the files from this repository
Replace/add the files as laid out in the tree. If you already created the Laravel app above, put files into matching paths.

### 2) Other commands 
```bash
cd weather-forecast-junior-nafees

composer install

cp .env.example .env or copy .env.example .env (Based on your local enviroment) or copy the following line to your existing .env file 

#Weather API key and URL
APP_WEATHER_KEY=XXXXXXXXXXXXXX
APP_WEATHER_URL="https://api.weatherbit.io/v2.0/forecast/daily"
APP_CACHE_TIME=0  # 1 Day = 60 * 60 * 24  = 86400


npm install 

php artisan key:generate

php artisan migrate

php artisan db:seed

php artisan serve

In another: Navigaate to weather-forecast-junior-nafees and Run 

npm run dev


```


GO to https://www.weatherbit.io/api read the documentation and signup for API key once you got the API keu Navigate to root folder open .env file Paste your key

APP_WEATHER_KEY=XXXXXXXXXXXXXX


visit Front viewe http://127.0.0.1:8000/

Back end http://127.0.0.1:8000/api/forecast?city=Brisbane

### 5) Console command
```bash
php artisan forecast
# or
php artisan forecast Brisbane "Gold Coast" "Sunshine Coast"
```

## Assumptions & Notes

- NO Cache user can enable cache based on time by changing the value APP_CACHE_TIME
- No CI\CD no automation testing like selenium
- 2 only classes unit Test case
## Tech

- Laravel (PHP) backend 8.0.X, Vite + React frontend.
- Tailwind.
- CURL config with SSL 
