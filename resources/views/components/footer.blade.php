{{-- php artisan make:component Footer --}}

<footer
    class="relative bg-linear-to-t from-[#2F313F] to-[#0d0d0d] flex justify-between items-center px-20 pb-10 pt-16 text-white">
    <div class="absolute inset-0 bg-repeat bg-left-top pointer-events-none z-0"
        style="background-image: url('https://framerusercontent.com/images/6mcf62RlDfRfU61Yg5vb2pefpi4.png'); background-size: 153.6px auto; opacity: 0.08;"
        aria-hidden="true"></div>

    <div class="flex items-center space-x-2">
        <p>Made by <span class="font-semibold"><a href="" class="hover:text-blue-400 transition-colors">Albert
                    Lanza</a></span></p>
    </div>
    <div class="flex items-center space-x-2">
        <p>Github: <a href="https://github.com/albertlanza" class="hover:text-blue-400 transition-colors">Albert Lanza
                GitHub</a></p>
    </div>
    <div class="text-sm">
        <p>&copy; {{ date('Y') }} My App. All rights reserved.</p>
    </div>
</footer>