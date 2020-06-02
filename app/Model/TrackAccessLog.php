<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class TrackAccessLog extends Model
{
	protected $fillable = [
		'id',
		'env_data',
		'script_name',
		'line_basic_id',
		'user_line_id',
		'csrf_token',
		'xuid',
		'asp_id',
		'ad_cd',
		'access_ip',
		'access_ua',
		'access_referrer',
		'access_tag',
		'status',
		'image_unique',
		'access_date',
		'created_at',
		'updated_at'
	];
}
