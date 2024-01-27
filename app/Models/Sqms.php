<?php
namespace App\Models;
use DB;
use App\Models\User;
use App\Models\Robust;
class Sqms extends Core {
	protected $table = 'sqms';
	public $incrementing = false;
	public $timestamps = false;
	public static $servers = ["http://moosejaw1.canadiangrid.ca:8088/sqm.php"];
	public static function countsqms($uuid) {
		$sq = self::where('uuid', $uuid)->get();
		$sqms = 0;
		foreach($sq as $s) {
			$sqms += $s->sqm;
		}
		return $sqms;
	}
	public static function cronSqms() {
		foreach (self::$servers as $s) {
			$rep = parent::senddata('POST', $s, [], []);
			if (is_array($rep)) {
				$ret = $rep;
			}else{
				$ret = json_decode($rep, true);
			}
			if (is_array($ret) && array_key_exists('sqm', $ret)) {
				$result = $ret['sqm'];
				foreach($result as $squrt) {
					$sqm = $squrt['sqm'];
					$uuid = $squrt['uuid'];
					$pid = $squrt['pid'];
					if (self::where('pid', $pid)->first()) {
						self::where('pid', $pid)->update(['uuid' => $uuid, 'sqm' => $sqm, 
							'parcel' => $squrt['parcel'], 'checked' => time()]);
					}else{
						self::insert(['pid' => $pid, 'uuid' => $uuid, 
							'region' => $squrt['region'], 'parcel' => $squrt['parcel'],
							'sqm' => $sqm, 'checked' => time()]);
					}
				}
			}
		}
		// delete if 2 or more hours old of not being checked
		self::where('checked', '<', (time() - 7200))->delete();
	}
}