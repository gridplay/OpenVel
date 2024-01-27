@extends('layout.app')
@section('title', 'Account - Proposal')
@section('content')
@if (Auth::check() && isset($id))
@php
$p = App\Models\Proposals::where('id', $id)->first();
@endphp
{!! Form::open(['method' => 'put', 'url' => 'acc/proposal']) !!}
{!! Form::hidden('id', $id) !!}
<div class="row">
	<div class="col-md-2">
		Proposing to {{ App\Models\Robust::uuid2name($p->receiver) }}
	</div>
	<div class="col-md-10">
		{!! Form::textarea('msg', null, ['class' => 'form-control', 'id' => 'htmleditor', 'placeholder' => 'Type your message here']) !!}
	</div>
</div>
<div class="row">
	<div class="col-md-2">
	</div>
	<div class="col-md-10">
		<button type="submit" class="btn btn-success">Propose</button>
	</div>
</div>
{!! Form::close() !!}
@elseif (Auth::check() && request()->has('code') && !empty(request()->input('code')))
@php
$code = request()->input('code');
$p = App\Models\Proposals::where('code', $code)->first();
@endphp
{!! Form::open(['method' => 'put', 'url' => 'acc/proposal']) !!}
{!! Form::hidden('code', $code) !!}
<div class="row">
	<div class="col-md-2">
		Proposal from {{ App\Models\Robust::uuid2name($p->asker) }}
	</div>
	<div class="col-md-10">
		{!! $p->msg !!}
	</div>
</div>
<div class="row">
	<div class="col-md-2">
		<button type="submit" class="btn btn-success" name="pp" value="accept">Accept</button>
	</div>
	<div class="col-md-2">
		<button type="submit" class="btn btn-danger" name="pp" value="denie">Denie</button>
	</div>
	<div class="col-md-8">
	</div>
</div>
{!! Form::close() !!}
@endif
@endsection