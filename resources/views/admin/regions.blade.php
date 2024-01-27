@extends('admin.app')
@section('admincontent')
@if (App\Models\Admin::isAdmin())
	@if (request()->has('u') && !empty(request()->input('u')))
		@if ($region = App\Models\Robust::tbl('regions')->where('uuid', request()->input('u'))->first())
			<div class="card card-body">
				<div class="row">
					<div class="col-md-3 text-end">
						<B>UUID</B>
					</div>
					<div class="col-md-9">
						<small>{{ $region->uuid }}</small>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3 text-end">
						<B>Name</B>
					</div>
					<div class="col-md-9">
						{{ $region->regionName }}
					</div>
				</div>
				<div class="row">
					<div class="col-md-3 text-end">
						<B>Grid Location</B>
					</div>
					<div class="col-md-9">
						{{ ($region->locX / 256) }},{{ ($region->locY / 256) }}
					</div>
				</div>
				<div class="row">
					<div class="col-md-3 text-end">
						<B>Owner</B>
					</div>
					<div class="col-md-9">
						<a href="{{ url('admin/residents?u='.$region->owner_uuid) }}">{{ App\Models\Robust::uuid2name($region->owner_uuid) }}</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3 text-end">
						<B>Server</B>
					</div>
					<div class="col-md-9">
						{{ $region->serverIP }}
					</div>
				</div>
				<div class="row">
					<div class="col-md-3 text-end">
						<B>Port</B>
					</div>
					<div class="col-md-9">
						{{ $region->serverPort }}
					</div>
				</div>
				<div class="row">
					<div class="col-md-3 text-end">
						<B>Size</B>
					</div>
					<div class="col-md-9">
						{{ $region->sizeX }} x {{ $region->sizeY }}
					</div>
				</div>
				<div class="row">
					<div class="col-md-3 text-end">
						<B>Avatars</B>
					</div>
					<div class="col-md-9">
						{{ number_format(App\Models\Robust::tbl('Presence')->where('RegionID', $region->uuid)->count()) }}
					</div>
				</div>
				{!! Form::open(['method' => 'put', 'url' => 'admin/restart']) !!}
					{!! Form::hidden('regionid', $region->uuid) !!}
					<button type="submit">RESTART</button>
				{!! Form::close() !!}
				<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
					<table class="w-full text-sm text-left rtl:text-right text-gray-500">
						<thead>
							<tr>
								<th>Avatar Name</th>
								<th>Last Seen</th>
							</tr>
						</thead>
						<tbody>
							@foreach(App\Models\Robust::tbl('Presence')->where('RegionID', $region->uuid)->get() as $p)
								<tr class="odd:bg-white bg-gray-300 border-b">
									<td>
										<a href="{{ url('admin/residents?u='.$p->UserID) }}">
											{{ App\Models\Robust::uuid2name($p->UserID) }}
										</a>
									</td>
									<td>{{ App\Models\Core::readabletimestamp($p->LastSeen) }}</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		@else
		<h1 class="text-center">Region Not Found</h1>
		@endif
	@else
		<div class="card card-body">
			@php
				$s = "";
				$regions = App\Models\Robust::tbl('regions');
				if (request()->has('s') && !empty(request()->input('s'))) {
					$s = request()->input('s');
					$regions = $regions->where('regionName', 'LIKE', '%'.$s.'%');
					$regions = $regions->orWhere('owner_uuid', 'LIKE', '%'.$s.'%');
					$regions = $regions->orWhere('serverIP', 'LIKE', '%'.$s.'%');
					$regions = $regions->orWhere('serverPort', 'LIKE', '%'.$s.'%');
				}
				$count = $regions->count();
				$order = "regionName";
				$by = "asc";
				$o = "regionName.asc";
				if (request()->has('o') && !empty(request()->input('o'))) {
					$o = request()->input('o');
					list($order,$by) = explode(".", $o);
				}
				$regions = $regions->join('UserAccounts', 'regions.owner_uuid', '=', 'UserAccounts.PrincipalID');
				$regions = $regions->orderBy($order, $by);
				$regions = $regions->paginate(50);
				$paging = $regions->appends(request()->all())->links();
				$orderlist = [
					'regionName.asc' => 'Region Name A-Z', 'regionName.desc' => 'Region Name Z-A',
					'FirstName.asc' => 'First Resident Name A-Z', 'FirstName.desc' => 'First Resident Name Z-A',
					'serverIP.asc' => 'Server A-Z', 'serverIP.desc' => 'Server Z-A',
					'serverPort.asc' => 'Port A-Z', 'serverPort.desc' => 'Port Z-A',
					'sizeX.asc' => 'Size X 0-9', 'sizeX.desc' => 'Size X 9-0',
					'sizeY.asc' => 'Size Y 0-9', 'sizeY.desc' => 'Size Y 9-0',
				];
			@endphp
			{!! Form::open(['url' => 'admin/regions', 'method' => 'GET']) !!}
				<div class="grid gap-6 mb-6 md:grid-cols-2">
					{!! Form::text('s', $s, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5', 'onchange' => 'this.form.submit();']) !!}
					{!! Form::select('o', $orderlist, $o, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5', 'onchange' => 'this.form.submit();']) !!}
				</div>
			{!! Form::close() !!}
			<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
				{!! $paging !!}<br>
				<table class="w-full text-sm text-left rtl:text-right text-gray-500">
					<thead>
						<tr>
							<th>UUID</th>
							<th>Name</th>
							<th>Location</th>
							<th>Owner</th>
							<th>Server : Port</th>
							<th>Size</th>
							<th>Avatars</th>
						</tr>
					</thead>
					<tbody>
						@foreach($regions as $region)
							<tr class="odd:bg-white bg-gray-300 border-b">
								<td><a href="{{ url('admin/regions?u='.$region->uuid) }}"><small>{{ $region->uuid }}</small></a></td>
								<td>{{ $region->regionName }}</td>
								<td>{{ ($region->locX / 256) }},{{ ($region->locY / 256) }}</td>
								<td>
									<a href="{{ url('admin/residents?u='.$region->owner_uuid) }}">
										{{ App\Models\Robust::uuid2name($region->owner_uuid) }}
									</a>
								</td>
								<td>{{ $region->serverIP }} : {{ $region->serverPort }}</td>
								<td>{{ $region->sizeX }} x {{ $region->sizeY }}</td>
								<td>{{ number_format(App\Models\Robust::tbl('Presence')->where('RegionID', $region->uuid)->count()) }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				{!! $paging !!}
			</div>
		</div>
	@endif
@endif
@endsection