@extends('admin.app')
@section('admincontent')
@php
$newres = 0;
foreach (App\Models\Robust::tbl('UserAccounts')->orderBy('Created', 'desc')->get() as $newress) {
	if ($newress->Created > (time() - 604800)) {
		++$newres;
	}
}
@endphp
<div class="w-full border border-gray-200 rounded-lg shadow bg-gray-800 border-gray-700">
	<div id="fullWidthTabContent" class="border-t border-gray-200">
		<dl class="grid max-w-screen-xl grid-cols-2 gap-8 p-4 mx-auto sm:grid-cols-3 xl:grid-cols-6 text-white sm:p-8">
			<div class="flex flex-col items-center justify-center">
				<dt class="mb-2 text-3xl font-extrabold">
					{{ number_format((App\Models\Robust::tbl('UserAccounts')->count() - 1)) }}
				</dt>
				<dd class="text-gray-500 dark:text-gray-400">Residents</dd>
			</div>
			<div class="flex flex-col items-center justify-center">
				<dt class="mb-2 text-3xl font-extrabold">
					{{ number_format($newres) }}
				</dt>
				<dd class="text-gray-500 dark:text-gray-400">New Residents</dd>
			</div>
			<div class="flex flex-col items-center justify-center">
				<dt class="mb-2 text-3xl font-extrabold">
					{{ number_format(App\Models\Robust::tbl('Presence')->where('RegionID', '!=', App\Models\Robust::$NULL_KEY)->count()) }}
				</dt>
				<dd class="text-gray-500 dark:text-gray-400">Inworld</dd>
			</div>
			<div class="flex flex-col items-center justify-center">
				<dt class="mb-2 text-3xl font-extrabold">
					{{ number_format(App\Models\Robust::tbl('regions')->count()) }}
				</dt>
				<dd class="text-gray-500 dark:text-gray-400">Regions</dd>
			</div>
			<div class="flex flex-col items-center justify-center">
				<dt class="mb-2 text-3xl font-extrabold">
					{{ number_format(App\Models\Money::tbl('transactions')->count()) }}
				</dt>
				<dd class="text-gray-500 dark:text-gray-400">Transactions</dd>
			</div>
			<div class="flex flex-col items-center justify-center">
				<dt class="mb-2 text-3xl font-extrabold">
					{{ number_format(App\Models\Reports::count()) }}
				</dt>
				<dd class="text-gray-500 dark:text-gray-400">Reports</dd>
			</div>
		</dl>
	</div>
</div>
<div>
  <canvas id="newResidentChart"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@php
$label = [];
$oneday = 86400;
for ($i=0; $i < 8; $i++) { 
	$time = time() - ($oneday * $i);
	$label[] = App\Models\Core::time2date($time, 'j M Y');
}
$days = [];
foreach($label as $d) {
	$days[$d] = 0;
}
$data = [];
$res = App\Models\Robust::tbl('UserAccounts')->orderBy('Created', 'desc')->get();
foreach ($res as $r) {
	if ($r->Created > (time() - 604800)) {
		$day = App\Models\Core::time2date($r->Created, 'j M Y');
		if (array_key_exists($day, $days)) {
			$days[$day] += 1;
		}
	}
}
foreach ($days as $key => $value) {
	$data[] = $value;
}
$lj = json_encode($label);
$dj = json_encode($data);
@endphp
<script>
  const ctx = document.getElementById('newResidentChart');

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: {!! $lj !!},
      datasets: [{
        label: '# of NEW Residents',
        data: {!! $dj !!},
        borderWidth: 1,
        backgroundColor: '#ff0000'
      }]
    },
    options: {
    	animation: true,
		scales: {
			y: {
				beginAtZero: true
			}
		}
    }
  });
</script>
@endsection