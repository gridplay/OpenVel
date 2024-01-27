<?php
namespace App\Models;
use DB;
class Reports extends Core {
	protected $table = 'abusereports';
	public $incrementing = false;
	public $timestamps = false;
	public static function savereport() {
		$d = request()->only(['reporter', 'abuser', 'img', 'summary', 'details']);
		self::insert([
			'id' => uuid(),
			'reporter' => $d['reporter'],
			'abuser' => $d['abuser'],
			'img' => $d['img'],
			'summary' => $d['summary'],
			'details' => $d['details'],
			'posted' => time()
		]);
	}
}