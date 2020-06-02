<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TrackImageLog extends Model
{
	protected $fillable = [
		'id',
		'env_data',
		'script_name',
		'line_basic_id',
		'user_line_id',
		'access_ip',
		'access_ua',
		'image_unique',
		'created_at',
		'updated_at'
	];
}
