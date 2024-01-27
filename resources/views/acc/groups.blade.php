@extends('acc.app')
@section('usercontent')
@if(Auth::check())
<h3 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Groups List</h3>
@php
	$u = Auth::user();
	$groups = App\Models\Robust::tbl('os_groups_membership')->where('PrincipalID', $u->uuid);
	$groups = $groups->join('os_groups_groups', 'os_groups_membership.GroupID', '=', 'os_groups_groups.GroupID');
	$groups = $groups->orderBy('os_groups_groups.Name', 'asc');
	$groups = $groups->paginate(10);
@endphp
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
	<table class="w-full text-left rtl:text-right text-gray-500">
		<thead>
			<tr>
				<th>Name</th>
				<th>Members</th>
				<th>Deeded Sqms</th>
			</tr>
		</thead>
		<tbody>
			@foreach($groups as $g)
				@php
					$group = App\Models\Robust::tbl('os_groups_groups')->where('GroupID', $g->GroupID)->first();
					$gcount = App\Models\Robust::tbl('os_groups_membership')->where('GroupID', $g->GroupID)->count();
					$gsqms = 0;
					if ($gs = App\Models\Sqms::where('uuid', $g->GroupID)->get()) {
						foreach($gs as $gsq) {
							$gsqms += $gsq->sqm;
						}
					}
				@endphp
				<tr class="odd:bg-white bg-gray-300 border-b">
					<td>{{ $group->Name }}</td>
					<td>{{ number_format($gcount) }}</td>
					<td>{{ number_format($gsqms) }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>
</div>
{!! $groups->appends(request()->except(['page']))->links() !!}
@endif
@endsection