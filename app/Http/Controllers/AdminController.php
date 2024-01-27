<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Response;
use DB;
use Auth;
use Log;
use Hash;
use File;
use Storage;
use App\Models\User;
use App\Models\Core;
use App\Models\Admin;
use App\Models\Robust;
use App\Models\Reports;
class AdminController extends Controller {
	public function missingMethod($params = array()) {
		$notfound = ["error" => "Page not found"];
		return view('errors.404')->with('notfound', $notfound);
	}
	// GET
	public function index() {
		if (Admin::isAdmin()) {
			return view('admin.index');
		}
		return redirect('/');
	}
	public function show($id) {
		if (Admin::isAdmin()) {
			if (view()->exists('admin.'.$id)) {
				return view('admin.'.$id);
			}else{
				return view('admin.index');
			}
		}
		return redirect('/');
	}
	// POST
	public function store() {
		$ret = "error";
		return Response::make($ret)->header('Content-Type', 'text/html', 'charset', 'utf-8');
	}
	// PUT
	public function update($id) {
		if (Admin::isAdmin()) {
			$r = [];
			if ($id == "users") {
				if (request()->has('uid')) {
					$uid = request()->input('uid');
					$d = request()->only(['']);
					if ($user = User::find($uid)) {
						User::where('id', $uid)->update($d);
						$r['msg'] = 'User updated';
					}
					$r['id'] = $uid;
				}
			}
			if ($id == "residents") {
				if (request()->has('uid') && !empty(request()->input('uid'))) {
					$uid = request()->input('uid');
					$d = request()->only(['UserLevel', 'Email']);
					if ($user = Robust::find($uid)) {
						Robust::tbl('UserAccounts')->where('PrincipalID', $uid)->update($d);
						$r['msg'] = 'Resident updated';
					}
					$r['id'] = $uid;
				}
			}
			if ($id == "kickuser") {
				$id = 'inworld';
				if (request()->has('uuid') && !empty(request()->input('uuid'))) {
					$r = Robust::kickuser(request()->input('uuid'));
				}
			}
			if ($id == "passwordreset") {
				if (request()->has('uuid') && !empty(request()->input('uuid'))) {
					$r = Robust::Adminpasswordreset(request()->input('uuid'));
				}
				$id = 'residents';
			}
			if ($id == "groups") {
				if (request()->has('gid') && !empty(request()->input('gid'))) {
					$d = request()->only(['Charter']);
					Robust::tbl('os_groups_groups')->where('GroupID', request()->input('gid'))->update($d);
				}
			}
			if ($id == "restart") {
				$rid = request()->input('regionid');
				Robust::region_restarter($rid);
				return redirect('admin/regions?u='.$rid);
			}
			$v = view('admin.'.$id);
			if (isset($r['id'])) {
				$v = $v->with('id',$r['id']);
			}
			if (isset($r['type'])) {
				$v = $v->withError($r);
			}
			return $v;
		}
		return redirect('/');
	}
	// DELETE
	public function destroy($id) {
		if (Admin::isAdmin()) {
			if ($id == "residents") {
				$uuid = request()->input('uuid');
				$userid = request()->input('id');
				// [table => uuid field]
				$da = [];
				foreach ($da as $dn => $df) {
					DB::table($dn)->where($df, $userid)->delete();
				}
				$ra = ["os_groups_groups" => "FounderID",
						"classifieds" => "creatoruuid",
						"userprofile" => "useruuid",
						"usersettings" => "useruuid",
						"userpicks" => "creatoruuid",
						"usernotes" => "useruuid",
						"userdata" => "UserId",
						"UserAccounts" => "PrincipalID",
						"GridUser" => "UserID",
						"Friends" => "PrincipalID",
						"Friends" => "Friend",
						"Avatars" => "PrincipalID",
						"auth" => "UUID",
						"inventoryitems" => "avatarID",
						"inventoryfolders" => "agentID"];
				foreach ($ra as $rn => $rf) {
					if ($rn == "os_groups_groups") {
						foreach(Robust::tbl($rn)->where($rf, $uuid)->get() as $g) {
							$gid = $g->GroupID;
							Robust::tbl('os_groups_membership')->where('GroupID', $gid)->delete();
							Robust::tbl('os_groups_notices')->where('GroupID', $gid)->delete();
							Robust::tbl('os_groups_invites')->where('GroupID', $gid)->delete();
							Robust::tbl('os_groups_rolemembership')->where('GroupID', $gid)->delete();
							Robust::tbl('os_groups_roles')->where('GroupID', $gid)->delete();
							Robust::tbl('os_groups_groups')->where('GroupID', $gid)->delete();
						}
					}else{
						Robust::tbl($rn)->where($rf, $uuid)->delete();
					}
				}
				User::where('uuid', $uuid)->delete();
			}
			if ($id == "offlineim") {
				$mid = request()->input('id');
				Robust::tbl('im_offline')->where('ID', $mid)->delete();
			}
			if ($id == "groups") {
				if (request()->has('gid')) {
					$gid = request()->input('gid');
					Robust::tbl('os_groups_membership')->where('GroupID', $gid)->delete();
					Robust::tbl('os_groups_notices')->where('GroupID', $gid)->delete();
					Robust::tbl('os_groups_invites')->where('GroupID', $gid)->delete();
					Robust::tbl('os_groups_rolemembership')->where('GroupID', $gid)->delete();
					Robust::tbl('os_groups_roles')->where('GroupID', $gid)->delete();
					Robust::tbl('os_groups_groups')->where('GroupID', $gid)->delete();
				}
			}
			if ($id == "reports") {
				$rid = request()->input('rid');
				Reports::where('id', $rid)->delete();
			}
			return view('admin.'.$id);
		}
		return redirect('/');
	}
}
