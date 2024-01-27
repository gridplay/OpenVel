<?php
namespace App\Models;
use Illuminate\Http\Request;
use Auth;
use Log;
use App\Models\User;
use App\Models\Robust;
class Admin extends Core {
    public static function isPrem($id = null) {
        return false;
    }
    public static function isMod($id = null) {
        if (is_null($id) && Auth::check()) {
            $id = Auth::id();
        }
        if (!is_null($id)) {
            if ($user = User::find($id)) {
                if (Robust::getUserLevel($user->uuid) >= 100) {
                    return true;
                }
            }
        }
        return false;
    }
    public static function isAdmin($id = null) {
        if (is_null($id) && Auth::check()) {
            $id = Auth::id();
        }
        if (!is_null($id)) {
            if ($user = User::find($id)) {
                if (Robust::getUserLevel($user->uuid) == 250) {
                    return true;
                }
            }
        }
        return false;
    }
    public static function ValidReCaptcha() {
        return self::ValidCaptcha();
    }
    public static function ValidCaptcha(Request $request) {
        if ($request->has('g-recaptcha-response')) {
            $remoteIp = $request->getClientIp();
            $gRecaptchaResponse = $request->input('g-recaptcha-response');
            $conf = config('site.recaptcha');
            $recaptcha = new \ReCaptcha\ReCaptcha($conf['secret']);
            $resp = $recaptcha->setExpectedHostname('canadiangrid.ca')->verify($gRecaptchaResponse, $remoteIp);
            if ($resp->isSuccess()) {
                return true;
            }else{
                $errors = $resp->getErrorCodes();
            }
        }
        return false;
    }
}
