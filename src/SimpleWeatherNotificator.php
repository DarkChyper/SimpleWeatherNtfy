<?php
namespace src;

use src\Services\DotEnv;
use src\Services\NtfyTools;
use src\Services\OpenWeatherTools;
use function getenv;

ini_set('display_errors', 1);


spl_autoload_register(static function(string $fqcn) {
    $path = '../'.str_replace('\\', '/', $fqcn).'.php';
    require_once($path);
});

function main() {
    (new DotEnv(__DIR__ . '/../.env.local'))->load();

    $weather = OpenWeatherTools::getDailyWeather(
        getenv('OWM_URL'),
        getenv('OWM_TOKEN'),
        getenv('OWM_LAT'),
        getenv('OWM_LON'),
        getenv('OWM_UNIT')
    );

    $weatherTomorrow = $weather->daily[1];

    $date = new \DateTime('@'.$weatherTomorrow->dt);
    $sunrise = new \DateTime('@'.$weatherTomorrow->sunrise);
    $sunset = new \DateTime('@'.$weatherTomorrow->sunset);
    $sunriseTxt = $sunrise->format('H:i:s');
    $sunsetTxt = $sunset->format('H:i:s');

    $title = $date->format('d/m/Y'). ' à ' . getenv('OWN_CITY');

    $weatherDescription = $weatherTomorrow->weather[0]->description;
    $tempMin = $weatherTomorrow->temp->min;
    $tempMax = $weatherTomorrow->temp->max;
    $feelNight = $weatherTomorrow->feels_like->night;
    $feelDay = $weatherTomorrow->feels_like->day;
    $humidity = $weatherTomorrow->humidity;
    $windSpeed = OpenWeatherTools::metersSecondToKilometersHour($weatherTomorrow->wind_speed);
    $windDirection = OpenWeatherTools::getCardinalWindFromDegrees($weatherTomorrow->wind_deg);

    $rainProb = $weatherTomorrow->pop * 100;

    $message =
        "$weatherDescription
        Température min : $tempMin °C
        Température max : $tempMax °C
        
        Probabilité de précipitations : $rainProb%
        Vent $windSpeed km/h $windDirection
        
        Température ressentie : $feelNight °C => $feelDay °C
        Humidité : $humidity%
        
        Levé du soleil à $sunriseTxt
        Couché du soleil à $sunsetTxt";

    NtfyTools::sendNotify(
        getenv('NTFY_URL'),
        $title,
        $message,
        $weatherTomorrow->weather[0]->main,
        getenv('NTFY_USERNAME'),
        getenv('NTFY_PASSWORD')
    );
}

main();
