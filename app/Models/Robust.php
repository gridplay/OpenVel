<?php
namespace App\Models;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use DB;
use App\Models\Admin;
use App\Models\User;
use Auth;
use Hash;
use Log;
class Robust extends Core {
	protected $connection = 'robust';
    public $incrementing = false;
    public $timestamps = false;
	public static $NULL_KEY = "00000000-0000-0000-0000-000000000000";
	public static $UserLevels = [
		0 => 'Resident',
		200 => 'Grid Helper',
		250 => 'Grid God'
	];
	public static function tbl($tbl = 'UserAccounts') {
		if (!empty($tbl)) {
			return self::from($tbl);
		}
		return null;
	}
	public static function find($uuid) {
		if ($u = self::tbl('UserAccounts')->where('PrincipalID', $uuid)->first()) {
			return $u;
		}
		return false;
	}
	public static function uuid2name($uuid) {
		if ($user = self::tbl('UserAccounts')->where('PrincipalID', $uuid)->first()) {
			return $user->FirstName." ".$user->LastName;
		}
		return "";
	}
	public static function name2key($name) {
		if (strpos($name, " ")) {
			list($first, $last) = explode(" ", $name);
		}else{
			$first = $name;
			$last = "Resident";
		}
		if ($user = self::tbl('UserAccounts')->where('FirstName', $first)->where('LastName', $last)->first()) {
			return $user->PrincipalID;
		}
		return "";
	}
	public static function getGridStats() {
		$monthago = time() - 2629800;
		$r = [];
		$r['Residents'] = (self::tbl('UserAccounts')->count() - 1);
		$r['New Joins'] = self::tbl('UserAccounts')->where('Created', '>', (time() - 604800))->count();
		$r['Online'] = self::tbl('Presence')->count();
		$r['30 days'] = self::tbl('GridUser')->where('Login', '>', $monthago)->count();
		$r['Regions'] = self::tbl('regions')->count();
		$r['Varregions'] = self::tbl('regions')->where('sizeX', '>', 256)->count();
		$r['Normal Regions'] = self::tbl('regions')->where('sizeX', 256)->count();
		$rsize = 0;
		$normalsim = 0;
		foreach (self::tbl('regions')->get() as $region) {
			$rsize += ($region->sizeX * $region->sizeY);
			if ($region->sizeX > 256) {
				$normalsim += (($region->sizeX / 256) + ($region->sizeY / 256));
			}else if ($region->sizeX == 256) {
				++$normalsim;
			}
		}
		$r['256 Sims'] = $normalsim;
		$r['Land Mass'] = $rsize;
		return $r;
	}
	public static function register(Request $request) {
		$r = [];
		if (Admin::ValidCaptcha($request)) {
			$email = $request->input('email');
			$email = str_replace('mailto:', '', $email);
			$first = $request->input('firstname');
			$last = "Resident";
			if ($request->has('lastname') && !empty($request->input('lastname'))) {
				$last = $request->input('lastname');
			}
			$reservednames = config('site.reservedNames');
			if (in_array($last, $reservednames)) {
				$r['type'] = 'yellow';
				$r['msg'] = 'Sorry last name reserved for employees ONLY';
			}else{
				if ($ru = self::tbl('UserAccounts')->where('FirstName', $first)->where('LastName', $last)->first()) {
					$r['type'] = 'red';
					$r['msg'] = 'That resident is already registered';
				}else if ($re = self::tbl('UserAccounts')->where('Email', $email)->first()) {
					$r['type'] = 'red';
					$r['msg'] = 'That email is already registered';
				}else{
					if (!empty($email)) {
						if ($request->input('password') == $request->input('cpassword')) {
							$passwordSalt = md5(randcode(8, 25));
							$passhash = md5(md5($request->input('password')).':'.$passwordSalt);
							$UUID = uuid();

							$ua = ['PrincipalID' => $UUID, 'ScopeID' => self::$NULL_KEY, 'Email' => $email,
							'FirstName' => $first, 'LastName' => $last, 'UserTitle' => '',
							'Created' => time(), 'ServiceURLs' => 'HomeURI= InventoryServerURI= AssetServerURI='];
							self::tbl('UserAccounts')->insert($ua);
							
							$uauth = ['UUID' => $UUID, 'passwordHash' => $passhash,
							'passwordSalt' => $passwordSalt, 'accountType' => 'UserAccount',
							'webLoginKey' => self::$NULL_KEY];
							self::tbl('auth')->insert($uauth);

							$homesim = config('site.HomeRegion');
							$homepos = config('site.HomePos');
							$ugu = ['UserID' => $UUID, 'HomeRegionID' => $homesim, 'HomePosition' => $homepos, 'HomeLookAt' => '<0,1,0>',
							'LastRegionID' => $homesim, 'LastPosition' => $homepos, 'LastLookAt' => '<0,1,0>', 'Online' => 'False'];
							self::tbl('GridUser')->insert($ugu);

							$ruth = "";
							if ($request->has('avi') && !empty($request->input('avi'))) {
								$ruth = $request->input('avi');
							}
							self::copyRuth($UUID, $ruth);

			                $tos = 0;
			                if ($request->has('tos') && !empty($request->input('tos'))) {
			                    $tos = 1;
			                }
							$u = User::create(['id' => uuid(), 'firstname' => $first, 'lastname' => $last, 'email' => $email, 
								'password' => Hash::make($request->input('password')), 'uuid' => $UUID, 'tos' => $tos]);
							Auth::login($u, false);
	                        event(new Registered($u));

		                	$r['type'] = "green";
		                	$r['msg'] = "Successfully registered and logged in<br>Your viewer login username is ".$first." ".$last;
						}else{
							$r['type'] = 'yellow';
							$r['msg'] = 'Passwords do not match';
						}
					}else{
						$r['type'] = 'red';
						$r['msg'] = 'Email required';
					}
				}
			}
		}else{
			$r['type'] = "red";
			$r['msg'] = "Captcha invalid";
		}
		return $r;
	}
	private static function copyRuth($uuid, $ruth = '') {
		if (empty($ruth)) {
			$ruth = config('site.Ruth');
		}
		// outfit
		foreach (self::tbl('Avatars')->where('PrincipalID', $ruth)->get() as $a) {
			self::tbl('Avatars')->insert(['PrincipalID' => $uuid, 'Name' => $a->Name, 'Value' => $a->Value]);
		}
		// inv folders and items
		$myinv = uuid();
		$ia = ['agentID' => $uuid, 'folderName' => 'My Inventory', 'type' => 8, 
		'version' => 21, 'folderID' => $myinv, 'parentFolderID' => self::$NULL_KEY];
		self::tbl('inventoryfolders')->insert($ia);
		if ($ruthparent = self::tbl('inventoryfolders')->where('agentID', $ruth)->where('folderName', 'My Inventory')->first()) {
			foreach(self::tbl('inventoryfolders')->where('agentID', $ruth)->where('parentFolderID', $ruthparent->folderID)->get() as $ai) {
				if ($ai->folderName != 'My Inventory' || $ai->folderName != '#Firestorm') {
					$fuuid = uuid();
					$aia = ['agentID' => $uuid, 'folderName' => $ai->folderName, 'type' => $ai->type, 
					'version' => $ai->version, 'folderID' => $fuuid, 'parentFolderID' => $myinv];
					self::tbl('inventoryfolders')->insert($aia);
					foreach(self::tbl('inventoryitems')->where('parentFolderID', $ai->folderID)->get() as $ii) {
						$inv = ['assetID' => $ii->assetID, 'assetType' => $ii->assetType, 'inventoryName' => $ii->inventoryName,
						'inventoryDescription' => $ii->inventoryDescription, 'inventoryNextPermissions' => $ii->inventoryNextPermissions,
						'inventoryCurrentPermissions' => $ii->inventoryCurrentPermissions, 'invType' => $ii->invType, 'creatorID' => $ii->creatorID,
						'inventoryBasePermissions' => $ii->inventoryBasePermissions, 'inventoryEveryOnePermissions' => $ii->inventoryEveryOnePermissions,
						'salePrice' => $ii->salePrice, 'saleType' => $ii->saleType, 'creationDate' => $ii->creationDate, 'groupID' => $ii->groupID, 'groupOwned' => $ii->groupOwned,
						'flags' => $ii->flags, 'inventoryID' => uuid(), 'avatarID' => $uuid, 'parentFolderID' => $fuuid, 'inventoryGroupPermissions' => $ii->inventoryGroupPermissions];
						self::tbl('inventoryitems')->insert($inv);
					}
				}
			}
		}
	}
	public static function forgotPassword(Request $request) {
		$r = [];
		if (Admin::ValidCaptcha($request)) {
			if ($request->has('email')) {
				if ($ru = self::tbl('UserAccounts')->where('Email', $request->input('email'))->first()) {
					if ($lu = User::findbyuuid($ru->PrincipalID)) {
						if (!is_null($lu->email_verified_at) || !empty($lu->email_verified_at)) {
							$code = randcode(8, 12);
							DB::table('email_reset')->insert(['email' => $ru->Email, 'code' => $code, 'expire' => (time() + 900)]);
							$subj = env('APP_NAME')." password reset";
							$msg = "Here is the link to reset your password<br>";
							$msg .= '<a href="'.url("auth/reset?code=".$code).'">'.url("auth/reset?code=".$code).'</a>';
							$msg .= "<br>This link expires in 15 minutes";
							parent::sendemail($ru->Email, $subj, $msg);

							$r['type'] = "green";
							$r['msg'] = "Email with a link to reset has been sent<br>Check your junk/spam folder for a email from us";
						}else{
							$r['type'] = 'red';
							$r['msg'] = 'Email validation REQUIRED!';
						}
					}else{
						$r['type'] = 'yellow';
						$r['msg'] = 'Site login required!';
					}
				}else{
					$r['type'] = "red";
					$r['msg'] = "Email not found";
				}
			}else{
				$r['type'] = "yellow";
				$r['msg'] = "Email address required";
			}
		}else{
			$r['type'] = "red";
			$r['msg'] = "Invalid Captcha";
		}
		return $r;
	}
	public static function resetPassword(Request $request) {
		$r = [];
		if (Admin::ValidCaptcha($request)) {
			if (request()->input('newpsswrd') == request()->input('cnewpsswrd')) {
				if ($e = DB::table('email_reset')->where('code', request()->input('code'))->first()) {
					if($e && $e->expires > time()) {
						if ($ru = self::tbl('UserAccounts')->where('Email', $e->email)->first()) {
							$password = request()->input('newpsswrd');
							$passwordSalt = md5(randcode(8, 25));
							$passhash = md5(md5($password).':'.$passwordSalt);
							$uauth = ['passwordHash' => $passhash, 'passwordSalt' => $passwordSalt];
							self::tbl('auth')->where('UUID', $ru->PrincipalID)->update($uauth);
							User::where('uuid', $ru->PrincipalID)->update(['password' => Hash::make($password)]);
							$r['type'] = 'green';
							$r['msg'] = 'Password reset successfully';
						}else{
							$r['type'] = 'red';
							$r['msg'] = 'Account not found';
						}
					}else{
						$r['type'] = 'yellow';
						$r['msg'] = 'Code expired';
					}
				}else{
					$r['type'] = 'red';
					$r['msg'] = 'Reset invalid';
				}
			}else{
				$r['type'] = 'red';
				$r['msg'] = 'Passwords do not match';
			}
		}else{
			$r['type'] = "red";
			$r['msg'] = "Captcha invalid";
		}
		return $r;
	}
	public static function getProfilePic($uuid) {
		if ($p = self::tbl('userprofile')->where('useruuid', $uuid)->first()) {
			$i = parent::getTexture($p->profileImage);
			return $i;
		}
		return '';
	}
	public static function getUserLevel($uuid) {
		if ($user = self::tbl('UserAccounts')->where('PrincipalID', $uuid)->first()) {
			return $user->UserLevel;
		}
		return 0;
	}
	public static function getUserRole($uuid = '') {
		if (empty($uuid)) {
			$uuid = Auth::user()->uuid;
		}
		$level = self::getUserLevel($uuid);
		switch ($level) {
			case 250:
				return 'Grid God';
				break;
			case 200:
				return 'Grid Helper';
				break;
			case 150:
				return 'Grid Liaison';
				break;
			case 100:
				return 'CSR';
				break;
			case -1:
				return "Banned";
				break;
			
			default:
				return 'Resident';
				break;
		}
	}
	public static function kickuser($uuid) {
		$r = [];
		if ($ru = self::tbl('UserAccounts')->where('PrincipalID', $uuid)->first()) {
			if ($pres = self::tbl('Presence')->where('UserID', $uuid)->first()) {
				self::tbl('Presence')->where('UserID', $uuid)->delete();
				self::tbl('GridUser')->where('UserID', $uuid)->update(['Online' => 'False']);
				$r['type'] = "success";
				$r['msg'] = "User kicked";
			}
		}
		return $r;
	}
	public static function getSimInfo($p = '') {
		$d = request()->only(['gridx', 'gridy']);
		$r = 'Empty spot<br>Could be a varregion';
		if (request()->has('gridx') && request()->has('gridy')){
			if ($region = self::tbl('regions')->where('locX', $d['gridx'])->where('locY', $d['gridy'])->first()) {
				$r = 'Region: '.$region->regionName;
				if ($p = self::tbl('Presence')->where('RegionID', $region->uuid)->first()) {
					$r .= '<br>Avatars: '.self::tbl('Presence')->where('RegionID', $region->uuid)->count();
				}else{
					$r .= '<br>Avatars: 0';
				}
				$r .= '<br>Size: '.$region->sizeX.' x '.$region->sizeY;
			}
			$r .= '<br>X:'.($d['gridx'] / 256).' Y:'.($d['gridy'] / 256);
		}
		return $r;
	}
	public static function region_restarter($region_uuid) {
		try {
			return;
		}finally{
			if ($r = self::tbl('regions')->where('uuid', $region_uuid)->first()) {
				$req = ['code' => 'L0RoYnBS', 'region' => $region_uuid, 'msg' => 'Test Restart', 'delay' => 120];
				$xml = xmlrpc_encode($req);
				$head = ['content-type' => 'text/xml'];
				$send = ['body' => $xml];
				//$test1 = parent::senddata('post', $r->serverURI.'webrestarter', $send, $head);
				//Log::debug($test1);
			}
		}
	}
}