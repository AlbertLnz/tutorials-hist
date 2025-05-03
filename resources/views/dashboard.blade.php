<h1>
  Welcome, {{ Auth::user()->name }} to the private dashboard
</h1>

<form action="{{ route('logout') }}" method="POST">
  @csrf
  <button>
    Logout
  </button>
</form>

<form action="{{ route('yt.url.attempt') }}" method="POST">
  @csrf
  <input type="text" name="yt-url" placeholder="https://www.youtube.com/watch?v=Lk_lNSfa3kk">
</form>

<h2>Mis videos</h2>

@if (isset($videos) && $videos->count() > 0)
  <table border="1" cellpadding="5" cellspacing="0">
    <thead>
    <tr>
      <th>Miniatura</th>
      <th>Título</th>
      <th>Descripción</th>
      <th>Categoría</th>
      <th>Status</th>
      <th>Status Bar</th>
      <th>Nombre Canal</th>
      <th>Publicado En</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($videos as $video)
    <tr>
      <td>
        <a href="https://www.youtube.com/watch?v={{ $video->pivot->video_id }}&t={{ $video->pivot->timestamp }}" target="_blank">
          <img src="https://i.ytimg.com/vi/{{ $video->pivot->video_id }}/default.jpg" alt="Foto video">
        </a>
      </td>
      <td>{{ $video->title }}</td>
      <td>{{ $video->description }}</td>
      <td>{{ $video->category }}</td>
      <td>{{ $video->pivot->status }}</td>
      <td>
        <progress value="{{ $video->pivot->timestamp ?? 0 }}" max="{{ $video->seconds }}"  />
      </td>
      <td>{{ $video->channelTitle }}</td>
      <td>{{ $video->publishedAt }}</td>
    </tr>
    @endforeach
    </tbody>
  </table>
@else
  <p>No tienes videos aún.</p>
@endif