<section class="relative px-20 bg-linear-to-b from-red-950 to-[#0d0d0d] text-white">
  <div class="absolute inset-0 bg-repeat bg-left-top pointer-events-none z-0"
    style="background-image: url('https://framerusercontent.com/images/6mcf62RlDfRfU61Yg5vb2pefpi4.png'); background-size: 153.6px auto; opacity: 0.08;"
    aria-hidden="true"></div>

  <header class="relative z-10 flex justify-between w-full pt-8 pb-12 px-8">
    <p>My ICON</p>

    <h1 class="text-2xl font-semibold">YouDevTube</h1>

    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button class="cursor-pointer">
        Logout
      </button>
    </form>
  </header>
</section>