@extends('acc.app')
@section('usercontent')
@if(Auth::check())
@php
$u = App\Models\User::find(Auth::id());
@endphp

<h3 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Account Settings</h3>
	{!! Form::open(['url' => 'acc/settings', 'method' => 'put', 'enctype' => "multipart/form-data"]) !!}
		<div class="grid gap-6 mb-6 md:grid-cols-1">
			<div>
				<B>Email</B>
				{!! Form::text('email', $u->email, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
            </div>
            <button class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" name="act" value="Save" type="submit">Save</button>
		</div>
	{!! Form::close() !!}
	@if (empty($u->email_verified_at))
		{!! Form::open(['url' => 'email/verification-notification', 'method' => 'post']) !!}
		<button class="focus:outline-none text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" type="submit">Resend verify email</button>
		{!! Form::close() !!}
	@endif
<h3 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Change Password</h3>
	{!! Form::open(['url' => 'acc/changepassword', 'method' => 'put']) !!}
		<div class="grid gap-6 mb-6 md:grid-cols-3">
			<div>
				<B>Current Password</B>
				<input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="cpsswrd" type="password" value="" placeholder="current password here">
			</div>
			<div>
				<B>New Password</B>
				<input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="newpsswrd" type="password" value="" placeholder="new password here">
			</div>
			<div>
				<B>Confirm New Password</B>
				<input class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" name="cnewpsswrd" type="password" value="" placeholder="confirm password here">
			</div>
			<br><button type="submit" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">Change Password</button>
		</div>
	{!! Form::close() !!}
	Changing your password here WILL change it for your grid login<br>
	Forgot your current password? <a href="{{ url('auth/forgot') }}" class="hover:underline font-bold">Click here</a> to reset it
@php
$alert = "";
$s = App\Models\Sqms::where('uuid', $u->uuid)->paginate(25);
$total = 0;
foreach($s as $size) {
	$total += $size->sqm;
}					
if ($grp = App\Models\Robust::tbl('os_groups_groups')->where('FounderID', $u->uuid)->first()) {
	if ($gs = App\Models\Sqms::where('uuid', $grp->GroupID)->get()) {
		foreach($gs as $gsize) {
			$total += $gsize->sqm;
		}
	}
}
$max = 0;
$tname = "Tier 1";
if ($tier = App\Models\Tier::find($u->tier)) {
	$max = $tier->sqms;
	$tname = "Tier ".$tier->id;
}
if ($total > $max) {
	$alert = "You are over your limit. Please reduce your usage immediately";
}
@endphp
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
	<h4 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Parcels</h4>
	<p>
		<B>Total Usage:</B> {{ number_format($total) }} sqms<br>
		<B>Allowed Usage:</B> {{ number_format($max) }} sqms<br>
		<B>Tier Name:</B> {{ $tname }}<br>
		<B>Monthly Tier renewal:</B>
		@if ($u->tier_expire > 0)
			{{ App\Models\Core::time2date($u->tier_expire) }}
		@else
			Never
		@endif
	</p>
	@if (!empty($alert))
		<span class="bg-red-800 text-white">{{ $alert }}</span>
	@endif
	{!! $s->appends(request()->except('page'))->links() !!}
	<table class="w-full table-fixed text-2xl">
		<thead>
			<tr>
				<th class="text-left">Region</th>
				<th class="text-center">Parcel</th>
				<th class="text-right">Sqms</th>
			</tr>
		</thead>
		<tbody>
			@foreach($s as $sqms)
				@if (!empty($sqms->region))
					@php
						$reg = App\Models\Robust::tbl('regions')->where('uuid',$sqms->region)->first();
					@endphp
					@if ($reg)
						<tr class="odd:bg-white bg-gray-300 border-b">
							<td class="text-left">{{ $reg->regionName }}</td>
							<td class="text-center">{{ $sqms->parcel }}</td>
							<td class="text-right">{{ number_format($sqms->sqm) }}</td>
						</tr>
					@endif
				@endif
			@endforeach
		</tbody>
	</table>
	{!! $s->appends(request()->except('page'))->links() !!}
</div>
@endif
@stop