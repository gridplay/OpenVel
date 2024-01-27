<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class ProfilesController extends Controller
{
    // GET
    public function index()
    {
        return view('profile');
    }
    public function show($id)
    {
        return view('profile')->with('id', $id);
    }
    public function edit($id)
    {
        return view('profile');
    }
    public function create()
    {
        return view('profile');
    }

    // PUT
    public function update($id)
    {
        return view('profile');
    }
    // POST
    public function store(Request $request)
    {
        return view('profile');
    }
    // DELETE
    public function destroy($id)
    {
        return view('profile');
    }
}
