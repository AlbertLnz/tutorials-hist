@extends('layouts.layout')

@section('title', 'Dashboard')

@section('content')
  <section class="relative px-20 bg-[#0d0d0d] text-white pb-20">
    <div class="absolute inset-0 bg-repeat bg-left-top pointer-events-none z-0"
    style="background-image: url('https://framerusercontent.com/images/6mcf62RlDfRfU61Yg5vb2pefpi4.png'); background-size: 153.6px auto; opacity: 0.08;"
    aria-hidden="true"></div>

    <h1 class="text-4xl font-medium pb-10">
    Welcome back, {{ Auth::user()->name }}
    </h1>

    <form class="flex flex-col p-4 border border-gray-200 rounded-md hover:bg-[#121212]"
    action="{{ route('yt.url.attempt') }}" method="POST">
    @csrf
    <label class="text-xl" for="yt">
      Add New Video
    </label>
    <input id="yt" class="my-4 pb-2 outline-none" type="text" name="yt-url"
      placeholder="https://www.youtube.com/watch?v=Lk_lNSfa3kk">
    <p class="text-sm text-gray-300 italic">*Enter the URL of a YouTube video to add it to your dashboard.</p>
    </form>

    <div class="flex items-end gap-6 mt-12 mb-8">
    <h2 class="text-3xl font-medium">My videos ({{$videos->count()}})<h2>
      <div class="flex justify-center items-center gap-4 w-full text-xs">
        <p class="bg-yellow-900 rounded-full px-3 py-1">In progress: <span
          class="text-base font-semibold">{{$inProgress}}</span></p>
        <p class="bg-blue-900 rounded-full px-3 py-1">Todo: <span class="text-base font-semibold">{{$todo}}</span></p>
        <p class="bg-green-900 rounded-full px-3 py-1">Completed: <span
          class="text-base font-semibold">{{$completed}}</span></p>
      </div>
    </div>

    @if (isset($videos) && $videos->count() > 0)

    <table class="min-w-full border border-collapse border-gray-200 text-sm">
    <thead>
      <tr class="bg-black text-center text-base">
      <th class="px-4 py-2 border-b">Thumbnail</th>
      <th class="px-4 py-2 border-b">Title</th>
      <th class="px-4 py-2 border-b">Description</th>
      <th class="px-4 py-2 border-b">Category</th>
      <th class="px-4 py-2 border-b">Status</th>
      <th class="px-4 py-2 border-b">Progress</th>
      <th class="px-4 py-2 border-b">Channel Name</th>
      <th class="px-4 py-2 border-b">Published At</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($videos as $video)
      @php
    $timestamp = (float) ($video->pivot->timestamp ?? 0);
    $seconds = (float) ($video->seconds ?? 1);
    $percent = number_format(($timestamp / $seconds) * 100, 2);

    // dd($timestamp, $seconds, $percent)
    @endphp

      <tr class="hover:bg-[#111]">
      <td class="px-4 py-2 border-b">
      <a href="https://www.youtube.com/watch?v={{ $video->pivot->video_id }}&t={{ $video->pivot->timestamp }}"
      target="_blank">
      <img src="https://i.ytimg.com/vi/{{ $video->pivot->video_id }}/default.jpg" alt="Foto video">
      </a>
      </td>
      <td class="px-4 py-2 border-b">{{ $video->title }}</td>
      <td class="px-4 py-2 border-b">{{ \Str::limit($video->description, 80) }}</td>
      <td class="px-4 py-2 border-b text-center">{{ $video->videoCategoryTxt }}</td>
      <td class="px-4 py-2 border-b text-center">{{ $video->pivot->status }}</td>
      <td class="px-4 py-2 border-b">
      <label class="text-[#0EC043] font-medium">
      {{ $percent }}%
      <progress value="{{ (float) $video->pivot->timestamp ?? 0 }}" max="{{ $video->seconds }}" />
      </label>
      </td>
      <td class="px-4 py-2 border-b text-center">{{ $video->channelTitle }}</td>
      <td class="px-4 py-2 border-b text-center">{{ \Carbon\Carbon::parse($video->publishedAt)->format('F j, Y') }}
      </td>
      </tr>
    @endforeach
    </tbody>
    </table>
    @else
    <p>No tienes videos aún.</p>
    @endif

  </section>
@endsection