<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LineOfficialAccount extends Model
{
	protected $fillable = [
		'id', 
		'line_basic_id', 
		'line_channel_id',
		'line_channel_secret',
		'line_token',
		'name',
		'memo',
		'qrcode',
		'created_at',
		'updated_at'
	];
}
