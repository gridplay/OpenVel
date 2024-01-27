@extends('layout.app')
@section('title', 'Payment Successful')
@section('content')
@php
if (request()->has('t') && !empty(request()->input('t'))) {
	$t = request()->input('t');
	$p = App\Models\Payment::where('transaction', $t)->first();
	$expire = \Carbon\Carbon::now()->addMonths(1)->timestamp;
	App\Models\User::where('id', Auth::id())->update(['paypal_sub' => $p->paypal_id, 'tier' => $p->tier, 'tier_expire' => $expire]);
	App\Models\Payment::where('transaction', $t)->update(['completed' => time()]);
}
@endphp
<h1 class="mb-4 text-6xl text-center">Payment Successful</h1>
<p class="text-center">You may now get more land</p>
@endsection