@if (Auth::check())
	<h4 class="mb-4 text-4xl text-center font-bold leading-none tracking-tight text-gray-900">Friends</h4>
	<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
		@php
			$u = Auth::user();
			$friends = App\Models\Robust::tbl('Friends')->where('PrincipalID', $u->uuid)->where('Offered', 0);
			$friends = $friends->join('GridUser', 'Friends.Friend', '=', 'GridUser.UserID');
			$friends = $friends->orderBy('GridUser.Online', 'desc');
			$friends = $friends->paginate(10);
			$farray = [];
		@endphp
		<table class="w-full table-fixed text-2xl">
			<thead>
				<tr>
					<th class="text-left">Name</th>
					<th class="text-right">Status</th>
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
							$bg = "gray";
							if ($GU = App\Models\Robust::tbl('Presence')->where('UserID', $f->Friend)->first()) {
								$fname = '<B>'.$friendname.'</B>';
								$ostatus = "Online";
								$bg = "green";
							}else{
								$fname = '<small>'.$friendname.'</small>';
								$ostatus = "Offline";
								$bg = "red";
							}
							$html = '<tr class="bg-'.$bg.'-300 border-b"><td>'.$fname.'</td><td class="text-right">'.$ostatus.'</td></tr>';
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
	</div>
@endif