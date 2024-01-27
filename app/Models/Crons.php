<?php
namespace App\Models;
use DB;
use Log;
use App\Models\Core;
use App\Models\Robust;
use App\Models\Sqms;
class Crons extends Core {
	public static function everyMinute() {
		DB::table('email_reset')->where('expires', '<', time())->delete();
	}
	public static function everyFiveMinutes() {
		Sqms::cronSqms();
	}
	public static function hourly() {
		$hourago = time() - 3600;
	}
	public static function daily() {
		$time = time();
		$dayago = $time - 86400;
		$twodaysago = $time - 172800;
		$weekago = $time - 604800;
		$monthago = $time - 2419200;
	}
	public static function monthly() {
	}
}
