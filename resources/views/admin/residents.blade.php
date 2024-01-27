@extends('admin.app')
@section('admincontent')
@if (App\Models\Admin::isAdmin())
	@if (request()->has('u') && $u = App\Models\Robust::find(request()->input('u')))
	<section class="bg-gray-50">
	    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
	        <div class="w-full bg-white rounded-lg shadow md:mt-0 sm:max-w-md xl:p-0">
				<h4 class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">{{ $u->FirstName }} {{ $u->LastName }}</h4>
				
				<div class="p-6 space-y-4 md:space-y-6 sm:p-8">
					{!! Form::open(['url' => 'admin/residents', 'method' => 'PUT', 'class' => 'space-y-4 md:space-y-6']) !!}
						{!! Form::hidden('uid', $u->PrincipalID) !!}
							<div>
								<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">User Level</label>
								{!! Form::select('UserLevel', App\Models\Robust::$UserLevels, $u->UserLevel, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5']) !!}
							</div>
							<div>
								<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">User Email</label>
								{!! Form::text('Email', $u->Email, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5']) !!}
							</div>
						<button type="submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">Save</button>
					{!! Form::close() !!}
				</div>

				{!! Form::open(['url' => 'admin/passwordreset', 'method' => 'PUT']) !!}
					{!! Form::hidden('uuid', $u->PrincipalID) !!}
					<button type="submit" class="focus:outline-none text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">Reset Password</button>
				{!! Form::close() !!}
				@if ($su = App\Models\User::findbyuuid($u->PrincipalID))
					{!! Form::open(['url' => 'admin/residents', 'method' => 'DELETE']) !!}
						{!! Form::hidden('uuid', $u->PrincipalID) !!}
						{!! Form::hidden('id', $su->id) !!}
						<button type="submit" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">DELETE</button>
					{!! Form::close() !!}
				@endif
				@php
					$onoff = "<span class='text-red-800'>Offline</span><br>";
					if ($p = App\Models\Robust::tbl('Presence')->where('UserID', $u->PrincipalID)->first()) {
						$onoff = "<span class='text-green-800'>Online</span>";
						if ($reg = App\Models\Robust::tbl('regions')->where('uuid', $p->RegionID)->first()) {
							$onoff .= '<br><a href="'.url('admin/regions?u='.$reg->uuid).'">'.$reg->regionName.'</a><br>';
						}
					}
					$uname = $u->FirstName;
					if (!empty($u->LastName)) {
						$uname = $u->FirstName.".".$u->LastName;
					}
					$sqms = 0;
					$maxsqms = 0;
					if ($su = App\Models\User::where('uuid', $u->PrincipalID)->first()) {
						$sqms = App\Models\Sqms::countsqms($u->PrincipalID);
						$maxsqms = $su->tier;
					}
					$login = 0;
					if ($gu = App\Models\Robust::tbl('GridUser')->where('UserID', $u->PrincipalID)->first()) {
						$login = $gu->Login;
					}

					$alert = "";
					$s = App\Models\Sqms::where('uuid', $u->PrincipalID)->get();
					$total = 0;
					foreach($s as $size) {
						$total += $size->sqm;
					}
					if ($grp = App\Models\Robust::tbl('os_groups_groups')->where('FounderID', $u->PrincipalID)->first()) {
						if ($gs = App\Models\Sqms::where('uuid', $grp->GroupID)->get()) {
							foreach($gs as $gsize) {
								$total += $gsize->sqm;
							}
						}
					}
					$max = 0;
					if ($su = App\Models\User::where('uuid', $u->PrincipalID)->first()) {
						if ($tier = App\Models\Tier::find($su->tier)) {
							$max = $tier->sqms;
						}
					}
					if ($total > $max) {
						$alert = "This resident is over their limit.";
					}
				@endphp
				{!! $onoff !!}
				Joined: {{ App\Models\Core::time2date($u->Created) }}<br>
				Last Seen: {{ App\Models\Core::time2date($login) }}<br>
				C${{ number_format(App\Models\Money::getBal($u->PrincipalID)) }}<br>
				<br><a href="{{ url('u/'.$uname) }}" class="focus:outline-none text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">Profile</a>
				<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
					<h4 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Parcels</h4>
						<p>
							Total Usage: {{ number_format($total) }} sqms<br>
							Allowed Usage: {{ number_format($max) }} sqms
						</p>
						@if (!empty($alert))
							<span class="bg-red-800 text-white">{{ $alert }}</span>
						@endif
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
				</div>
				<h3 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Regions</h3>
				@php
					$rs = App\Models\Robust::tbl('regions');
					$rs = $rs->where('owner_uuid', $u->PrincipalID);
					$s = "";
					if (request()->has('r') && !empty(request()->input('r'))) {
						$s = request()->input('r');
						$rs = $rs->where('regionName', 'LIKE', '%'.$s.'%');
					}
					$rs = $rs->orderBy('regionName', 'asc');
					$rs = $rs->get();
				@endphp
				{!! Form::open(['url' => 'admin/residents', 'method' => 'GET']) !!}
				{!! Form::hidden('u', request()->input('u')) !!}
				{!! Form::text('r', $s, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5', 'placeholder' => 'Search by Region Name', 'onchange' => 'this.form.submit();']) !!}
				{!! Form::close() !!}
				<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
					<table class="w-full text-left rtl:text-right text-gray-500">
						<thead>
							<tr>
								<th>Name</th>
								<th>Server</th>
								<th>Port</th>
								<th>Location</th>
								<th>Size</th>
							</tr>
						</thead>
						<tbody>
							@foreach($rs as $r)
								<tr class="odd:bg-white bg-gray-300 border-b">
									<td><a href="{{ url('admin/regions?u='.$r->uuid) }}">{{ $r->regionName }}</a></td>
									<td>{{ $r->serverIP }}</td>
									<td>{{ $r->serverPort }}</td>
									<td>{{ ($r->locX / 256) }},{{ ($r->locY / 256) }}</td>
									<td>{{ $r->sizeX }} x {{ $r->sizeY }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<h3 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Groups</h3>
				@php
				$groups = App\Models\Robust::tbl('os_groups_membership')->where('PrincipalID', $u->PrincipalID);
				$groups = $groups->join('os_groups_groups', 'os_groups_membership.GroupID', '=', 'os_groups_groups.GroupID');
				$groups = $groups->orderBy('os_groups_groups.Name', 'asc');
				$groups = $groups->get();
				@endphp
				<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
	                <table class="w-full text-left rtl:text-right text-gray-500">
	                    <thead>
	                        <tr>
	                            <th>Name</th>
	                            <th>Members</th>
	                            <th>Deeded Sqms</th>
	                        </tr>
	                    </thead>
	                    <tbody>
							@foreach($groups as $g)
								@php
									$gsqms = 0;
									$group = App\Models\Robust::tbl('os_groups_groups')->where('GroupID', $g->GroupID)->first();
									$gcount = App\Models\Robust::tbl('os_groups_membership')->where('GroupID', $g->GroupID)->count();
									if ($gs = App\Models\Sqms::where('uuid', $grp->GroupID)->get()) {
										foreach($gs as $gsq) {
											$gsqms += $gsq->sqm;
										}
									}
								@endphp
								<tr class="odd:bg-white bg-gray-300 border-b">
									<td>{{ $group->Name }}</td>
									<td>{{ number_format($gcount) }}</td>
									<td>{{ number_format($gsqms) }}</td>
								</tr>
							@endforeach
	                    </tbody>
	                </table>
	            </div>
	        </div>
	    </div>
	</section>
	@else
		@php
		$s = "";
		$usersdb = App\Models\Robust::tbl('UserAccounts');
		if (request()->has('s') && !empty(request()->input('s'))) {
			$s = request()->input('s');
			if (strpos($s, " ")) {
				list($first, $last) = explode(" ", $s);
				$userdb = $usersdb->where('FirstName', $first);
				$userdb = $usersdb->where('LastName', $last);
			}else{
				$userdb = $usersdb->where('FirstName', 'LIKE', '%'.$s.'%');
				$userdb = $usersdb->orWhere('LastName', 'LIKE', '%'.$s.'%');
			}
			$userdb = $usersdb->orWhere('PrincipalID', 'LIKE', '%'.$s.'%');
			$userdb = $usersdb->orWhere('Email', 'LIKE', '%'.$s.'%');
		}
		if (request()->has('o') && !empty(request()->input('o'))) {
			$o = request()->input('o');
			list($otype,$odir) = explode(".", $o);
		}else{
			$o = "Created.desc";
			$otype = "Created";
			$odir = "desc";
		}
		$usersdb = $usersdb->orderBy($otype, $odir);
		$display = 25;
		if (request()->has('d') && !empty(request()->input('d'))) {
			$display = request()->input('d');
		}
		$usersdb = $usersdb->paginate($display);
		$paging = $usersdb->appends(request()->all())->links();
		$orderlist = [
			'Created.desc' => 'Newest Residents', 'Created.asc' => 'Oldest Residents',
			'FirstName.desc' => 'First Name Z-A', 'FirstName.asc' => 'First Name A-Z',
			'LastName.desc' => 'Last Name Z-A', 'LastName.asc' => 'Last Name A-Z',
			'Email.desc' => 'Email Z-A', 'Email.asc' => 'Email A-Z',
			'UserLevel.desc' => 'UserLevel 9-0', 'UserLevel.asc' => 'UserLevel 0-9',
		];
		@endphp
		{!! Form::open(['method' => 'GET']) !!}
			<div class="grid gap-6 mb-6 md:grid-cols-2">
		        {!! Form::text('s',$s,['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5', 'placeholder' => 'Search by name, UUID or email', 'onchange' => 'this.form.submit();']) !!}
		        {!! Form::select('o', $orderlist, $o, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5', 'onchange' => 'this.form.submit();']) !!}
		    </div>
	    {!! Form::close() !!}
		{!! $paging !!}
		<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
			<table class="w-full text-sm text-left rtl:text-right text-gray-500">
				<thead>
					<tr>
						<th>ID<br>Last Sim</th>
						<th>Name<br>Status</th>
						<th>Joined<br>Last Seen</th>
						<th>Role</th>
						<th>Email<br>Confirmed</th>
						<th>C$ Balance</th>
						<th>Sqm Usage</th>
					</tr>
				</thead>
				<tbody>
					@foreach($usersdb as $u)
						@if ($u->PrincipalID != "6571e388-6218-4574-87db-f9379718315e")
							@php
								$sim = "UNKNOWN SIM";
								$onoff = "<div class='text-red-800'>Offline</div>";
								if ($p = App\Models\Robust::tbl('Presence')->where('UserID', $u->PrincipalID)->first()) {
									$onoff = "<div class='text-green-800'>Online</div>";
									if ($reg = App\Models\Robust::tbl('regions')->where('uuid', $p->RegionID)->first()) {
										$sim = '<a href="'.url('admin/regions?s='.$reg->regionName).'">'.$reg->regionName.'</a>';
									}
								}else{
									if ($g = App\Models\Robust::tbl('GridUser')->where('UserID', $u->PrincipalID)->first()) {
										if ($reg = App\Models\Robust::tbl('regions')->where('uuid', $g->LastRegionID)->first()) {
											$sim = '<a href="'.url('admin/regions?s='.$reg->regionName).'">'.$reg->regionName.'</a>';
										}
									}
								}
								$guser = null;
								if ($gu = App\Models\Robust::tbl('GridUser')->where('UserID', $u->PrincipalID)->first()) {
									$guser = $gu;
								}else{
									$guser = App\Models\Robust::query();
									$guser->Login = 0;
								}
								$s = App\Models\Sqms::where('uuid', $u->PrincipalID)->get();
								$total = 0;
								foreach($s as $size) {
									$total += $size->sqm;
								}
								if ($grp = App\Models\Robust::tbl('os_groups_groups')->where('FounderID', $u->PrincipalID)->first()) {
									if ($gs = App\Models\Sqms::where('uuid', $grp->GroupID)->get()) {
										foreach($gs as $gsq) {
											$total += $gsq->sqm;
										}
									}
								}
								$max = 0;
								$emailverified = "Unknown";
								if ($su = App\Models\User::where('uuid', $u->PrincipalID)->first()) {
									if ($tier = App\Models\Tier::find($su->tier)) {
										$max = $tier->sqms;
									}
									$emailverified = '<span class="text-red-800">NOT Verified</span>';
									if (!empty($su->email_verified_at)) {
										$emailverified = '<span class="text-green-800">Verified '.App\Models\Core::readabletimestamp($su->email_verified_at).'</span>';
									}
								}
								$usagewarning = "";
								if ($total > $max) {
									$usagewarning = "bg-red-800 text-white";
								}
							@endphp
							<tr class="odd:bg-white bg-gray-300 border-b">
								<td>
									<small>{{ $u->PrincipalID }}</small><br>
									{!! $sim !!}
								</td>
								<td>
									<a href="{{ url('admin/residents?u='.$u->PrincipalID) }}" class="font-extrabold hover:underline">{{ $u->FirstName }} {{ $u->LastName }}</a><br>
									{!! $onoff !!}
								</td>
								<td>
									<small>{{ App\Models\Core::time2date($u->Created) }}</small><br>
									<small>
										@if ($guser->Login > 0)
											{{ App\Models\Core::time2date($guser->Login) }}
										@endif
									</small>
								</td>
								<td>
									{{ App\Models\Robust::getUserRole($u->PrincipalID) }}<br>
									{{ $u->UserLevel }}
								</td>
								<td>{{ $u->Email }}<br>{!! $emailverified !!}</td>
								<td>{{ env('CURRENCY') }}{{ number_format(App\Models\Money::getBal($u->PrincipalID)) }}</td>
								<td class="{{ $usagewarning }}">
									Using: {{ number_format($total) }} sqms<br>Allowed: {{ number_format($max) }} sqms
								</td>
							</tr>
						@endif
					@endforeach
				</tbody>
			</table>
		</div>
		{!! $paging !!}
	@endif
@endif
@endsection
