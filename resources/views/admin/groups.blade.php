@extends('admin.app')
@section('admincontent')
@if (App\Models\Admin::isAdmin())
    @if (request()->has('g') && !empty(request()->input('g')))
        @php
        $g = request()->input('g');
        $group = App\Models\Robust::tbl('os_groups_groups')->where('GroupID', $g)->first();
        $gm = App\Models\Robust::tbl('os_groups_membership')->where('GroupID', $g)->get();
        $sq = null;
        if ($s = App\Models\Sqms::where('uuid', $g)->get()) {
            $sq = $s;
        }
        @endphp
        <h3 class="mb-4 text-3xl font-extrabold leading-none tracking-tight text-gray-900 md:text-5xl lg:text-6xl dark:text-white">{{ $group->Name }}</h3>
        <B>Founder:</B> {{ App\Models\Robust::uuid2name($group->FounderID) }}<br>
        {!! Form::open(['url' => 'admin/groups', 'method' => 'PUT']) !!}
        {!! Form::hidden('gid', $group->GroupID) !!}
        {!! Form::textarea('Charter', $group->Charter, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500']) !!}
        <button type="submit" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">Save</button>
        {!! Form::close() !!}
        {!! Form::open(['url' => 'admin/groups', 'method' => 'DELETE']) !!}
        {!! Form::hidden('gid', $group->GroupID) !!}
        Deleting a group will wipe out that group forever<br>
        <button type="submit" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">Delete</button>
        {!! Form::close() !!}
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <table class="w-full text-left rtl:text-right text-gray-500">
                    <thead>
                        <tr>
                            <th>Member Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gm as $g)
                            @php
                                $gname = App\Models\Robust::uuid2name($g->PrincipalID);
                            @endphp
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td>{{ $gname }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                <table class="w-full text-left rtl:text-right text-gray-500">
                    <thead>
                        <tr>
                            <th>Region Name</th>
                            <th>Parcel Name</th>
                            <th>Deeded Sqms</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sq as $sqms)
                            @php
                                $reg = App\Models\Robust::tbl('regions')->where('uuid',$sqms->region)->first();
                            @endphp
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                                <td>{{ $reg->regionName }}</td>
                                <td>{{ $sqms->parcel }}</td>
                                <td>{{ number_format($sqms->sqm) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        @php
        $groups = App\Models\Robust::tbl('os_groups_groups');
        $groups = $groups->where('Location', '');
        $s = "";
        if (request()->has('s') && !empty(request()->input('s'))) {
            $s = request()->input('s');
            $groups = $groups->where('Name', 'LIKE', '%'.$s.'%');
            $groups = $groups->orWhere('Charter', 'LIKE', '%'.$s.'%');
        }
        if (request()->has('o') && !empty(request()->input('o'))) {
            $o = request()->input('o');
            list($otype,$odir) = explode(".", $o);
        }else{
            $o = "Name.asc";
            $otype = "Name";
            $odir = "asc";
        }
        $groups = $groups->orderBy($otype, $odir);
        $count = $groups->count();
        $groups = $groups->paginate(50);
        $paging = $groups->appends(request()->except(['page']))->links();
        $orderlist = [
            'Name.asc' => 'Group Name A-Z', 'Name.desc' => 'Group Name Z-A',
        ];
        @endphp
        {!! Form::open(['method' => 'GET']) !!}
        {!! Form::text('s', $s, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'placeholder' => 'Search by Name or Description', 'onchange' => 'this.form.submit();']) !!}
        {!! Form::select('o', $orderlist, $o, ['class' => 'bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500', 'onchange' => 'this.form.submit();']) !!}
        {!! Form::close() !!}
        {!! $paging !!}
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-left rtl:text-right text-gray-500">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Founder</th>
                        <th>Members</th>
                        <th>Deeded Sqms</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($groups as $g)
                        @php
                            $gcount = App\Models\Robust::tbl('os_groups_membership')->where('GroupID', $g->GroupID)->count();
                            $gsqms = 0;
                            if ($gs = App\Models\Sqms::where('uuid', $g->GroupID)->get()) {
                                foreach($gs as $gsq) {
                                    $gsqms += $gsq->sqm;
                                }
                            }
                        @endphp
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700">
                            <td><a href="{{ url('admin/groups?g='.$g->GroupID) }}">{{ $g->Name }}</a></td>
                            <td>{{ App\Models\Robust::uuid2name($g->FounderID) }}</td>
                            <td>{{ number_format($gcount) }}</td>
                            <td>{{ number_format($gsqms) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {!! $paging !!}
    @endif
@endif
@endsection