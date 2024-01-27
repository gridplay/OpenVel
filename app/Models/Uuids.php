<?php
namespace App\Models;
use App\Models\Uuid;
trait Uuids {
	protected static function boot() {
		parent::boot();
		static::creating(function ($model) {
            $model->{$model->getKeyName()} = Uuid::generate();
        });
	}
}