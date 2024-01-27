<?php
namespace App\Http\Controllers;
use Illuminate\Auth\Events\Registered;
use Request;
use Response;
use App\Models\User;
use Auth;
use DB;
use Hash;
use Log;
use Storage;
use App\Models\Core;
use App\Models\Robust;
use App\Models\Proposals;
class AccController extends Controller {
    public function missingMethod($params = array()) {
        $notfound = ["error" => "Page not found"];
        return view('errors.404')->with('notfound', $notfound);
    }
    // GET Requests
    public function index() {
        if (Auth::check()) {
            return view('acc.settings');
        }else{
            return redirect('/');
        }
    }
    public function show($t="") {
        if (Auth::check()) {
            if (view()->exists('acc.'.$t)) {
                return view('acc.'.$t);
            }else{
                return view('acc.settings');
            }
        }else{
            return redirect('/');
        }
    }
    // PUT
    public function update($id) {
        if (Auth::check()) {
            $u = Auth::user();
            if ($id == "changepassword") {
                $data = request()->only(['cpsswrd', 'newpsswrd', 'cnewpsswrd']);
                if (!empty($data['cpsswrd'])) {
                    if ($data['newpsswrd'] == $data['cnewpsswrd']) {
                        if (Hash::check($data['cpsswrd'], $u->password)) {
                            User::where('id', $u->id)->update(['password' => Hash::make($data['newpsswrd'])]);
                            
                            $passwordSalt = md5(randcode(8, 25));
                            $passhash = md5(md5($data['newpsswrd']).':'.$passwordSalt);
                            $uauth = ['passwordHash' => $passhash,'passwordSalt' => $passwordSalt];
                            Robust::tbl('auth')->where('UUID', $u->uuid)->update($uauth);
                            $ret = ['type' => 'green', 'msg' => 'Password has been changed'];
                        }else{
                            $ret = ['type' => 'red', 'msg' => 'Invalid current password'];
                        }
                    }else{
                        $ret = ['type' => 'red', 'msg' => 'new passwords did not match'];
                    }
                }else{
                    $ret = ['type' => 'yellow', 'msg' => 'Current Password is empty.'];
                }
                return view('acc.settings')->withError($ret);
            }
            if ($id == "settings") {
                $d = request()->only(['email']);
                User::where('id', $u->id)->update($d);
                if ($u->email != $d['email']) {
                    User::where('id', $u->id)->update(['email_verified_at' => null]);
                    Robust::tbl('UserAccounts')->where('PrincipalID', $u->uuid)->update(['Email' => $d['email']]);
                    $newu = User::find($u->id);
                    event(new Registered($newu));
                }
                return view('acc.settings');
            }
            if ($id == "friendlist") {
                $fs = request()->input('fs');
                $friend = request()->input('friend');
                if ($fs == "remove" || $fs == "denied") {
                    Robust::tbl('Friends')->where('PrincipalID', $u->uuid)->where('Friend', $friend)->delete();
                    Robust::tbl('Friends')->where('PrincipalID', $friend)->where('Friend', $u->uuid)->delete();
                }
                if ($fs == "accept") {
                    Robust::tbl('Friends')->where('PrincipalID', $u->uuid)->where('Friend', $friend)->update(['Offered' => 0]);
                    Robust::tbl('Friends')->insert(['PrincipalID' => $friend, 'Friend' => $u->uuid, 'Flags' => 1, 'Offered' => 0]);
                }
                if ($fs == "propose") {
                    $pid = Proposals::createproposal($u->uuid, $friend);
                    return view('acc.proposal')->with('id', $pid);
                }
                if ($fs == "divorce") {
                    Robust::tbl('userprofile')->where('useruuid', $u->uuid)->update(['profilePartner' => Robust::$NULL_KEY]);
                    Robust::tbl('userprofile')->where('useruuid', $friend)->update(['profilePartner' => Robust::$NULL_KEY]);
                }
                return view('acc.friendlist');
            }
            if ($id == "proposal") {
                if (request()->has('id')) {
                    $pid = request()->input('id');
                    $msg = request()->input('msg');
                    $code = randcode(8, 24);
                    Proposals::where('id', $pid)->update(['msg' => $msg, 'code' => $code]);
                    $p = Proposals::where('id', $pid)->first();
                    $subj = env('APP_NAME')." Marriage Proposal";
                    $who = Robust::uuid2name($p->asker);
                    $msg = $who." has proposed to you<br>";
                    $msg .= '<a href="'.url("acc/proposal?code=".$code).'">'.url("acc/proposal?code=".$code).'</a>';
                    $msg .= "<br>This link expires in 15 minutes";
                    $ru = Robust::find($p->receiver);
                    Core::sendemail($ru->Email, $subj, $msg);
                }else if (request()->has('code')) {
                    $pp = request()->input('pp');
                    $code = request()->input('code');
                    if ($pp == "denied") {
                        Proposals::where('code', $code)->delete();
                    }
                    if ($pp == "accept") {
                        if ($p = Proposals::where('code', $code)->first()) {
                            Robust::tbl('userprofile')->where('useruuid', $p->asker)->update(['profilePartner' => $p->receiver]);
                            Robust::tbl('userprofile')->where('useruuid', $p->receiver)->update(['profilePartner' => $p->asker]);
                            Proposals::where('code', $code)->delete();
                        }
                    }
                }
                return view('acc.friendlist');
            }
        }else{
            return redirect('/');
        }
    }
    // DELETE
    public function destroy($id) {
        if (Auth::check()) {
            $u = Auth::user();
            if ($id == "offlineim") {
                $mid = request()->input('id');
                Robust::tbl('im_offline')->where('ID', $mid)->delete();
            }
            return redirect('acc/'.$id);
        }
        return redirect('/');
    }
}
