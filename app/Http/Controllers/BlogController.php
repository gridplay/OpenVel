<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Auth;
use Str;
use File;
use App\Models\Blog;
use App\Models\Admin;
class BlogController extends Controller
{
    // GET
    public function index()
    {
        return view('blogs.index');
    }
    public function show($id)
    {
        return view('blogs.index')->with('id', $id);
    }
    public function edit($id)
    {
        if (Admin::isMod()) {
            return view('blogs.edit')->with('id', $id);
        }
        return view('blogs.index')->with('id', $id);
    }
    public function create()
    {
        if (Admin::isMod()) {
            return view('blogs.create');
        }
        return view('blogs.index');
    }

    // PUT
    public function update($id)
    {
        if (Admin::isMod()) {
            $d = request()->only(['title', 'blog']);
            $d['edited'] = time();
            Blog::Where('id', $id)->update($d);
            return view('blogs.index')->with('id', $id);
        }
        return view('blogs.index');
    }
    // POST
    public function store(Request $request)
    {
        if (Admin::isMod()) {
            $d = request()->only(['title', 'blog']);
            $d['poster'] = Auth::id();
            $d['posted'] = time();
            $d['edited'] = time();
            $id = Blog::insertGetId($d);
            return view('blogs.index')->with('id', $id);
        }
        return view('blogs.index');
    }
    // DELETE
    public function destroy($id)
    {
        if (Admin::isMod()) {
            Blog::Where('id', $id)->delete();
        }
        return view('blogs.index');
    }
}
