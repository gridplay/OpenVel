@extends('layout.app')
@section('title', 'How To\'s')
@section('content')
<div class="mb-4 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-tab" data-tabs-toggle="#default-tab-content" role="tablist">
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="connect-tab" data-tabs-target="#connect" type="button" role="tab" aria-controls="connect" aria-selected="false">Connect</button>
        </li>
        <li class="me-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="kitely-tab" data-tabs-target="#kitely" type="button" role="tab" aria-controls="kitely" aria-selected="false">Kitely Marketplace</button>
        </li>
    </ul>
</div>
<div id="default-tab-content">
    <div class="hidden p-4 rounded-lg" id="connect" role="tabpanel" aria-labelledby="connect-tab">
		@include('parts.connect')
    </div>
    <div class="hidden p-4 rounded-lg" id="kitely" role="tabpanel" aria-labelledby="kitely-tab">
		@include('parts.kitely')
	</div>
</div>
@endsection