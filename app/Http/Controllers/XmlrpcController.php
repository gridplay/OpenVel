<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Xml;
use Log;
class XmlrpcController extends Controller {
    public function xmlrpc() {
        $xml = Xml::process();
        return response($xml, 200)->header("Content-type", "application/xml");
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response('test', 200)->header("Content-type", "text/text");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response('test', 200)->header("Content-type", "text/text");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->xmlrpc();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return response('test', 200)->header("Content-type", "text/text");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return response('test', 200)->header("Content-type", "text/text");
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return response('test', 200)->header("Content-type", "text/text");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return response('test', 200)->header("Content-type", "text/text");
    }
}
