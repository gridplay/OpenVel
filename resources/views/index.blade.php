@extends('layout.app')
@section('title', 'Home')
@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 grid-rows-1 gap-4">
	<div class="text-start">
		@if (Auth::check())
			@include('parts.leftbar')
		@endif
	</div>

	<div class="text-center">
		<img src="{{ url('CanadianGridLogo.png') }}" class="h-auto max-w-full mx-auto" alt="Canadian Grid Logo" />
		<h1 class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Why join us?</h1>
		<ul class="list-disc list-inside">
			<li>OpenSim 0.9.2.2 with custom code</li>
			<li>First grid to have in world abuse reports working</li>
			<li>Mainland living with 30k prims per region</li>
			<li>Free 1024 sqm parcel <small>(1) (2)</small></li>
			<li>Tier based model <small>(3)</small></li>
			<li>no "Premium" or "Premium plus" required</li>
			<li>FREE groups, uploads and classifieds</li>
			<li>Hypergrid disabled with Kitely Market enabled</li>
			<li>C$ Currency buying and selling through Podex</li>
			<li>Own marketplace <small>(4)</small></li>
			<li>ubODE physics and ubODEMeshmerizer</li>
			<li>Discord support <small>(5)</small></li>
		</ul>
		<small class="text-gray-500 text-xxs">
			<B>Disclaimers</B><br>
			(1) C$ may be needed to buy land inworld<br>
			(2) Must log in at least once within 6 months to keep free land<br>
			(3) Payments through Paypal, land reclaimed if subscription is cancelled<br>
			(4) MP Coming soon<br>
			(5) Ticket system coming soon, right now we are using our parent company's support portal<br>
		</small>
		<iframe src="https://discord.com/widget?id=1182398479922909245&theme=dark" width="100%" height="500" allowtransparency="true" frameborder="0" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe>
	</div>

	<div class="">
		<h4 class="mb-4 text-4xl text-center font-bold leading-none tracking-tight text-gray-900">Grid Status</h4>
		<table class="w-full table-fixed text-2xl">
			<tbody>
				@foreach(App\Models\Robust::getGridStats() as $name => $val)
					<tr class="odd:bg-white bg-gray-300 border-b">
						<th class="text-left">{{ $name }}</th>
						<td class="text-right">{{ number_format($val) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
		<h4 class="mb-4 text-4xl text-center font-bold leading-none tracking-tight text-gray-900">Grid News</h4>
		@foreach(App\Models\Blog::orderBy('posted', 'desc')->get() as $b)
			<a href="{{ url('blog/'.$b->id) }}" class="block max-w bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100">
				<h2 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $b->title }}</h2>
				<p class="font-normal text-gray-700 dark:text-gray-400">Created {{ App\Models\Core::time2date($b->posted) }}</p>
			</a>
		@endforeach
	</div>
</div>
@endsection