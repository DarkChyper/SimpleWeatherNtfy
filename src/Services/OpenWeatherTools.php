<?php

namespace src\Services;

class OpenWeatherTools
{
    public const WIND_SECTORS = [
        1 => 'Nord',
        2 => 'Nord Nord-Est',
        3 => 'Nord-Est',
        4 => 'Est Nord-Est',
        5 => 'Est',
        6 => 'Est Sud-Est',
        7 => 'Sud-Est',
        8 => 'Sud Sud-Est',
        9 => 'Sud',
        10 => 'Sud Sud-Ouest',
        11 => 'Sud-Ouest',
        12 => 'Ouest Sud-Ouest',
        13 => 'Ouest',
        14 => 'Ouest Nord-Ouest',
        15 => 'Nord-Ouest',
        16 => 'Nord Nord-Ouest',
        17 => 'Nord'
    ];

    public static function getDailyWeather(
        string $url,
        string $token,
        string $lat,
        string $lon,
        string $units
    ): object
    {
        $finalUrl = $url
            . '?appId=' . $token
            . '&lat=' . $lat
            . '&lon=' . $lon
            . '&units=' . $units
            . '&exclude=current,minutely,hourly&lang=fr';

        $ch = curl_init($finalUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $response = json_decode($response, false);
        curl_close($ch);

        return $response;
    }

    public static function getCardinalWindFromDegrees(int $degrees): string
    {
        return self::WIND_SECTORS[round($degrees % 360 / 22.5) + 1];
    }

    public static function metersSecondToKilometersHour(float $metersSeconds): float
    {
        return $metersSeconds * 3.6;
    }
}