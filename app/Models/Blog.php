<?php
namespace App\Models;
use DB;
class Blog extends Core {
	protected $table = 'blog';
	public $incrementing = true;
	public $timestamps = false;
}