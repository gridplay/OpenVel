@extends('layout.app')
@section('title', 'Payment Cancelled')
@section('content')
@php
if (request()->has('t') && !empty(request()->input('t'))) {
	$t = request()->input('t');
	App\Models\Payment::where('transaction', $t)->delete();
}
@endphp
<h1 class="mb-4 text-6xl text-center">Payment Cancelled</h1>
@endsection