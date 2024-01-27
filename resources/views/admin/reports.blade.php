@extends('admin.app')
@section('admincontent')
@if (App\Models\Admin::isAdmin())
	@if (request()->has('r') && $report = App\Models\Reports::where('id', request()->input('r'))->first())
		@php
			$search = '/hop:\/\/(.*)/';
			$hop = '<a href="hop://${1}">${1}</a>';
			$details = preg_replace($search, $hop, $report->details);
			$usersdb = App\Models\Robust::tbl('UserAccounts');
			$reporter = $usersdb->where('PrincipalID', $report->reporter)->first();
			$abuser = $usersdb->where('PrincipalID', $report->abuser)->first();
		@endphp
		<section class="bg-gray-50">
	    	<div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
	        	<div class="w-full bg-white rounded-lg shadow md:mt-0 sm:max-w-md xl:p-0">
	        		<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Summary</label>
	        		<div class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
	        			{!! $report->summary !!}
	        		</div>
	        		<hr class="h-px my-8 bg-gray-200 border-0">
	        		@if ($reporter)
	        			<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Reporter</label>
	        			<div class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
	        				<a href="{{ url('admin/residents?u='.$reporter->PrincipalID) }}">{{ $reporter->FirstName }} {{ $reporter->LastName }}</a>
	        			</div>
	        		@endif
	        		@if ($abuser)
	        			<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Abuser</label>
	        			<div class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
	        				<a href="{{ url('admin/residents?u='.$abuser->PrincipalID) }}">{{ $abuser->FirstName }} {{ $abuser->LastName }}</a>
	        			</div>
	        		@endif
	        		<hr class="h-px my-8 bg-gray-200 border-0">
	        		<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Details</label>
	        		<div class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
	        			{!! $details !!}
	        		</div>
	        		<hr class="h-px my-8 bg-gray-200 border-0">
	        		<label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Posted</label>
	        		<div class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5">
	        			{{ App\Models\Core::time2date($report->posted) }}
	        		</div>
	        	</div>
	        </div>
	    </section>
	    {!! Form::open(['method' => 'delete', 'url' => 'admin/reports']) !!}
	    	{!! Form::hidden('rid', $report->id) !!}
	    	<button type="submit" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2">DELETE REPORT</button>
	    {!! Form::close() !!}
	@else
		@php
		$reports = new App\Models\Reports();
		$otype = "posted";
		$odir = "desc";
		$reports = $reports->orderBy($otype, $odir);
		$reports = $reports->paginate(50);
		$paging = $reports->appends(request()->except(['page']))->links();
		@endphp
		<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
			{!! $paging !!}
			<table class="w-full text-sm text-left rtl:text-right text-gray-500">
				<thead>
					<tr>
						<th>Summary</th>
						<th>Details</th>
					</tr>
				</thead>
				<tbody>
					@foreach($reports as $r)
						<tr class="odd:bg-white bg-gray-300 border-b">
							<td>
								<a href="{{ url('admin/reports?r='.$r->id) }}">
									{{ $r->summary }}
								</a>
							</td>
							<td>{{ $r->details }}</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			{!! $paging !!}
		</div>
	@endif
@endif
@endsection