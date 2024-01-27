@extends('layout.app')
@section('title', 'Password Reset')
@section('content')
@if (request()->has('code'))
	@php
		$ec = DB::table('email_reset')->where('code', request()->input('code'))->first();
	@endphp
	@if($ec && $ec->expires > time())
		<section class="bg-gray-50">
		    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
		        <div class="w-full bg-white rounded-lg shadow md:mt-0 sm:max-w-md xl:p-0">
		            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
						{!! Form::open(['url' => 'auth/reset', 'method' => 'PUT', 'class' => 'space-y-4 md:space-y-6', 'id' => 'accountform']) !!}
						{!! Form::hidden('code', request()->input('code')) !!}
						<h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl">Reset Password</h1>
							<div>
								<label for="newpsswrd" class="block mb-2 text-sm font-medium text-gray-90">New Password</label>
								<input class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" name="newpsswrd" type="password" placeholder="new password here" required>
							</div>
							<div>
								<label for="cnewpsswrd" class="block mb-2 text-sm font-medium text-gray-90">Confirm New Password</label>
								<input class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" name="cnewpsswrd" type="password" placeholder="confirm password here" required>
							</div>
							<div>
								<div class="g-recaptcha" data-sitekey="{{ config('site.recaptcha')['site'] }}"></div>
							</div>
							<div>
								<button data-sitekey="{{ config('site.recaptcha')['site'] }}" data-callback='onAccountSubmit' data-action='submit' class="g-recaptcha text-white bg-green-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Change Password</button>
							</div>
						{!! Form::close() !!}
						Changing your password here WILL change it for your grid login
					</div>
				</div>
			</div>
		</section>
	@else
		<h1 class="text-center">CODE EXPIRED!</h1>
	@endif
@else
<h1 class="text-center">INVALID ACCESS</h1>
@endif
@endsection