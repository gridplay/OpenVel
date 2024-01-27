@extends('admin.app')
@section('admincontent')
@if(Auth::check())
@php
$u = Auth::user();
$trans = App\Models\Payment::orderBy('added', 'desc');
$trans = $trans->paginate(25);
@endphp
<h3 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Tier Payments</h3>
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
	{!! $trans->appends(request()->all())->links() !!}
	<table class="w-full text-sm text-left rtl:text-right text-gray-500">
		<thead>
			<tr>
				<th>Date Added<br>Paypal ID</th>
				<th>Resident Name</th>
				<th>Tier<br>Sqms</th>
				<th>Date Completed<br>Date Cancelled</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
			@foreach($trans as $t)
				@php
					$tier = "1";
					$sqms = "512";
					$amount = 0;
					if ($tr = App\Models\Tier::where('id', $t->tier)->first()) {
						$tier = $tr->id;
						$sqms = $tr->sqms;
						$amount = $tr->cad;
					}
					$completed = "";
					if (!empty($t->completed)) {
						$completed = App\Models\Core::time2date($t->completed);
					}
					$cancelled = "";
					if (!empty($t->cancelled)) {
						$cancelled = App\Models\Core::time2date($t->cancelled);
					}
					$resident = "John Doe";
					if ($u = App\Models\User::find($t->user_id)) {
						$resident = $u->firstname." ".$u->lastname;
					}
				@endphp
				<tr class="odd:bg-white bg-gray-300 border-b">
					<td><B>{{ App\Models\Core::time2date($t->added) }}</B>
						<br><small>{{ $t->paypal_id }}</small></td>
					<td>{{ $resident }}</td>
					<td>Tier {{ $tier }}<br>{{ number_format($sqms) }} sqms</td>
					<td>{{ $completed }}<br>{{ $cancelled }}</td>
					<td>CAD${{ number_format($amount, 2) }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	{!! $trans->appends(request()->all())->links() !!}
</div>
@endif
@stop