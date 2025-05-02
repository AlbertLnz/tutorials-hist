<form action="{{ route('register.store') }}" method="POST">
  @csrf
  <input type="text" name="name" placeholder="Albert" />
  <input type="email" name="email" placeholder="albert@example.com" />
  <input type="password" name="password" placeholder="******">
  <button>Register</button>
</form>