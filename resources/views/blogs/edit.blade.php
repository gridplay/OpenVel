@extends('layout.app')
@section('title', 'Blog - Edit')
@section('content')
@if (App\Models\Admin::isMod())
	@if ($b = App\Models\Blog::where('id', $id)->first())
		{!! Form::open(['url' => 'blog/'.$id, 'method' => 'PUT']) !!}
			<div class="grid gap-6 mb-6 md:grid-cols-1">
				<div>
					<label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Title</label>
					{!! Form::text('title', $b->title, ['id' => 'title', 'class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5']) !!}
				</div>
				<div>
					<label for="editor" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Blog</label>
					{!! Form::textarea('blog', $b->blog, ['id' => 'editor', 'rows' => 4, 'class' => 'block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500']) !!}
				</div>
				<div>
					<button class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" type="submit">Save</button>
				</div>
			</div>
		{!! Form::close() !!}
		{!! Form::open(['url' => 'blog/'.$id, 'method' => 'DELETE']) !!}
			<button class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" type="submit">DELETE</button>
		{!! Form::close() !!}
	@endif
@endif
@endsection