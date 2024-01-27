<?php
namespace App\Models;
use DB;
class Proposals extends Core {
	protected $table = 'proposals';
	public $incrementing = false;
	public $timestamps = false;
	public static function createproposal($asking, $to) {
		$id = uuid();
		self::insert(['id' => $id,
			'asker' => $asking,
			'receiver' => $to,
			'posted' => time()
		]);
		return $id;
	}
}