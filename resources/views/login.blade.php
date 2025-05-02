<p>Login through email</p>
<form action="{{ route('login.email.attempt') }}" method="POST">
  @csrf

  @if ($errors->any())
    <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
    </ul>
  @endif

  <input type="email" name="email" placeholder="albert@example.com" />
  <input type="password" name="password" placeholder="******">

  <button>Submit</button>
</form>

<p>Login through username</p>
<form action="{{ route('login.username.attempt') }}" method="POST">
  @csrf

  @if ($errors->any())
    <ul>
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
    </ul>
  @endif

  <input type="text" name="username" placeholder="albertlnz" />
  <input type="password" name="password" placeholder="******">

  <button>Submit</button>
</form>