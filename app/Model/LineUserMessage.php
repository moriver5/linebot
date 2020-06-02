<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LineUserMessage extends Model
{
	protected $fillable = [
		'id', 
		'line_basic_id', 
		'user_line_id',
		'act_flg',
		'reply_token',
		'msg',
		'created_at',
		'updated_at'
	];
}
