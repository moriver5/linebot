<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Registered_mail extends Model
{
	protected $fillable = [
		'id',
		'specified_time',
		'enable_flg',
		'item_type',
		'item_value',
		'like_type',
		'groups',
		'device',
		'title',
		'body',
		'html_body',
		'remarks',
		'created_at',
		'updated_at',
	];
}
