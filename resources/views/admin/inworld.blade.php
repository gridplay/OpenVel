@extends('admin.app')
@section('admincontent')
@if (App\Models\Admin::isAdmin())
@php
$pres = App\Models\Robust::tbl('Presence');
$pres = $pres->where('RegionID', '!=', App\Models\Robust::$NULL_KEY);
$pres = $pres->join('regions', 'regions.uuid', '=', 'Presence.RegionID');
if (request()->has('o') && !empty(request()->input('o'))) {
	$o = request()->input('o');
	list($otype,$odir) = explode(".", $o);
}else{
	$o = "LastSeen.desc";
	$otype = "LastSeen";
	$odir = "desc";
}
$pres = $pres->orderBy($otype, $odir);
$pres = $pres->paginate(50);
$paging = $pres->appends(request()->except(['page']))->links();
$orderlist = [
	'LastSeen.desc' => 'Latest Seen', 'LastSeen.asc' => 'First Seen',
	'regionName.desc' => 'Region Name Z-A', 'regionName.asc' => 'Region Name A-Z'
];
@endphp
{!! Form::open(['method' => 'GET']) !!}
<div class="grid gap-6 mb-6 md:grid-cols-2">
	{!! Form::select('o', $orderlist, $o, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5', 'onchange' => 'this.form.submit();']) !!}
</div>
{!! Form::close() !!}
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
	{!! $paging !!}
	<table class="w-full text-sm text-left rtl:text-right text-gray-500">
		<thead>
			<tr>
				<th>Resident</th>
				<th>Sim</th>
				<th>Last Seen</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach($pres as $p)
				@php
					$res = App\Models\Robust::tbl('UserAccounts')->where('PrincipalID', $p->UserID)->first();
					$rname = "UNKNOWN";
					if ($reg = App\Models\Robust::tbl('regions')->where('uuid', $p->RegionID)->first()) {
						$rname = '<a href="'.url('admin/regions?u='.$reg->uuid).'">'.$reg->regionName.'</a>';
					}
				@endphp
				<tr class="odd:bg-white bg-gray-300 border-b">
					<td><a href="{{ url('admin/residents?u='.$p->UserID) }}">{{ $res->FirstName }} {{ $res->LastName }}</a></td>
					<td>{!! $rname !!}</td>
					<td>{{ App\Models\Core::readabletimestamp($p->LastSeen) }}</td>
					<td>
						{!! Form::open(['url' => 'admin/kickuser', 'method' => 'PUT']) !!}
						{!! Form::hidden('uuid', $p->UserID) !!}
							<button type="submit" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
								Kick Ghost
							</button>
						{!! Form::close() !!}
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{!! $paging !!}
</div>
@endif
@endsection