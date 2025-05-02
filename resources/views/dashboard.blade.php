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