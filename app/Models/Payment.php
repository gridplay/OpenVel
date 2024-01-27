<?php
namespace App\Models;
use Illuminate\Http\Request;
use DB;
use Log;
use Auth;
use Str;
use Carbon\Carbon;
use App\Models\Tier;
use App\Models\User;
class Payment extends Core {
    protected $table = 'payment';
    public $incrementing = false;
    public $timestamps = false;
    public static function pay($tier_id = 1) {
        if (Auth::check()) {
            $user = Auth::user();
            if ($tier_id > 1) {
                if ($user->tier > $tier_id) {
                    self::where('paypal_id', $user->paypal_sub)->update(['cancelled' => time()]);
                }
                $tid = Str::uuid();
                $trans = randcode(8,24);
                $tier = Tier::where('id', $tier_id)->first();
                $tax = (env('TAX', 0) / 100) * $tier->cad;
                $price = $tier->cad + $tax;
                $payment = self::insert([
                    'id' => $tid,
                    'user_id' => $user->id, 
                    'transaction' => $trans, 
                    'tier' => $tier_id,
                    'amount' => $price,
                    'added' => time()
                ]);
                $success_url = url('paypal-success');
                $cancel_url = url('paypal-cancel');
                $dt = Carbon::now()->addHour();
                return ['error' => 'Unknown'];
            }else{
                User::where('id', Auth::id())->update(['paypal_sub' => null, 'tier' => 1, 'tier_expire' => 0]);
                return redirect('acc/settings');
            }
        }
        return ["status" => 'Not Logged In'];
    }
    public static function PaypalVerify(Request $request) {
        $id = $request->input('id');
        $stats = ["CANCELLED", "SUSPENDED"];
        if (!empty($id) && $id == env('PAYPAL_WEBHOOK_ID')) {
            $status = $request->input('event_type');
            if (strpos($status, ".") !== false) {
                list($billing, $sub, $stat) = explode(".", $status);
                if ($billing == "BILLING" && $sub == "SUBSCRIPTION") {
                    if (in_array($stat, $stats)) {
                        $resource = $request->input('resource');
                        if (in_array('id', $resource)) {
                            $pid = $resource['id'];
                            if ($p = self::where('paypal_id', $pid)->first()) {
                                self::where('paypal_id', $pid)->update(['cancelled' => time()]);
                                User::where('id', $p->user_id)->update(['paypal_sub' => null, 'tier' => 1, 'tier_expire' => 0]);
                                return "OK";
                            }
                        }
                    }
                }
            }
        }
        return "FAIL";
    }
}