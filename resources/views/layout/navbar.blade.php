<nav class="bg-red-700 border-gray-200">
  <div class="max-w-screen-sm flex flex-wrap items-center justify-between mx-auto p-4">
    <a href="{{ url('/') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
        <span class="self-center text-2xl font-semibold whitespace-nowrap text-white">{{ env('APP_NAME') }}</span>
    </a>
    <!-- start user menu -->
    <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
        @auth
            @php
                $user = Auth::user();
                $prof = url('u/'.$user->firstname.'.'.$user->lastname);
                if ($user->lastname == "Resident") {
                    $prof = url('u/'.$user->firstname);
                }
            @endphp
          <button type="button" class="flex text-sm bg-red-800 rounded-full md:me-0 focus:ring-4 focus:ring-gray-300" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
            <span class="sr-only">Open user menu</span>
            <img class="w-8 h-8 rounded-full" src="{{ App\Models\Robust::getProfilePic($user->uuid) }}" alt="user photo">
          </button>
          <!-- Dropdown menu -->
          <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600" id="user-dropdown">
            <div class="px-4 py-3">
              <a href="{{ $prof }}"><span class="block text-sm text-gray-900 dark:text-white">{{ $user->firstname." ".$user->lastname }}</span>
              <span class="block text-sm  text-gray-500 truncate dark:text-gray-400">C${{ number_format(App\Models\Money::getBal($user->uuid)) }}</span></a>
            </div>
            <ul class="py-2" aria-labelledby="user-menu-button">
              <li>
                <a href="{{ url('acc/settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Dashboard</a>
              </li>
              @if (App\Models\Robust::getUserLevel($user->uuid) > 200)
                  <li>
                    <a href="{{ url('admin/index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Admin Panel</a>
                  </li>
              @endif
              <li>
                <a href="{{ url('auth/logout') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Sign out</a>
              </li>
            </ul>
          </div>
        @else
          <ul class="flex font-medium">
            <li>
              <a href="{{ url('auth/login') }}" class="block py-2 px-3 text-white rounded bg-transparent">Login</a>
            </li>
            <li>
              <a href="{{ url('join') }}" class="block py-2 px-3 text-white rounded bg-transparent">Join</a>
            </li>
          </ul>
        @endauth
      <button data-collapse-toggle="navbar-user" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-user" aria-expanded="false">
        <span class="sr-only">Open main menu</span>
        <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h15M1 7h15M1 13h15"/>
        </svg>
      </button>
    </div>
    <!-- end user menu -->
    <!-- start top bar -->
    <div class="items-center text-center justify-between hidden w-full md:flex md:w-auto md:order-1" id="navbar-user">
      <ul class="flex flex-col font-medium p-4 md:p-0 mt-4 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0">
        @foreach(config('menu.main') as $mname => $muri)
          <li>
            <a href="{{ url($muri) }}" class="block py-2 px-3 text-white rounded bg-transparent">{{ $mname }}</a>
          </li>
        @endforeach
        <li class="items-center text-center">
            <button id="supportNavbarLink" data-dropdown-toggle="supportNavbar" class="block py-2 px-3 text-white rounded bg-transparent">
                Support
            </button>
            <!-- Dropdown menu -->
            <div id="supportNavbar" class="z-10 hidden font-normal bg-white divide-y divide-gray-100 rounded-lg shadow w-44">
                <ul class="py-2 text-sm text-gray-700 dark:text-gray-400" aria-labelledby="dropdownLargeButton">
                    @foreach(config('menu.support') as $sname => $suri)
                        <li>
                            <a href="{{ url($suri) }}" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">{{ $sname }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </li>
      </ul>
    </div>
    <!-- end top bar -->
  </div>
</nav>
