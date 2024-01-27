<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Payment;
use Log;
class TierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('tier');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tier');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('tier')) {
            return Payment::pay($request->input('tier'));
        }
        return view('tier');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('tier');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('tier');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return view('tier');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return view('tier');
    }

    public function paypal(Request $request) {
        $pp = Payment::pay($request);
        return response($pp, 200)->header("Content-type", "text/text");
    }
}
