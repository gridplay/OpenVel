<?php
namespace App\Models;
use DB;
class Tier extends Core {
    protected $table = 'tier';
    public $incrementing = true;
    public $timestamps = false;
}