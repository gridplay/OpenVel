@extends('admin.app')
@section('admincontent')
@if(App\Models\Admin::isAdmin())
@php
$trans = App\Models\Money::tbl('transactions');
$trans = $trans->orderBy('time', 'desc');
$trans = $trans->paginate(50);
@endphp
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
	<h3 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Transaction History</h3>
	{!! $trans->appends(request()->all())->links() !!}
	<table class="w-full text-sm text-left rtl:text-right text-gray-500">
		<thead>
			<tr>
				<th>Date<br>Trans ID</th>
				<th>Region<br>Description</th>
				<th>Sender(buyer)<br>Receiver(seller)</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
			@foreach($trans as $t)
				@if(!empty($t->regionUUID))
					@php
						$sender = App\Models\Robust::uuid2name($t->sender);
						$receiver = App\Models\Robust::uuid2name($t->receiver);
						$region = "";
						if ($r = App\Models\Robust::tbl('regions')->where('uuid', $t->regionUUID)->first()) {
							$region = $r->regionName;
						}
						$info = "";
						if (!empty($t->objectName)) {
							$info = $t->objectName;
						}else{
							$info = preg_replace('/[0-9]+/', '', $t->description);
							$info = str_replace(" // :: ", "", $info);
							$info = str_replace("AM", "", $info);
							$info = str_replace("PM", "", $info);
						}
					@endphp
					<tr class="odd:bg-white bg-gray-300 border-b">
						<td><B>{{ App\Models\Core::time2date($t->time) }}</B>
							<br><small>{{ $t->UUID }}</small></td>
						<td><B>{{ $region }}</B>
							<br>{{ $info }}</td>
						<td>
							@if (strpos($sender, 'http://') === false)
								<a href="{{ url('admin/residents?u='.$t->sender) }}">{{ $sender }}</a>
							@else
								{{ $sender }}
							@endif
							<br>
							@if (strpos($receiver, 'http://') === false)
								<a href="{{ url('admin/residents?u='.$t->receiver) }}">{{ $receiver }}</a>
							@else
								{{ $receiver }}
							@endif
						</td>
						<td>C$ {{ number_format($t->amount) }}</td>
					</tr>
				@endif
			@endforeach
		</tbody>
	</table>
	{!! $trans->appends(request()->all())->links() !!}
</div>
@endif
@stop