<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use DateInterval;

class YoutubeController extends Controller
{

    public function post_new_yt_video(Request $request)
    {
        $validated = $request->validate([
            'yt-url' => ['required', 'string'],
        ]);

        $parsed = $this->parseYoutubeUrl($validated['yt-url']);
        $videoId = $parsed['videoId'];
        $timestamp = $parsed['timestamp'];
        $status = $timestamp ? 'In Progress' : 'Todo';

        $info = $this->getYoutubeAPIData($videoId);

        // dd($info);

        Video::updateOrCreate(
            ['id' => $videoId],
            [
                'title' => $info['items'][0]['snippet']['title'],
                'description' => $info['items'][0]['snippet']['description'],
                'channelId' => $info['items'][0]['snippet']['channelId'],
                'channelTitle' => $info['items'][0]['snippet']['channelTitle'],
                'category' => $info['items'][0]['snippet']['categoryId'],
                'seconds' => $this->durationToSeconds($info['items'][0]['contentDetails']['duration']),
                'publishedAt' => $info['items'][0]['snippet']['publishedAt']
            ]
        );

        // Actualizar la tabla pivote
        $user = auth()->user();
        $user->videos()->syncWithoutDetaching([
            $videoId => ['status' => $status, 'timestamp' => $timestamp]
        ]);

        return redirect()->route('dashboard');
        // return response()->json(['message' => 'Video asociado correctamente con el usuario']);
    }

    public function get_all_user_videos(Request $request)
    {
        if (Auth::user()->id) {

            $user = User::find(Auth::user()->id);
            $videos = $user->videos()->get();

            // dd($videos);

            return view('dashboard', [
                'user' => $user,
                'videos' => $videos
            ]);

            // return response()->json(['user' => $user, 'videos' => $videos], 200);
        }
    }

    // ----------------------------

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
            'part' => 'snippet,statistics,recordingDetails,contentDetails',
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

    private function durationToSeconds($duration)
    {
        $interval = new DateInterval($duration);
        $seconds = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
        return $seconds;
    }
}
