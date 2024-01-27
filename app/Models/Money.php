<?php
namespace App\Models;
use DB;
use Auth;
use App\Models\Robust;
use App\Models\Core;
class Money extends Core {
	protected $connection = 'robust';
    public $incrementing = false;
    public $timestamps = false;
	public static function tbl($tbl = 'balances') {
		return self::from($tbl);
	}
	public static function getBal($uuid = '') {
		if (empty($uuid)) {
			if (Auth::check()) {
				$uuid = Auth::user()->uuid;
			}
		}
		if (!empty($uuid)) {
			if ($bal = self::tbl('balances')->where('user', $uuid)->first()) {
				return $bal->balance;
			}
		}
		return 0;
	}
	public static function processSecureAPIPost($p = "") {
		$r = ['uri' => $p];
		if ($p == "getbal") {
			if (request()->has('uuid')) {
				$uuid = request()->input('uuid');
				if ($bal = self::tbl('balances')->where('user', $uuid)->first()) {
					$r["balance"] = $bal->balance;
				}
			}
		}
		if ($p == "pay") {
			$sender = request()->input('sender');
			$receiver = request()->input('receiver');
			$amount = request()->input('amount');
			$se = self::tbl('balances')->where('user', $sender)->first();
			if ($se->balance >= $amount) {
				self::tbl('balances')->where('user', $sender)->decrement('balance', $amount);
				self::tbl('balances')->where('user', $receiver)->increment('balance', $amount);
				self::addTransaction($sender, $receiver, $amount, "d12b0fa7-90ae-4d6f-b1ff-f830c9719d63", "Marketplace - ".request()->input('item'));
				$r['status'] = true;
			}else{
				$r['status'] = false;
			}
		}
		return $r;
	}
	public static function pay($sender, $receiver, $amount, $type = "Website Pay", $regionid = '') {
		if (empty($regionid)) {
			$regionid = Robust::$NULL_KEY;
		}
		$se = self::tbl('balances')->where('user', $sender)->first();
		if ($se->balance >= $amount) {
			self::tbl('balances')->where('user', $sender)->decrement('balance', $amount);
			if (!is_null($receiver)) {
				self::tbl('balances')->where('user', $receiver)->increment('balance', $amount);
				self::addTransaction($sender, $receiver, $amount, $regionid, $type);
			}else{
				self::addTransaction($sender, null, $amount, $regionid, $type);
			}
			return true;
		}
		return false;
	}
	public static function addTransaction($sender, $receiver, $amount, $regionUUID, $desc) {
		$in = ['UUID' => uuid()];
		$in['sender'] = $sender;
		$in['receiver'] = $receiver;
		$in['amount'] = $amount;
		$in['senderBalance'] = self::tbl('balances')->where('user', $sender)->first()->balance;
		$in['receiverBalance'] = self::tbl('balances')->where('user', $receiver)->first()->balance;
		$in['objectUUID'] = Robust::$NULL_KEY;
		$in['objectName'] = $desc;
		if ($region = Robust::tbl('regions')->where('uuid', $regionUUID)->first()) {
			$in['regionHandle'] = $region->regionHandle;
		}else{
			$in['regionHandle'] = "";
		}
		$in['regionUUID'] = $regionUUID;
		$in['type'] = 5001;
		$in['time'] = time();
		$in['secure'] = uuid();
		$in['status'] = 0;
		$in['description'] = $desc;
		$in['commonName'] = "";
		self::tbl('transactions')->insert($in);
	}
}
