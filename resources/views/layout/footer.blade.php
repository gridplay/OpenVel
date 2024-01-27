<footer class="rounded-lg shadow m-4 bg-gray-800">
    <div class="w-full mx-auto max-w-screen-xl p-4 md:flex md:items-center md:justify-between">
      <span class="text-sm sm:text-center text-gray-400">© {{ date('Y') }} <a href="https://gridplay.net" class="hover:underline">GridPlay Productions</a>. All Rights Reserved.
        <br>Time is in Eastern Time ({{ date('T') }})
    </span>
    <ul class="flex flex-wrap items-center mt-3 text-sm font-medium text-gray-400 sm:mt-0">
        <li>
            <a href="https://gridplay.net/privacy" class="hover:underline me-4 md:me-6">GridPlay's Privacy Policy</a>
        </li>
        <li>
            <a href="{{ url('tos') }}" class="hover:underline me-4 md:me-6">Terms of Service</a>
        </li>
        <li>
            <a href="http://opensimulator.org" class="hover:underline me-4 md:me-6">
                <img src="{{ url('opensim.png') }}">
            </a>
        </li>
    </ul>
    </div>
</footer>
