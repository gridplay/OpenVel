@extends('layout.app')
@section('title', 'Admin Panel')
@section('content')
  <ul class="flex flex-wrap text-sm font-medium text-center text-gray-500 border-b border-gray-200">
      @foreach(config('menu.kellie') as $at => $au)
        <li class="me-2">
          <a href="{{ url($au) }}" class="inline-block p-4 rounded-t-lg hover:text-gray-600 hover:bg-gray-50">
              {{ $at }}
          </a>
        </li>
     @endforeach
  </ul>
	@yield('admincontent')
@endsection
