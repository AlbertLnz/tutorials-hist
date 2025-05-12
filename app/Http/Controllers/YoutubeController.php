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

            foreach ($videos as $video) {
                $video->videoCategoryTxt = $this->parseVideoCategory($video->category);
            }

            // dd($videos);

            $inProgress = $videos->where('pivot.status', 'In Progress')->count();
            $todo = $videos->where('pivot.status', 'Todo')->count();
            $completed = $videos->where('pivot.status', 'Completed')->count();

            return view('dashboard', [
                'user' => $user,
                'videos' => $videos,
                'inProgress' => $inProgress,
                'todo' => $todo,
                'completed' => $completed
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

    private function parseVideoCategory($categoryNum)
    {
        switch ($categoryNum) {
            case 1:
                return 'Film & Animation';
            case 2:
                return 'Cars & Vehicles';
            case 10:
                return 'Music';
            case 15:
                return 'Pets & Animals';
            case 17:
                return 'Sports';
            case 18:
                return 'Short Movies';
            case 19:
                return 'Travel & Events';
            case 20:
                return 'Gaming';
            case 21:
                return 'Videoblogging';
            case 22:
                return 'People & Blogs';
            case 23:
                return 'Comedy';
            case 24:
                return 'Entertainment';
            case 25:
                return 'News & Politics';
            case 26:
                return 'How-to & Style';
            case 27:
                return 'Education';
            case 28:
                return 'Science & Technology';
            case 29:
                return 'Non-profits & Activism';
            case 30:
                return 'Movies';
            case 31:
                return 'Anime/Animation';
            case 32:
                return 'Action/Adventure';
            case 33:
                return 'Classics';
            case 34:
                return 'Comedy';
            case 35:
                return 'Documentary';
            case 36:
                return 'Drama';
            case 37:
                return 'Family';
            case 38:
                return 'Foreign';
            case 39:
                return 'Horror';
            case 40:
                return 'Sci-Fi/Fantasy';
            case 41:
                return 'Thriller';
            case 42:
                return 'Shorts';
            case 43:
                return 'Shows';
            case 44:
                return 'Trailers';
            default:
                return 'Unknown';
        }
    }
}
