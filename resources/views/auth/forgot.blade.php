@extends('layout.app')
@section('title', 'Forgot Password')
@section('content')
<section class="bg-gray-50">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
        <div class="w-full bg-white rounded-lg shadow md:mt-0 sm:max-w-md xl:p-0">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
				{!! Form::open(['url' => 'auth/forgot', 'method' => 'PUT', 'class' => 'space-y-4 md:space-y-6', 'id' => 'accountform']) !!}
					<h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl">Forgot Password</h1>
					<div>
						<label for="firstname" class="block mb-2 text-sm font-medium text-gray-90">First Name</label>
						<input name="firstname" type="text" placeholder="First Name" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" required>
					</div>
					<div>
						<label for="lastname" class="block mb-2 text-sm font-medium text-gray-90">Last Name</label>
						<input name="lastname" type="text" placeholder="Last Name" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
					</div>
					<div>
						<label for="email" class="block mb-2 text-sm font-medium text-gray-90">Email Address</label>
						<input name="email" type="email" placeholder="Email Address" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" required>
					</div>
					<div>
						<button data-sitekey="{{ config('site.recaptcha')['site'] }}" data-callback='onAccountSubmit' data-action='submit' class="g-recaptcha text-white bg-green-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" type="submit">Reset Password</button>
					</div>
				{!! Form::close() !!}
			</div>
		</div>
	</div>
</section>
@endsection