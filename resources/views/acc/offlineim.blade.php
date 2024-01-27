@extends('acc.app')
@section('usercontent')
@if (Auth::check())
@php
$u = Auth::user();
$off = App\Models\Robust::tbl('im_offline');
$off = $off->where('PrincipalID', $u->uuid);
$off = $off->orderBy('TMStamp', 'desc');
$off = $off->paginate(25);
$paging = $off->appends(request()->except(['page']))->links();
@endphp
<h3 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Offline IM's</h3>
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
	{!! $paging !!}
	<table class="w-full text-sm text-left rtl:text-right text-gray-500">
		<thead>
			<tr>
				<th>From</th>
				<th>Type</th>
				<th>Message</th>
				<th>Time</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			@foreach($off as $o)
				@php
					$from = "UNKNOWN";
					$fromname = App\Models\Robust::uuid2name($o->FromID);
					if ($fromname != "") {
						$from = $fromname;
					}
					$array = json_decode(json_encode((array)simplexml_load_string($o->Message)),true);
					$imType = "IM";
					if ($array['fromGroup'] == "true") {
						$imType = "GROUP";
						if ($g = App\Models\Robust::tbl('os_groups_groups')->where('GroupID', $o->FromID)->first()) {
							$from = $g->Name;
						}
						$from .= " - ".$array['fromAgentName'];
					}
					$msg = str_replace("\n","<br>", $array['message']);
				@endphp
				<tr class="odd:bg-white bg-gray-300 border-b">
					<td>{{ $from }}</td>
					<td>{{ $imType }}</td>
					<td>{!! $msg !!}</td>
					<td>{{ App\Models\Core::readabletimestamp($o->TMStamp) }}</td>
					<td>
						{!! Form::open(['url' => 'acc/offlineim', 'method' => 'DELETE']) !!}
			            {!! Form::hidden('id', $o->ID) !!}
			                <div class="row"><div class="col">
			                    <button class="btn btn-danger" type="submit">Delete</button>
			                </div></div>
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