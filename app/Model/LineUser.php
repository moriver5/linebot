<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LineUser extends Model
{
	protected $fillable = [
		'id', 
		'line_basic_id', 
		'user_line_id',
		'follow_flg',
		'block24h',
		'disable',
		'ad_cd',
		'access_date',
		'created_at',
		'updated_at'
	];
}
