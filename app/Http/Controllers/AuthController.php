<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Models\User;
use App\Models\Robust;
use App\Models\Admin;
use Hash;
use Auth;
class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return redirect('/');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect('/');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // registration
        if ($request->has('email') && !empty($request->input('email'))) {
            $r = Robust::register($request);
            return view('index')->withError($r);
        }
        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id = '')
    {
        if ($id == "logout") {
            Auth::logout();
            return redirect('/');
        }
        if ($id == "login") {
            return view('auth.login');
        }
        if ($id == "register") {
            return redirect('join');
        }
        if ($id == "join") {
            return view('auth.reg');
        }
        if ($id == "forgot") {
            return view('auth.forgot');
        }
        return redirect('/');
    }
    public function join() {
        return view('auth.reg');
    }
    public function login() {
        return view('auth.login');
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id = '')
    {
        return redirect('/');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if ($id == "login") {
            // login
            if (Admin::ValidCaptcha($request)) {
                $email = $request->input('email');
                $pass = $request->input('password');
                $rem = false;
                if ($request->has('reme') && !empty($request->input('reme'))) {
                    $rem = true;
                }
                if ($user = User::where('email', $email)->first()) {
                    if (Hash::check($pass, $user->password)) {
                        Auth::login($user, $rem);
                        return view('acc.settings')->withError(['type' => 'green', 'msg' => 'Login successful']);
                    }else{
                        return view('index')->withError(['type' => 'red', 'msg' => 'Password invalid']);
                    }
                }else{
                    if ($ru = Robust::tbl('UserAccounts')->where('Email', $email)->first()) {
                        $gu = Robust::tbl('auth')->where('UUID', $ru->PrincipalID)->first();
                        $hashcheck = md5(md5($pass).":".$gu->passwordSalt);
                        if ($hashcheck == $gu->passwordHash) {
                            $user = User::create(['id' => uuid(), 
                                'email' => $email,
                                'uuid' => $ru->PrincipalID,
                                'firstname' => $ru->FirstName, 'lastname' => $ru->LastName,
                                'password' => Hash::make($pass)]);
                            Auth::login($user, $rem);
                            event(new Registered($user));
                            return view('acc.settings')->withError(['type' => 'green', 'msg' => 'Login successful']);
                        }
                    }else{
                        return view('index')->withError(['type' => 'red', 'msg' => 'Unable to find your email address']);
                    }
                }
            }else{
                return view('index')->withError(['type' => 'red', 'msg' => 'Invalid Captcha']);
            }
            return redirect('/');
        }
        if ($id == "forgot") {
            $r = Robust::forgotPassword($request);
            return view('index')->withError($r);
        }
        if ($id == "reset") {
            $e = Robust::resetPassword($request);
            return view('index')->withError($e);
        }
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return redirect('/');
    }
}
