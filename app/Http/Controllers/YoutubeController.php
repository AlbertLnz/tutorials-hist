<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Video;

class YoutubeController extends Controller
{

    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'yt-url' => ['required', 'string'],
        ]);

        $parsed = $this->parseYoutubeUrl($validated['yt-url']);
        $videoId = $parsed['videoId'];
        $timestamp = $parsed['timestamp'];
        $status = $timestamp ? 'In Progress' : 'Todo';

        $info = $this->getYoutubeAPIData($videoId);

        Video::updateOrCreate(
            ['id' => $videoId],
            [
                'title' => $info['items'][0]['snippet']['title'],
                'description' => $info['items'][0]['snippet']['description'],
                'channelId' => $info['items'][0]['snippet']['channelId'],
                'channelTitle' => $info['items'][0]['snippet']['channelTitle'],
                'category' => $info['items'][0]['snippet']['categoryId'],
                'publishedAt' => $info['items'][0]['snippet']['publishedAt']
            ]
        );

        // Actualizar la tabla pivote
        $user = auth()->user();
        $user->videos()->syncWithoutDetaching([
            $videoId => ['status' => $status, 'timestamp' => $timestamp]
        ]);

        return response()->json(['message' => 'Video asociado correctamente con el usuario']);
    }

    private function getYoutubeVideoId($url)
    {
        preg_match(
            '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i',
            $url,
            $matches
        );
        return $matches[1] ?? null;
    }

    private function getYoutubeVideoImg($videoId)
    {
        return 'https://img.youtube.com/vi/' . $videoId . '/hqdefault.jpg';
    }

    private function getYoutubeAPIData($videoId)
    {
        $apiKey = env('YOUTUBE_API_KEY');
        $url = 'https://www.googleapis.com/youtube/v3/videos';

        $response = Http::get($url, [
            'id' => $videoId,
            'part' => 'snippet,statistics,recordingDetails',
            'key' => $apiKey,
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            return [
                'error' => 'API request failed.',
                'status' => $response->status(),
                'body' => $response->body()
            ];
        }
    }

    private function parseYoutubeUrl($url)
    {
        $videoId = null;
        $timestamp = null;

        $parsedUrl = parse_url($url);

        // Short YT URL: "https://youtu.be/VIDEO_ID?t=174"
        if (isset($parsedUrl['host']) && ($parsedUrl['host'] === 'youtu.be')) {
            $videoId = ltrim($parsedUrl['path'], '/');
        }

        // Long YT URL: "https://www.youtube.com/watch?v=VIDEO_ID&t=174"
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);

            if (isset($queryParams['v'])) {
                $videoId = $queryParams['v'];
            }

            if (isset($queryParams['t'])) {
                $timestamp = $queryParams['t'];
            }
        }

        // Medium YT URL: "https://youtube.com/Lk_lNSfa3kk?t=174"
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryParams);

            if (isset($queryParams['t'])) {
                $timestamp = $queryParams['t'];
            }
        }

        return [
            'videoId' => $videoId,
            'timestamp' => $timestamp,
        ];
    }
}
