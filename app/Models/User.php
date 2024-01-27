<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use DB;
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Uuids;
    protected $table = 'users';
    public $incrementing = false;
    protected $fillable = ['id','firstname','lastname','uuid','email','password','tos',];
    protected $hidden = ['password','remember_token',];
    protected $casts = ['email_verified_at' => 'datetime','password' => 'hashed',];
    public static function findbyuuid($uuid) {
        if ($u = self::where('uuid', $uuid)->first()) {
            return $u;
        }
        return null;
    }
}
