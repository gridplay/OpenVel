@extends('layout.app')
@section('title', 'News')
@section('content')
<h1 class="text-center mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">News</h1>
@if (isset($id) && !empty($id))
	@if ($b = App\Models\Blog::where('id', $id)->first())
		<div class="shadow-md bg-white">
			<h2 class="text-left mb-4 text-2xl font-extrabold leading-none tracking-tight text-gray-900 md:text-3xl lg:text-4xl">{{ $b->title }}</h2>
			<p class="mb-3 text-gray-500">{!! $b->blog !!}</p>
			<hr class="h-px my-8 bg-gray-200 border-0">
			<B>Posted By:</B> {{ App\Models\User::find($b->poster)->firstname." ".App\Models\User::find($b->poster)->lastname }}<br>
			Created {{ App\Models\Core::time2date($b->posted) }}<br>
			Edited {{ App\Models\Core::time2date($b->edited) }}<br>
		</div>
		@if (App\Models\Admin::isMod())
			<p>
				<a href="{{ url('blog/'.$id.'/edit') }}" class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-3 py-2 me-2 mb-2">Edit</a>
			</p>
		@endif
	@endif
@else
@if (App\Models\Admin::isMod())
	<p><a href="{{ url('blog/create') }}" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-3 py-2 me-2 mb-2">Create</a></p>
@endif
	<div class="grid gap-3 md:grid-cols-3">
		@foreach(App\Models\Blog::orderBy('posted', 'desc')->get() as $b)
			<a href="{{ url('blog/'.$b->id) }}" class="block max-w-sm bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100">
				<h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $b->title }}</h2>
				Created {{ App\Models\Core::time2date($b->posted) }}<br>
				<p class="font-normal text-gray-700 dark:text-gray-400">{!! $b->blog !!}</p>
			</a>
		@endforeach
	</div>
@endif
@endsection