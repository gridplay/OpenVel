@extends('layout.app')
@section('title', 'Mainland Tiers')
@section('content')
<h1 class="mb-4 text-6xl text-center">Mainland Tiers</h1>
@if (Auth::check())
	{!! Form::open(['method' => 'post', 'url' => 'tier', 'class' => 'space-y-4 md:space-y-6']) !!}
	<button type="submit" class="text-white bg-green-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 text-center">Save changes</button>
	<br>If downgrading your current subscription will be cancelled<br>
@endif
	Billing is currently offline due to a switch in payment system
	<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
		<table class="w-full table-fixed text-2xl">
			<thead>
				<tr>
					<th class="text-left">Price in CAD</th>
					<th class="text-center">Tier Name</th>
					<th class="text-center">Square Meters</th>
					<th class="text-right">Parcel size</th>
				</tr>
			</thead>
			<tbody>
				@foreach(App\Models\Tier::orderBy('cad', 'desc')->get() as $t)
					<tr class="odd:bg-white bg-gray-300 border-b">
						<td>
							@if (Auth::check())
								@php
									$user = Auth::user();
									$checked = "";
									if ($user->tier == $t->id) {
										$checked = "checked";
									}
								@endphp
								<input {{ $checked }} type="radio" value="{{ $t->id }}" name="tier" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2">
							@endif
							${{ number_format($t->cad) }} / month
						</td>
						<td class="text-center">Tier {{ $t->id }}</td>
						<td class="text-center">{{ number_format($t->sqms) }}</td>
						<td class="text-right">{{ $t->size }} Region</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		<small>
			No setup fee EVER!
			<br>Prices do not include Ontario sales tax (HST - Harmonized Sales Tax) of {{ env('TAX') }}%
		</small>
	</div>
@if (Auth::check())
	{!! Form::close() !!}
@endif
@endsection