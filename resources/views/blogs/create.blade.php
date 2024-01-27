@extends('layout.app')
@section('title', 'Blog - Create')
@section('content')
@if (App\Models\Admin::isMod())
	{!! Form::open(['url' => 'blog/', 'method' => 'POST']) !!}
		<div class="grid gap-6 mb-6 md:grid-cols-1">
			<div>
				<label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
				{!! Form::text('title', null, ['id' => 'title', 'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5']) !!}
			</div>
			<div>
				<label for="editor" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Blog</label>
				{!! Form::textarea('blog', null, ['id' => 'editor', 'rows' => 4, 'class' => 'block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500']) !!}
			</div>
			<div>
				<button class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" type="submit">Save</button>
			</div>
		</div>
	{!! Form::close() !!}
@endif
@endsection