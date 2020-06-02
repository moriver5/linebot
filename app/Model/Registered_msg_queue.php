<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Registered_msg_queue extends Model
{
	protected $fillable = [
		'line_push_id',
		'user_line_id',
		'created_at',
		'updated_at'
	];
}
