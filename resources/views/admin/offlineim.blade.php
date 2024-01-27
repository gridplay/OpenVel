@extends('admin.app')
@section('admincontent')
@if (App\Models\Admin::isAdmin())
@php
$off = App\Models\Robust::tbl('im_offline');
$s = '';
if (request()->has('s') && !empty(request()->input('s'))) {
	$s = request()->input('s');
	$first = $s;
	$last = 'Resident';
	if (strpos($s, ' ')) {
		list($first, $last) = explode(" ", $s);
	}
	if ($user = App\Models\Robust::tbl('UserAccounts')->where('FirstName', 'LIKE', '%'.$first.'%')->where('LastName', 'LIKE', '%'.$last.'%')->first()) {
		$off = $off->where('PrincipalID', $user->PrincipalID);
		$off = $off->orWhere('FromID', $user->PrincipalID);
	}
}
$off = $off->orderBy('TMStamp', 'desc');
$off = $off->paginate(25);
$paging = $off->appends(request()->except(['page']))->links();
@endphp
<div class="card">
	<div class="card-header">Offline IM's</div>
	<div class="card-body">
		{!! Form::open(['method' => 'GET']) !!}
        {!! Form::text('s', $s, ['class' => 'form-control', 'onchange' => 'this.form.submit();', 'placeholder' => 'Search by name']) !!}
        {!! Form::close() !!}
		{!! $paging !!}
		<table class="table">
			<thead>
				<tr>
					<th>To</th>
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
						$to = "UNKNOWN";
						$toname = App\Models\Robust::uuid2name($o->PrincipalID);
						if ($toname != "") {
							$to = '<a href="'.url('admin/residents?u='.$o->PrincipalID).'">'.$toname.'</a>';
						}
						$from = "UNKNOWN";
						$fromname = App\Models\Robust::uuid2name($o->FromID);
						if ($fromname != "") {
							$from = '<a href="'.url('admin/residents?u='.$o->FromID).'">'.$fromname.'</a>';
						}
						$array = json_decode(json_encode((array)simplexml_load_string($o->Message)),true);
						$imType = "IM";
						if ($array['fromGroup'] == "true") {
							$imType = "GROUP";
							if ($g = App\Models\Robust::tbl('os_groups_groups')->where('GroupID', $o->FromID)->first()) {
								$from = '<a href="'.url('admin/groups?g='.$g->GroupID).'">'.$g->Name.'</a>';
							}
							$from .= '<br> - '.$array['fromAgentName'];
						}
						$msg = str_replace("\n","<br>", $array['message']);
					@endphp
					<tr>
						<td>{!! $to !!}</td>
						<td>{!! $from !!}</td>
						<td>{{ $imType }}</td>
						<td>{!! $msg !!}</td>
						<td>{{ App\Models\Core::readabletimestamp($o->TMStamp) }}</td>
						<td>
							{!! Form::open(['url' => 'admin/offlineim', 'method' => 'DELETE']) !!}
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
</div>
@endif
@endsection