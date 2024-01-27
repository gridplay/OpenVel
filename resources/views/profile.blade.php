@extends('layout.app')
@section('title', 'Profiles')
@section('content')
@if (isset($id) && !empty($id))
	@php
	$p = App\Models\Robust::tbl('UserAccounts');
	$first = $id;
	$last = "Resident";
	if (strpos($id, ".")) {
		list($first, $last) = explode(".", $id);
	}
	$p = $p->where('FirstName', $first)->where('LastName', $last);
	$p = $p->first();
	if ($p) {
		$partner = "";
		$ptxt = "";
		$ftxt = "";
		$ppic = "";
		$fpic = "";
		$groups = [];
		$picks = [];
		if ($prof = App\Models\Robust::tbl('userprofile')->where('useruuid', $p->PrincipalID)->first()) {
			$ppic = App\Models\Core::getTexture($prof->profileImage);
			$fpic = App\Models\Core::getTexture($prof->profileFirstImage);
			if (!empty($prof->profilePartner) && $prof->profilePartner != App\Models\Robust::$NULL_KEY) {
				if ($part = App\Models\Robust::tbl('UserAccounts')->where('PrincipalID', $prof->profilePartner)->first()) {
					$partner = $part->FirstName.".".$part->LastName;
					if ($part->LastName == "Resident") {
						$partner = $part->FirstName;
					}
				}
			}
			$picks = App\Models\Robust::tbl('userpicks')->where('creatoruuid', $p->PrincipalID)->get();
			if (!empty($prof->profileAboutText)) {
				$ptxt = str_replace("\n", "<br>", $prof->profileAboutText);
			}
			$ftxt = "";
			if (!empty($prof->profileFirstText)) {
				$ftxt = str_replace("\n", "<br>", $prof->profileFirstText);
			}
		}
	}
	@endphp
	<div class="">
		<div class="">
			@if ($p)
				<h4 class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">{{ $p->FirstName }} {{ $p->LastName }}</h4>
				<B>Profile Picture</B>
				@if (!empty($ppic))
					<br><img class="h-auto max-w-xs" src="{{ $ppic }}" alt="Profile photo">
				@endif
				<hr class="h-px my-8 bg-gray-200 border-0">
				<B>Biography</B>
				<br>{!! $ptxt !!}
				<hr class="h-px my-8 bg-gray-200 border-0">
				@if (!empty($partner))
					<B>Partner</B><br><a href="{{ url('u/'.$partner) }}">{{ $partner }}</a>
					<hr class="h-px my-8 bg-gray-200 border-0">
				@endif
				<B>Rez Day</B><br>{{ App\Models\Core::time2date($p->Created) }}
				<hr class="h-px my-8 bg-gray-200 border-0">
				<B>Real World Biography</B>
				<br>{!! $ftxt !!}
				<hr class="h-px my-8 bg-gray-200 border-0">
				<B>First Life Picture</B>
				@if (!empty($fpic))
					<br><img class="h-auto max-w-xs" src="{{ $fpic }}" alt="Firstlife photo">
				@endif
			@else
				<h1 class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Resident not found</h1>
			@endif
		</div>
	</div>
@else
<h1 class="mb-4 text-4xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl">Resident not found</h1>
@endif
@endsection