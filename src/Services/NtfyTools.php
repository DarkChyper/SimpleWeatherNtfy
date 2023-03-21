<?php

namespace src\Services;

class NtfyTools
{
    public static function sendNotify(
        string $url,
        string $title,
        string $content,
        string $tags,
        string $username,
        string $password

    ): bool|string
    {
        $ch = curl_init($url);

        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization: Basic ' . base64_encode($username . ':' . $password),
                'Title: ' . $title,
                'Tags: ' . NtfyTools::tagsToEmojiTags($tags),
                'Content-Type: text/plain'
            ),
            CURLOPT_POSTFIELDS => $content
        ));
        return curl_exec($ch);
    }

    public static function tagsToEmojiTags(string $tags): string
    {
        $tagsToReturn = '';
        $arrayTags = explode(',', $tags);
        foreach ($arrayTags as $tag) {
            $tagToReturn = match ($tag) {
                'Clear' => 'sunny',
                'Clouds' => 'cloud',
                'Fog' => 'fog',
                'Rain' => 'cloud_with_rain',
                'snow' => 'cloud_with_snow',
                'Thunderstorm' => 'cloud_with_lightning_and_rain',
                'Tornado' => 'tornado',
                default => $tag
            };

            if (!empty($tagsToReturn)) {
                $tagsToReturn .= ',';
            }
            $tagsToReturn .= $tagToReturn;
        }

        return $tagsToReturn;
    }
}