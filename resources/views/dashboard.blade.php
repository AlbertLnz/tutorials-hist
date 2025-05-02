<h1>
  Welcome, {{ Auth::user()->name }} to the private dashboard
</h1>

<form action="{{ route('logout') }}" method="POST">
  @csrf
  <button>
    Logout
  </button>
</form>