@extends('acc.app')
@section('usercontent')
@if(Auth::check())
	@php
		$u = Auth::user();
		$friends = App\Models\Robust::tbl('Friends')->where('PrincipalID', $u->uuid);
		$friends = $friends->paginate(50);
		$farray = [];
	@endphp
	<h3 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Friends List</h3>
		<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
			{!! $friends->appends(request()->except(['page']))->links() !!}
			<table class="w-full text-sm text-left rtl:text-right text-gray-500">
				<thead>
					<tr>
						<th>Name</th>
						<th>Status</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@foreach($friends as $f)
						@php
							if ($friend = App\Models\Robust::tbl('UserAccounts')->where('PrincipalID', $f->Friend)->first()) {
								$first = $friend->FirstName;
								$last = " ".$friend->LastName;
								if ($last == " Resident") {
									$last = "";
								}
								$friendname = $first.$last;
								if ($GU = App\Models\Robust::tbl('Presence')->where('UserID', $f->Friend)->first()) {
									$fname = '<B>'.$friendname.'</B>';
									$ostatus = "<span class='text-green-800'>Online</span>";
								}else{
									$fname = ''.$friendname.'';
									$ostatus = "<span class='text-red-800'>Offline</span>";
								}
								$btn = "";
								if ($f->Offered == 0) {
									$btn = '<button class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" type="submit" name="fs" value="remove">Remove</button>';
									$married = false;
									if ($p = App\Models\Robust::tbl('userprofile')->where('useruuid', $u->uuid)->where('profilePartner', $f->Friend)->first()) {
										$married = true;
									}
									$profile = App\Models\Robust::tbl('userprofile')->where('useruuid', $u->uuid)->first();
									if (!$married && $profile->profilePartner == App\Models\Robust::$NULL_KEY) {
										$btn .= '<button class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" type="submit" name="fs" value="propose">Propose</button>';
									}else if ($married && $profile->profilePartner == $f->Friend) {
										$btn .= '<button class="focus:outline-none text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" type="submit" name="fs" value="divorce">Divorce</button>';
									}
								}else if ($f->Offered == 1) {
									$btn = '<button class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" type="submit" name="fs" value="accept">Accept</button>';
									$btn .= '<button class="focus:outline-none text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2" type="submit" name="fs" value="denie">Denie</button>';
								}
								$html = Form::open(['url' => 'acc/friendlist', 'method' => 'put']);
								$html .= Form::hidden('friend', $f->Friend);
								$html .= '<tr class="odd:bg-white bg-gray-300 border-b"><td>'.$fname.'</td><td>'.$ostatus.'</td><td>'.$btn.'</td></tr>';
								$html .= Form::close();
								$farray[$friendname] = $html;
							}
						@endphp
					@endforeach
					@php
						asort($farray);
					@endphp
					@foreach($farray as $fn => $fhtml)
						{!! $fhtml !!}
					@endforeach
				</tbody>
			</table>
			{!! $friends->appends(request()->except(['page']))->links() !!}
		</div>
@endif
@endsection